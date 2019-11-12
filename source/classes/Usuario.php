<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Classe Usuario, responsável por administrar os usuáarios
* @copyright GPL © 2007
* @package publicare
*
* MCTI - Ministério da Ciência, Tecnologia e Inovação - www.mcti.gov.br
* ANTT - Agência Nacional de Transportes Terrestres - www.antt.gov.br
* EPL - Empresa de Planejamento e Logística - www.epl.gov.br
* *
*
* Este arquivo é parte do programa Publicare
* Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
* como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
* Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
* de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
* Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
*/

/**
 * Classe responsável por gerenciar usuários e permissões
 */
class Usuario
{
	public $perfil = array (
			_PERFIL_ADMINISTRADOR => 'Administrador',
			_PERFIL_EDITOR => 'Editor',
			_PERFIL_AUTOR => 'Autor',
			_PERFIL_RESTRITO => 'Restrito',
                        _PERFIL_MILITARIZADO => 'Militarizado',
                        _PERFIL_DEFAULT => 'Default'
			);
        
	public $cod_objeto;
	public $usuario;
	public $browser_ver;
	public $is_explorer;
	public $action;
	public $inicio_secao;
	
	/**
	 * Metodo construtor da classe usuario
	 * @param Page $_page - Referencia objeto page
	 */
	function __construct(&$_page)
	{
		if (!isset($_SESSION['perfil']) || !is_array($_SESSION['perfil']) 
			|| count($_SESSION['perfil'])<1) $this->CarregarInfoPerfis($_page);
//			xd($_SESSION['usuario'])
		if (isset($_SESSION['usuario']['cod_usuario'])) $this->PegaPerfil($_page);
		else
		{
			$this->cod_perfil = _PERFIL_DEFAULT;
                        if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) $_SESSION['usuario'] = array();
//                        xd($_SESSION['usuario']);
			$_SESSION['usuario']['perfil'] = _PERFIL_DEFAULT;
		}
	}
        
        function pegaNomePerfil($cod)
        {
            return $this->perfil[$cod];
        }
        
	/**
	 * Busca todos os usuários ativos no banco de dados
	 * @param Page $_page - Referencia objeto Page
	 * @param Int $oculta_root - Indica se eve ocultar usuario root da lista
	 * @return array
	 */
	function listaUsuarios(&$_page, $oculta_root=0)
	{
		 $sql = "SELECT t1.cod_usuario, t1.nome, t1.email, t1.chefia, t1.secao, t1.ramal, "
				 . "t1.login, t1.data_atualizacao, t1.altera_senha, t1.ldap, "
				 . "t2.nome as nome_chefia "
				 . "FROM usuario t1 "
				 . "LEFT JOIN usuario t2 ON t1.chefia = t2.cod_usuario "
				 . "WHERE t1.valido=1 ".($oculta_root==1?"AND t1.cod_usuario>1 ":"")
				 . "ORDER BY nome;";
		 $rs = $_page->_db->ExecSQL($sql);
		 return $rs->getAll();
	}
		
	function EstaLogado($nivelInferior=NULL)
	{
		//echo ">>>".$_SESSION['usuario']['perfil'];
		if ($nivelInferior && isset($_SESSION['usuario']['perfil'])) {
			if ($_SESSION['usuario']['perfil'] > $nivelInferior)
				return null;
			else 
				return is_array($_SESSION['usuario']);
		}
		else 
			return (isset($_SESSION['usuario']) && is_array($_SESSION['usuario']));
	}

	function EstaLogadoMilitarizado()
	{
		if (($this->EstaLogado(_PERFIL_EDITOR)) || ($_SESSION['usuario']['perfil'] == _PERFIL_MILITARIZADO))
			return is_array($_SESSION['usuario']);
		else 
			return null;
	}

        /**
         * Realiza login de usuários, validando no banco de dados e no ad/ldap quando
         * for o caso
         * @param Page $_page - Referencia do objeto page
         * @param string $usuario - Login do usuário
         * @param string $senha - Senha do usuario
         * @return boolean
         */
	function Login(&$_page, $usuario, $senha)
	{
//            $usuario = htmlspecialchars($usuario, ENT_QUOTES, "UTF-8");
//            $senha = htmlspecialchars($senha, ENT_QUOTES, "UTF-8");
            
            $sql = $_page->_db->getCon()->prepare("SELECT cod_usuario, nome, "
                    . "email, chefia, secao, ramal, login, data_atualizacao, "
                    . "altera_senha, ldap "
                    . "from usuario "
                    . "where valido=1 and login=?");
            $bind = array(1 => $usuario);

            $rs = $_page->_db->ExecSQL(array($sql, $bind));
            
            // encontrou usuário com o login
            if ($rs->_numOfRows>0)
            {
                // muito tempo de acessar, login fica bloqueado
                if((int)$rs->fields['data_atualizacao'] < (int)date("Ymd"))
                {
                    return false;
                }
                // login é válido
                else
                {
                    // verifica se é login no ldap
                    // caso seja ldap, verifica senha com base ad/ldap
                    if (defined("_ldaphost") && $rs->fields['ldap']==1)
                    {
                        $resource = ldap_connect(_ldaphost, _ldapport);
                        ldap_set_option($resource, LDAP_OPT_PROTOCOL_VERSION, _ldapversion);
                        $bind = ldap_bind($resource, _ldapdomain."\\".$usuario, $senha);
                        if (!$bind) return false;
                    }
                    // caso contrário, verifica senha no banco
                    else
                    {
                        $sql2 = $_page->_db->getCon()->prepare("SELECT cod_usuario, "
                                . "nome, email, chefia, secao, ramal, login, "
                                . "data_atualizacao, altera_senha, ldap "
                                . "FROM usuario "
                                . "WHERE valido = 1 AND cod_usuario = ? AND senha = ?");
                        $bind2 = array(1 => $rs->fields['cod_usuario'], 2 => md5($senha));
                        $rs2 = $_page->_db->ExecSQL(array($sql2, $bind2));
                        if ($rs2->_numOfRows == 0) return false;
                    }
                    
                    // popula sessao de usuario
                    $_SESSION["usuario"] = $rs->fields;
                    
//                    xd($_SESSION["usuario"]);
                    
                    // atualiza data validade do usuario
                    $data_validade = strftime("%Y%m%d", strtotime("+6 month"));
                    $sql = "UPDATE usuario "
                            . "SET data_atualizacao = ".ConverteData($data_validade,16)." "
                            . "WHERE cod_usuario = ".$_SESSION["usuario"]["cod_usuario"];
                    $rs2 = $_page->_db->ExecSQL($sql);
                    
                    // carrega permissões do usuario
                    $this->Carregar($_page);
                    return true;
                }
            }

            $_SESSION['usuario'] = "";
            return false;
	}
		
	function Logout()
	{
		$cod_objeto=_ROOT;
		$_SESSION['usuario'] = "";
		$_SESSION['perfil'] = "";
	}
		
	function Carregar(&$_page)
	{
		$_SESSION['usuario']['direitos'] = $this->PegaDireitosDoUsuario($_page, $_SESSION['usuario']['cod_usuario']);
		$this->CarregarInfoPerfis($_page);
		$this->PegaPerfil($_page);
	}

//	function CarregarDireitos(&$_page)
//	{
//		$sql = "select cod_objeto, 
//		cod_perfil 
//		from 
//		usuarioxobjetoxperfil 
//		where 
//		cod_usuario=".$_SESSION['usuario']['cod_usuario'];
//		$rs = $_page->_db->ExecSQL($sql);
//		while ($row = $rs->FetchRow()) {
//			$_SESSION['usuario']['direitos'][$row['cod_objeto']] = $row['cod_perfil'];
//		}
//                
//                $sql = "select cod_objeto, 
//                cod_perfil 
//                from usuarioxobjetoxperfil 
//                where cod_usuario=$interCod_Usuario";
//                $rs = $_page->_db->ExecSQL($sql);
//                if ($rs->_numOfRows>0){
//                    while (!$rs->EOF){
//                        $out[$rs->fields['cod_objeto']]=$rs->fields['cod_perfil'];
//                        $rs->MoveNext();
//                    }
//                }
//                return $out;
//                
//	}

	function CarregarInfoPerfis(&$_page)
	{
		$sql = "select cod_perfil,
		acao,
		script,
		donooupublicado,
		sopublicado,
		sodono,
		naomenu,
		ordem,
                icone
		from 
		infoperfil 
		order by ordem";

		$_SESSION['perfil']=array();

		$rs = $_page->_db->ExecSQL($sql);
		while ($row = $rs->FetchRow()){
			$_SESSION['perfil'][$row["cod_perfil"]][] = $row;
		}
                
		for ($f = 1; $f < count($_SESSION['perfil']); $f++)
		{
			$_SESSION['perfil'][$f] = array_merge($_SESSION['perfil'][$f], $_SESSION['perfil'][_PERFIL_DEFAULT]);
		}
                
//                echo "<!-- ";
//                x($_SESSION);
//                echo " -->";
		
	}
		
	function PegaPerfil(&$_page, $cod_objeto=0)
	{
		if ($cod_objeto==0 && !$_page->_objeto->Valor($_page, 'cod_objeto')) return false;
                if ($cod_objeto==0) $cod_objeto = $_page->_objeto->Valor($_page, 'cod_objeto');
		$caminho[] = $cod_objeto;
                $objeto = new Objeto($_page, $cod_objeto);
		$caminho = array_merge($caminho, array_reverse($objeto->CaminhoObjeto));
		foreach ($caminho as $cod_obj)
		{
			if (isset($_SESSION['usuario']['direitos'][$cod_obj]))
			{
				$this->cod_perfil = $_SESSION['usuario']['direitos'][$cod_obj];
				$_SESSION['usuario']['perfil'] = $this->cod_perfil;
				return $this->cod_perfil;
			}
		}
		$this->cod_perfil=0;
		return _PERFIL_DEFAULT;
	}
		
	function PodeExecutar(&$_page, $script)
	{
		//Administrador Pode Tudo
		if ($this->cod_perfil==_PERFIL_ADMINISTRADOR)
		{
			switch($script){
				case '/do/delete':
                                    // Ou melhor, quase tudo... nem admin apaga objeto do sistema
                                    if ($_page->_objeto->Valor($_page, "objetosistema"))
                                    {
                                            return false;
                                    }
                                    break;
			} // switch
			return true;
		}
			
		if (is_array($_SESSION['perfil'][$this->cod_perfil]))
		{
			foreach ($_SESSION['perfil'][$this->cod_perfil] as $perfil)
			{
				if (!$perfil['script']) continue;
				$preg = "%".$perfil['script']."%is";
				
				if (preg_match($preg, $script))
				{
					if ($perfil['donooupublicado'])
					{
						
						//Testar se o usuario e dono do objeto ou se o objeto esta publicado
						if (!($_page->_objeto->metadados['cod_usuario']==$_SESSION['usuario']['cod_usuario']) && !($_page->_objeto->Publicado())){
							return false;
						}
					}
					if ($perfil['sopublicado'])
					{
						//Testar se o objeto esta publicado
						if (!$_page->_objeto->Publicado())
						{
							return false;
						}
					}
					if ($perfil['sodono'])
					{
						//Testar se o usuario e dono do objeto
						if ($_page->_objeto->metadados['cod_usuario']!=$_SESSION['usuario']['cod_usuario'])
						{
							return false;
						}
					}
					return true;
				}
			}
		}
		return false;
	}

	function ContaPendencias(&$_page)
	{
		//$sql = 'select count(*) as contador from pendencia where cod_usuario='.$_SESSION['usuario']["cod_usuario"];
		$sql = "select count(*) as contador from pendencia";
		$rs = $_page->_db->ExecSQL($sql);
		$row = $rs->fields;
		return $row['contador'];
	}

	function ContaRejeitados(&$_page)
	{
		$sql = "select count(*) as contador 
		from objeto 
		where cod_usuario=".$_SESSION['usuario']["cod_usuario"]. " 
		and cod_status="._STATUS_REJEITADO." 
		and apagado=0";
		$rs = $_page->_db->ExecSQL($sql);
		$row = $rs->fields;
		return $row['contador'];
	}

	function Menu(&$_page)
	{
            
            $retorno = array();
            
            foreach ($_SESSION['perfil'][$this->cod_perfil] as $perfil)
            {
                if ($perfil["naomenu"] == 0)
                {
                    $adiciona = true;
                    if ($perfil['donooupublicado'] == 1)
                    {
                        if ($_page->_objeto->metadados['cod_usuario'] != $_SESSION['usuario']['cod_usuario'] 
                                && !$_page->_objeto->Publicado()) 
                            $adiciona = false;
                    }
                    if ($perfil['sopublicado'] == 1)
                    {
                        if (!$_page->_objeto->Publicado()) $adiciona = false;
                    }
                    if ($perfil['sodono'] == 1)
                    {
                        if ($_page->_objeto->metadados['cod_usuario'] != $_SESSION['usuario']['cod_usuario'])
                           $adiciona = false; 
                    }
                    $perfil["script"] = preg_replace("|[.*?]|is", "", $perfil['script']);
                    if ($adiciona === true) $retorno[] = $perfil;
                }
            }
            
            usort($retorno, function($a, $b) {
                return $a["ordem"] > $b["ordem"];
            });
            
            $retorno = $this->Filtrar($_page, $retorno);
            
            return $retorno;
	}

	function PodeApagar(&$_page)
	{
		if (!is_array ($this->scripts))
			$this->Menu($_page);
		if (in_array('/do/delete',$this->scripts))
			return true;
		else
			return false;
	}
		
	function Filtrar (&$_page, $acao)
	{
		foreach ($acao as $item)
		{
			switch ($item['script'])
			{
				case '/manage/create':
					if ($_page->_objeto->PodeTerFilhos())
					{
						$out[]=$item;
					}
					break;
				case '/login/index':
					break;
				case '/do/recuperar_objeto':
					if ($_page->_objeto->Valor($_page, "apagado"))
						$out[]=$item;
					break;	
				case '/do/delete':
					if (($_page->_objeto->Valor($_page, "cod_objeto")!=_ROOT) && (!$_page->_objeto->Valor($_page, "apagado")))
						$out[]=$item;
					break;
				case '/manage/new':
					if ($_page->_objeto->Valor($_page, "temfilhos"))
						$out[]=$item;
					break;
				case '/do/publicar':
					if ($_page->_objeto->Valor($_page, "cod_status")!=_STATUS_PUBLICADO)
						$out[]=$item;
					break;
				case '/do/rejeitar':
					if ($_page->_objeto->Valor($_page, 'cod_objeto')!=_ROOT)
					{
					 	if (($_page->_objeto->Valor($_page, "cod_status")==_STATUS_SUBMETIDO) || ($_page->_objeto->Valor($_page, "cod_status")==_STATUS_PUBLICADO))
							$out[]=$item;
					}
					break;
				case '/do/submeter':
					if (($_page->_objeto->Valor($_page, "cod_status")==_STATUS_PRIVADO) || ($_page->_objeto->Valor($_page, "cod_status")==_STATUS_REJEITADO))
						$out[]=$item;
					break;
				case '/do/pendentes':
					$conta=$this->ContaPendencias($_page);
					if ($conta)
					{
						//$item['acao']=$conta .' objeto(s) para aprova��o';
						$item['acao'] = 'Objetos para aprova&ccedil;&atilde;o';
						$out[]=$item;
					}
					break;
				case '/do/rejeitados':
					$conta=$this->ContaRejeitados($_page);
					if ($conta)
					{
						$item['acao']=$conta.' objeto(s) para revis&atilde;o';
						$out[]=$item;
					}
					break;
				default:
					$out[]=$item;
			}
		}
			return $out;
	}
      
    /**
     * Retorna o nome do perfil conforme o código
     * @param int $tmpNum - codigo do perfil
     * @return string
     */
    static function VerificaPerfil($tmpNum)
    {
        switch ($tmpNum) {
        case _PERFIL_ADMINISTRADOR:
            $tmpPerfil = "Administrador";
            break;
        case _PERFIL_EDITOR:
            $tmpPerfil = "Editor";	
            break;
        case _PERFIL_AUTOR:
            $tmpPerfil = "Autor";
            break;
        case _PERFIL_RESTRITO:
            $tmpPerfil = "Restrito";
            break;
        case _PERFIL_MILITARIZADO:
            $tmpPerfil = "Militarizado";
            break;
        case _PERFIL_DEFAULT:
            $tmpPerfil = "Nenhum";
            break;
        default:
            $tmpPerfil = "!incoerencia";
            break;
        }
        return $tmpPerfil;
    }
    
    /**
     * Atualiza dados do usuário no banco de dados
     * @param Page $_page - Referencia do objeto page
     * @param array $dados - dados do usuário
     */
    function atualizaUsuario(&$_page, $dados)
    {
        $sql = "update usuario set nome='" . $dados['nome'] . "', "
                . "email='" . $dados['email'] . "', "
                . "secao='" . $dados["secao"] . "', "
                . "ramal='" . $dados['ramal'] . "', "
                . "login='" . $dados['login'] . "', "
                . "altera_senha=" . $dados['altera_senha'] . ", "
                . "ldap=" . $dados['ldap'] . ", "
                . "data_atualizacao=" . ConverteData($dados['data_atualizacao'], 16) . ", ";
        if ($dados['senha']!="") $sql .= "senha='" . md5($dados['senha']) . "', ";
        $sql .= "chefia=" . $dados['chefia'] . " "
                . "where cod_usuario=" . $dados['cod_usuario'];
        $_page->_db->ExecSQL($sql);
    }
    
    /**
     * Altera/exclui perfil de usuario em determinado objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_usuario - Codigo do usuario
     * @param int $cod_objeto - Codigo do objeto
     * @param int $perfil - Perfil a ser adicionado
     * @param bool $inserir - Indica se deve inserir novo perfil
     */
    function AlterarPerfilDoUsuarioNoObjeto(&$_page, $cod_usuario, $cod_objeto, $perfil, $inserir=true)
    {
        $sql = "delete from usuarioxobjetoxperfil "
			."where cod_objeto=".$cod_objeto." and cod_usuario=".$cod_usuario;
        $_page->_db->ExecSQL($sql);
        if ($inserir)
        {
            $sql = "insert into usuarioxobjetoxperfil "
				."(cod_usuario,cod_objeto,cod_perfil) "
				."values(".$cod_usuario.",".$cod_objeto.",".$perfil.")";
            $_page->_db->ExecSQL($sql);
        }
    }
    
    /**
     * Verifica se ja existe usuario com mesmo login
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $login - Login a ser verificado
     * @param int $cod_usuario - Codigo do usuario, caso seja update
     * @return bool
     */
    function ExisteOutroUsuario(&$_page, $login, $cod_usuario)
    {
        $sql = "select cod_usuario "
			."from usuario "
			."where login='".$login."' and valido<>0";
        if ($cod_usuario) $sql .=" and cod_usuario<>".$cod_usuario." ";
        $rs = $_page->_db->ExecSQL($sql);
        return !$rs->EOF;
    }
    
    /**
     * Busca lista de usuarios no banco de dados
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $secao - seção do usuario
     * @return array - Lista de usuários
     */
    function PegaListaDeUsuarios(&$_page, $secao=NULL)
    {
        if (!$secao)
                $sql = "select cod_usuario as codigo,nome as texto, chefia as intchefia from usuario where valido=1 order by  nome, secao";
        else 
                $sql = "select cod_usuario as codigo,nome as texto, chefia as intchefia from usuario where valido=1 and secao = '".$secao."' order by nome, secao";
        $rs = $_page->_db->ExecSQL($sql);
        return $rs->GetRows();
    }
    
    /**
     * Remove todas as entradas no banco de perfis de usuario
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_usuario - Codigo do usuario
     */
    function limpaPerfisUsuario(&$_page, $cod_usuario)
    {
        $sql = "delete from usuarioxobjetoxperfil where cod_usuario = " . $cod_usuario;
        $_page->_db->ExecSQL($sql);
    }
    
    /**
     * Busca informações de determinado usuario
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_usuario - Codigo do usuario a buscar
     * @return array - Dados do usuario
     */
    function PegaInformacaoUsuario(&$_page, $cod_usuario)
    {
        $sql = "select cod_usuario, "
                . "nome, "
                . "email, "
                . "login, "
                . "chefia, "
                . "secao, "
                . "ramal, "
                . "altera_senha, "
                . "ldap, "
                . "data_atualizacao "
                . "from usuario "
                . "where cod_usuario = ".$cod_usuario;
        $rs = $_page->_db->ExecSQL($sql);
        return $rs->fields;
    }
    
    /**
     * Busca todos os direitos que usuario tem no portal
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $interCod_Usuario - Codigo do usuario
     * @return array
     */
    function PegaDireitosDoUsuario(&$_page, $interCod_Usuario)
    {
        $sql = "SELECT cod_objeto, "
                . "cod_perfil "
                . "FROM usuarioxobjetoxperfil "
                . "WHERE cod_usuario = ".$interCod_Usuario." "
                . "ORDER BY cod_objeto";
        $rs = $_page->_db->ExecSQL($sql);
        if ($rs->_numOfRows>0){
            while ($row = $rs->FetchRow()){
                $out[$row['cod_objeto']] = $row['cod_perfil'];
            }
        }
        return $out;
    }
    
    /**
     * Bloqueia usuário no banco de dados
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod - Código do usuário
     */
    function bloquearUsuario(&$_page, $cod)
    {
        $sql = "UPDATE usuario "
                . "SET valido = 0 "
                . "WHERE cod_usuario = " . $cod;
        $_page->_db->ExecSQL($sql);
    }
		
}
?>