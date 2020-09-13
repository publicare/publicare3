<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @file Usuario.php
 * @description Classe responsável por gerenciar usuários da aplicação
 * @copyright MIT © 2020
 * @package publicare
 *
 * Este arquivo é parte do programa Publicare
 * 
 * Copyright (c) 2020 Publicare
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
*/

namespace Pbl\Core;

use Pbl\Core\Base;
use Pbl\Core\Objeto;

/**
 * Classe responsável por gerenciar usuários e permissões
 */
class Usuario extends Base
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
        
	// /**
	//  * Metodo construtor da classe usuario
	//  * @param Page $page - Referencia objeto page
	//  */
	// function __construct(&$page)
	// {
    //         $this->page = $page;
            
    //  
    // }
    
    /**
     * Inicializa objeto, definindo sessions necessarias a utilizacao
     */
    public function iniciar()
    {
        if (!isset($_SESSION['perfil']) || !is_array($_SESSION['perfil']) 
            || count($_SESSION['perfil'])<1) 
        {
            $this->carregarInfoPerfis();
        }
                
        if (isset($_SESSION['usuario']['cod_usuario'])) 
        {
            $this->pegarPerfil();
        }
        else
        {
            $this->cod_perfil = _PERFIL_DEFAULT;
            if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) $_SESSION['usuario'] = array();
            $_SESSION['usuario']['perfil'] = _PERFIL_DEFAULT;
        }
    }
        
        function pegarNomePerfil($cod)
        {
            return $this->perfil[$cod];
        }
        
	/**
	 * Busca todos os usuários ativos no banco de dados
	 * @param Int $oculta_root - Indica se eve ocultar usuario root da lista
	 * @return array
	 */
	function listarUsuarios($busca="", $ordem="", $dir="", $start=-1, $limit=-1, $oculta_root=1)
	{
            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS nome, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]." AS email, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." AS chefia, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." AS secao, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]." AS ramal, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." AS login, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." AS data_atualizacao, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["altera_senha"]." AS altera_senha, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ldap"]." AS ldap, "
                    . " t2.".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS nome_chefia "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"]." "
                    . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." t2 "
                        . " ON ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." = t2.".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 ";
            if ($busca != "")
            {
                $sql .= " AND (".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." like '%".$busca."%'"
                        . " OR ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." like '%".$busca."%' "
                        . " OR ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]." like '%".$busca."%' "
                        . " OR ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." like '%".$busca."%' "
                        . " OR ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]." like '%".$busca."%' "
                        . ") ";
            }
            $sql .= ($oculta_root==1?" AND ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." > 1 ":"");
            $sql .=" ORDER BY ".($ordem!=""?$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"][$ordem]:$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"])." ".$dir;
            
            $rs = $this->container["db"]->execSQL($sql, $start, $limit);
            return $rs->getRows();
	}
		
	function estaLogado($nivelInferior=NULL)
	{
        // x("usuario::estalogado - nivelInferior=".$nivelInferior);
        // x($_SESSION['usuario']);
		//echo ">>>".$_SESSION['usuario']['perfil'];
        if ($nivelInferior && isset($_SESSION['usuario']['perfil']))
        {
            if ($_SESSION['usuario']['perfil'] > $nivelInferior)
                return null;
            else 
                return is_array($_SESSION['usuario']);
        }
        else 
        {
            return (isset($_SESSION['usuario']["cod_usuario"]));
        }
	}

	function estaLogadoMilitarizado()
	{
            if (($this->estaLogado(_PERFIL_EDITOR)) || ($_SESSION['usuario']['perfil'] == _PERFIL_MILITARIZADO))
                return is_array($_SESSION['usuario']);
            else 
                return null;
	}

        /**
         * Realiza login de usuários, validando no banco de dados e no ad/ldap quando
         * for o caso
         * @param Page $page - Referencia do objeto page
         * @param string $usuario - Login do usuário
         * @param string $senha - Senha do usuario
         * @return boolean
         */
	function login($usuario, $senha)
	{
            
            $sql = "";
            $bind = array();

            if ($this->container["config"]->bd["tipo"]=="oracle11")
            {
                $sql = $this->container["db_con"]->getCon()->prepare("SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS nome, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]." AS email, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." AS chefia, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." AS secao, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]." AS ramal, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." AS login, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." AS data_atualizacao, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["altera_senha"]." AS altera_senha, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ldap"]." AS ldap "
                        . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                        . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 "
                        . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." = :login");
                $bind = array("login" => $usuario);
            }
            else
            {
                $sql = $this->container["db_con"]->getCon()->prepare("SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS nome, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]." AS email, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." AS chefia, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." AS secao, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]." AS ramal, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." AS login, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." AS data_atualizacao, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["altera_senha"]." AS altera_senha, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ldap"]." AS ldap "
                        . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                        . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 "
                        . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." = ?");
                $bind = array(1 => $usuario);
            }
            
            $rs = $this->container["db"]->execSQL(array($sql, $bind));
            
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
                    if ((isset($this->container["config"]->login["ldap"]) && $this->container["config"]->login["ldap"] === true) && $rs->fields['ldap']==1)
                    {
                        $resource = ldap_connect($this->container["config"]->login["ldaphost"], $this->container["config"]->login["ldapporta"]);
                        ldap_set_option($resource, LDAP_OPT_PROTOCOL_VERSION, $this->container["config"]->login["ldapversao"]);
                        $bind = ldap_bind($resource, $this->container["config"]->login["ldapdominio"]."\\".$usuario, $senha);
                        if (!$bind) return false;
                    }
                    // caso contrário, verifica senha no banco
                    else
                    {
                        $sql2 = "";
                        $bind2 = array();
                        if ($this->container["config"]->bd["tipo"]=="oracle11")
                        {
                            $sql2 = $this->container["db_con"]->getCon()->prepare("SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS nome, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]." AS email, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." AS chefia, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." AS secao, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]." AS ramal, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." AS login, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." AS data_atualizacao, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["altera_senha"]." AS altera_senha, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ldap"]." AS ldap "
                                    . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                                    . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 "
                                    . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = :login "
                                    . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["senha"]." = :senha");
                        
                            $bind2 = array("login" => $rs->fields['cod_usuario'], "senha" => md5($senha));
                        }
                        else
                        {
                            $sql2 = $this->container["db_con"]->getCon()->prepare("SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS nome, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]." AS email, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." AS chefia, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." AS secao, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]." AS ramal, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." AS login, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." AS data_atualizacao, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["altera_senha"]." AS altera_senha, "
                                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ldap"]." AS ldap "
                                    . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                                    . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 "
                                    . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = ? "
                                    . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["senha"]." = ?");
                        
                            $bind2 = array(1 => $rs->fields['cod_usuario'], 2 => md5($senha));
                        }
                        $rs2 = $this->container["db"]->execSQL(array($sql2, $bind2));
                        if ($rs2->_numOfRows == 0) return false;
                    }
                    
                    // popula sessao de usuario
                    $_SESSION["usuario"] = $rs->fields;
                    
                    // atualiza data validade do usuario
                    $data_validade = strftime("%Y%m%d", strtotime("+6 month"));
                    $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                            . " SET ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." = ".ConverteData($data_validade,16)." "
                            . "WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = ".$_SESSION["usuario"]["cod_usuario"];
                    $rs2 = $this->container["db"]->execSQL($sql);

                    // carrega permissões do usuario
                    $this->carregar();
                    return true;
                }
            }

            $_SESSION['usuario'] = "";
            return false;
	}
        
        function loginAutoPass($usuario)
	{
//            $usuario = htmlspecialchars($usuario, ENT_QUOTES, "UTF-8");
//            $senha = htmlspecialchars($senha, ENT_QUOTES, "UTF-8");
            
            $sql = "";
            $bind = array();

            if ($this->container["config"]->bd["tipo"]=="oracle11")
            {
                $sql = $this->container["db_con"]->getCon()->prepare("SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS nome, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]." AS email, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." AS chefia, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." AS secao, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]." AS ramal, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." AS login, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." AS data_atualizacao, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["altera_senha"]." AS altera_senha, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ldap"]." AS ldap "
                        . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                        . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 "
                        . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." = :login");
                $bind = array("login" => $usuario);
            }
            else
            {
                $sql = $this->container["db_con"]->getCon()->prepare("SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS nome, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]." AS email, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." AS chefia, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." AS secao, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]." AS ramal, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." AS login, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." AS data_atualizacao, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["altera_senha"]." AS altera_senha, "
                        . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ldap"]." AS ldap "
                        . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                        . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 "
                        . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." = ?");
                $bind = array(1 => $usuario);
            }
            
            $rs = $this->container["db"]->execSQL(array($sql, $bind));
            
            // encontrou usuário com o login
            if ($rs->_numOfRows>0)
            {
                // popula sessao de usuario
                $_SESSION["usuario"] = $rs->fields;
                
                // atualiza data validade do usuario
                $data_validade = strftime("%Y%m%d", strtotime("+6 month"));
                $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                        . " SET ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." = ".ConverteData($data_validade,16)." "
                        . "WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = ".$_SESSION["usuario"]["cod_usuario"];
                $rs2 = $this->container["db"]->execSQL($sql);

                // carrega permissões do usuario
                $this->carregar();
                return true;
            }

            $_SESSION['usuario'] = "";
            return false;
	}
		
	function logout()
	{
//            xd();
            $cod_objeto = $this->container["config"]->portal["objroot"];
            $_SESSION['usuario'] = "";
            $_SESSION['perfil'] = "";
            
            $this->cod_perfil = _PERFIL_DEFAULT;
            $_SESSION['usuario'] = array();
            $_SESSION['usuario']['perfil'] = _PERFIL_DEFAULT;
	}
		
	function carregar()
	{
        $_SESSION['usuario']['direitos'] = $this->pegarDireitosUsuario($_SESSION['usuario']['cod_usuario']);
        $this->carregarInfoPerfis();
        $this->pegarPerfil();
	}


	function carregarInfoPerfis()
	{
        $tblInfo = $this->container["config"]->bd["tabelas"]["infoperfil"];
		$sql = "SELECT ".$tblInfo["nick"].".".$tblInfo["colunas"]["cod_perfil"]." AS cod_perfil, "
            . " ".$tblInfo["nick"].".".$tblInfo["colunas"]["acao"]." AS acao, "
            . " ".$tblInfo["nick"].".".$tblInfo["colunas"]["script"]." AS script, "
            . " ".$tblInfo["nick"].".".$tblInfo["colunas"]["donooupublicado"]." AS donooupublicado, "
            . " ".$tblInfo["nick"].".".$tblInfo["colunas"]["sopublicado"]." AS sopublicado, "
            . " ".$tblInfo["nick"].".".$tblInfo["colunas"]["sodono"]." AS sodono, "
            . " ".$tblInfo["nick"].".".$tblInfo["colunas"]["naomenu"]." AS naomenu, "
            . " ".$tblInfo["nick"].".".$tblInfo["colunas"]["ordem"]." AS ordem, "
            . " ".$tblInfo["nick"].".".$tblInfo["colunas"]["icone"]." AS icone "
            . " FROM ".$tblInfo["nome"]." ".$tblInfo["nick"]." "
            . " ORDER BY ".$tblInfo["nick"].".".$tblInfo["colunas"]["ordem"];

		$_SESSION['perfil'] = array();
		$rs = $this->container["db"]->execSQL($sql);
		while ($row = $rs->FetchRow()){
			$_SESSION['perfil'][$row["cod_perfil"]][] = $row;
		}
                
		for ($f = 1; $f < count($_SESSION['perfil']); $f++)
		{
			$_SESSION['perfil'][$f] = array_merge($_SESSION['perfil'][$f], $_SESSION['perfil'][_PERFIL_DEFAULT]);
		}
	}
		
	function pegarPerfil($cod_objeto=0)
	{
        // xd($this->page->objeto->valor('cod_objeto'));
        // xd($this->container["objeto"]->valor("titulo"));
        if ($cod_objeto==0 && !$this->container["objeto"]->valor('cod_objeto')) return false;
        if ($cod_objeto==0) $cod_objeto = $this->container["objeto"]->valor('cod_objeto');
        $caminho[] = $cod_objeto;
        $objeto = new Objeto($this->container, $cod_objeto);
        $caminho = array_merge($caminho, array_reverse($objeto->caminhoObjeto));
        
            foreach ($caminho as $cod_obj)
            {
                $cod_obj = (int)$cod_obj;;
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
		
	function podeExecutar($script)
	{
            if (!isset($this->cod_perfil)) $this->cod_perfil = _PERFIL_DEFAULT;

            //Administrador Pode Tudo
            if ($this->cod_perfil == _PERFIL_ADMINISTRADOR)
            {
                switch($script){
                    case '/do/delete':
                        // Ou melhor, quase tudo... nem admin apaga objeto do sistema
                        if ($this->page->objeto->valor("objetosistema"))
                        {
                            return false;
                        }
                        break;
                } // switch
                return true;
            }
			
            if (isset($_SESSION['perfil'][$this->cod_perfil]) && is_array($_SESSION['perfil'][$this->cod_perfil]))
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
                            if (!($this->page->objeto->metadados['cod_usuario']==$_SESSION['usuario']['cod_usuario']) && !($this->page->objeto->publicado())){
                                    return false;
                            }
                        }
                        if ($perfil['sopublicado'])
                        {
                            //Testar se o objeto esta publicado
                            if (!$this->page->objeto->publicado())
                            {
                                    return false;
                            }
                        }
                        if ($perfil['sodono'])
                        {
                            //Testar se o usuario e dono do objeto
                            if ($this->page->objeto->metadados['cod_usuario']!=$_SESSION['usuario']['cod_usuario'])
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

	function contarPendencias()
	{
            //$sql = 'select count(*) as contador from pendencia where cod_usuario='.$_SESSION['usuario']["cod_usuario"];
            $sql = "SELECT COUNT(*) AS contador "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["pendencia"]["nome"];
            $rs = $this->container["db"]->execSQL($sql);
            $row = $rs->fields;
            return $row['contador'];
	}

	function contarRejeitados()
	{
            $sql = "SELECT COUNT(*) AS contador "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = ".$_SESSION['usuario']["cod_usuario"]." "
                    . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_status"]." = "._STATUS_REJEITADO." "
                    . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["apagado"]." = 0";
            $rs = $this->container["db"]->execSQL($sql);
            $row = $rs->fields;
            return $row['contador'];
	}

	function menu()
	{
            $retorno = array();
            
            foreach ($_SESSION['perfil'][$this->cod_perfil] as $perfil)
            {
                if ($perfil["naomenu"] == 0)
                {
                    $adiciona = true;
                    if ($perfil['donooupublicado'] == 1)
                    {
                        if ($this->page->objeto->metadados['cod_usuario'] != $_SESSION['usuario']['cod_usuario'] 
                                && !$this->page->objeto->publicado()) 
                            $adiciona = false;
                    }
                    if ($perfil['sopublicado'] == 1)
                    {
                        if (!$this->page->objeto->publicado()) $adiciona = false;
                    }
                    if ($perfil['sodono'] == 1)
                    {
                        if ($this->page->objeto->metadados['cod_usuario'] != $_SESSION['usuario']['cod_usuario'])
                           $adiciona = false; 
                    }
                    $perfil["script"] = preg_replace("|[.*?]|is", "", $perfil['script']);
                    if ($adiciona === true) $retorno[] = $perfil;
                }
            }
            
            usort($retorno, function($a, $b) {
                return $a["ordem"] > $b["ordem"];
            });
            
            $retorno = $this->filtrar($retorno);
            
            return $retorno;
	}
		
	function filtrar ($acao)
	{
		foreach ($acao as $item)
		{
			switch ($item['script'])
			{
				case '/do/create':
					if ($this->page->objeto->podeTerFilhos())
					{
						$out[]=$item;
					}
					break;
				case '/login/index':
					break;
				case '/do/recuperar_objeto':
					if ($this->page->objeto->valor("apagado"))
						$out[]=$item;
					break;	
				case '/do/delete':
					if (($this->page->objeto->valor("cod_objeto")!=$this->container["config"]->portal["objroot"]) && (!$this->page->objeto->valor("apagado")))
						$out[]=$item;
					break;
				case '/do/new':
					if ($this->page->objeto->valor("temfilhos"))
						$out[]=$item;
					break;
				case '/do/publicar':
					if ($this->page->objeto->valor("cod_status")!=_STATUS_PUBLICADO)
						$out[]=$item;
					break;
				case '/do/rejeitar':
					if ($this->page->objeto->valor('cod_objeto')!=$this->container["config"]->portal["objroot"])
					{
					 	if (($this->page->objeto->valor("cod_status")==_STATUS_SUBMETIDO) || ($this->page->objeto->valor("cod_status")==_STATUS_PUBLICADO))
							$out[]=$item;
					}
					break;
				case '/do/submeter':
					if (($this->page->objeto->valor("cod_status")==_STATUS_PRIVADO) || ($this->page->objeto->valor("cod_status")==_STATUS_REJEITADO))
						$out[]=$item;
					break;
				case '/do/pendentes':
					$conta=$this->contarPendencias();
					if ($conta)
					{
						//$item['acao']=$conta .' objeto(s) para aprova��o';
						$item['acao'] = 'Objetos para aprova&ccedil;&atilde;o';
						$out[]=$item;
					}
					break;
				case '/do/rejeitados':
					$conta=$this->contarRejeitados();
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
    static function verificarPerfil($tmpNum)
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
     * @param array $dados - dados do usuário
     */
    function atualizarUsuario($dados)
    {
        $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                . " SET ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." = '" . $dados['nome'] . "', "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]." = '" . $dados['email'] . "', "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." = '" . $dados["secao"] . "', "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]." = '" . $dados['ramal'] . "', "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." = '" . $dados['login'] . "', "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["altera_senha"]." = " . $dados['altera_senha'] . ", "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ldap"]." = " . $dados['ldap'] . ", "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." = ".ConverteData($dados['data_atualizacao'], 16).", ";
        if ($dados['senha']!="") 
        {
            $sql .= " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["senha"]." = '" . md5($dados['senha']) . "', ";
        }
        $sql .= " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." = " . $dados['chefia'] . " "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = " . $dados['cod_usuario'];
        $this->container["db"]->execSQL($sql);
    }
    
    /**
     * Altera/exclui perfil de usuario em determinado objeto
     * @param int $cod_usuario - Codigo do usuario
     * @param int $cod_objeto - Codigo do objeto
     * @param int $perfil - Perfil a ser adicionado
     * @param bool $inserir - Indica se deve inserir novo perfil
     */
    function alterarPerfilUsuarioObjeto($cod_usuario, $cod_objeto, $perfil, $inserir=true)
    {
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["colunas"]["cod_objeto"]." = ".$cod_objeto." "
                . " AND ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["colunas"]["cod_usuario"]." = ".$cod_usuario;
        $this->container["db"]->execSQL($sql);
        if ($inserir)
        {
            $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["nome"]." "
				."(".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["colunas"]["cod_usuario"].", ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["colunas"]["cod_objeto"].", ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["colunas"]["cod_perfil"].") "
				."values(".$cod_usuario.", ".$cod_objeto.", ".$perfil.")";
            $this->container["db"]->execSQL($sql);
        }
    }
    
    /**
     * Verifica se ja existe usuario com mesmo login
     * @param string $login - Login a ser verificado
     * @param int $cod_usuario - Codigo do usuario, caso seja update
     * @return bool
     */
    function existeOutroUsuario($login, $cod_usuario=false)
    {
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS cod_usuario "
			."FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
			."WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." = '".$login."' AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." <> 0";
        if ($cod_usuario) $sql .=" AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." <> ".$cod_usuario." ";
        $rs = $this->container["db"]->execSQL($sql);
        return !$rs->EOF;
    }
    
    /**
     * Busca lista de usuarios no banco de dados
     * @param string $secao - seção do usuario
     * @return array - Lista de usuários
     */
    function pegarListaUsuarios($secao=NULL)
    {
        if (!$secao && $secao==null)
        {
            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS codigo, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS texto, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." AS intchefia "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 "
                    . " ORDER BY ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"].", "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"];
        }
        else
        {
            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS codigo, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS texto, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." AS intchefia "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 "
                    . " AND ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." = '".$secao."' "
                    . " ORDER BY ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"].", "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"];
        }
        $rs = $this->container["db"]->execSQL($sql);
        return $rs->GetRows();
    }
    
    /**
     * Remove todas as entradas no banco de perfis de usuario
     * @param int $cod_usuario - Codigo do usuario
     */
    function limparPerfisUsuario($cod_usuario)
    {
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["colunas"]["cod_usuario"]." = " . $cod_usuario;
        $this->container["db"]->execSQL($sql);
    }
    
    /**
     * Busca informações de determinado usuario
     * @param int $cod_usuario - Codigo do usuario a buscar
     * @return array - Dados do usuario
     */
    function pegarInformacoesUsuario($cod_usuario)
    {
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS nome, "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]." AS email, "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]." AS login, "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." AS chefia, "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." AS secao, "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]." AS ramal, "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["altera_senha"]." AS altera_senha, "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ldap"]." AS ldap, "
                . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]." AS data_atualizacao "
                . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = ".$cod_usuario;
        $rs = $this->container["db"]->execSQL($sql);
        return $rs->fields;
    }
    
    /**
     * Busca todos os direitos que usuario tem no portal
     * @param int $interCod_Usuario - Codigo do usuario
     * @return array
     */
    function pegarDireitosUsuario($interCod_Usuario)
    {
        $out = array();
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["colunas"]["cod_perfil"]." AS cod_perfil "
                . " FROM ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["nome"]." "
                . "WHERE ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["colunas"]["cod_usuario"]." = ".$interCod_Usuario." "
                . "ORDER BY ".$this->container["config"]->bd["tabelas"]["usuarioxobjetoxperfil"]["colunas"]["cod_objeto"];
        $rs = $this->container["db"]->execSQL($sql);
        if ($rs->_numOfRows>0){
            while ($row = $rs->FetchRow()){
                $out[$row['cod_objeto']] = $row['cod_perfil'];
            }
        }
        return $out;
    }
    
    /**
     * Bloqueia usuário no banco de dados
     * @param int $cod - Código do usuário
     */
    function bloquearUsuario($cod)
    {
        $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                . "SET ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 0 "
                . "WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = " . $cod;
        $this->container["db"]->execSQL($sql);
    }
		
}