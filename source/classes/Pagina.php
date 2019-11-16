<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Classe Pagina é a classe principal do sistema, nela todas as outras são instanciadas
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
 * Classe responsavel por gerenciar a geracao e renderizacao das paginas
 */
class Pagina
{
		
    /**
     * Propriedade com instancia do dblayer
     * @var DBLayer
     */
    public $_db;
    
    /**
     * Propriedade com instancia da classe AdminObjeto
     * @var AdminObjeto 
     */
    public $_adminobjeto;
    
    /**
     * Propriedade com instancia da classe Objeto
     * @var Objeto 
     */
    public $_objeto;
    
    /**
     * Propriedade com instancia da classe Usuario
     * @var Usuario 
     */
    public $_usuario;
    
    /**
     * Propriedade com instancia da classe Parse
     * @var Parse 
     */
    public $_parser;
    
    /**
     * Propriedade com instancia da classe Administracao
     * @var Administracao 
     */
    public $_administracao;
    
    /**
     * Propriedade com instancia da classe ClasseLog
     * @var ClasseLog 
     */
    public $_log;
    
    /**
     * Propriedade com instancia da classe Blob
     * @var Blob 
     */
    public $_blob;
    
    /**
     * Codigo do objeto
     * @var integer 
     */
    public $cod_objeto;
    
    /**
     * Codigo do blob
     * @var integer 
     */
    public $cod_blob;
    
    /**
     * Grava inicio da execucao
     * @var integer
     */
    public $inicio;
    
    /**
     * Grava tempo que pagina demorou para ser executada
     * @var integer 
     */
    public $TempoDeExecucao;

    /**
     * Método construtor
     * @param DBLayer $_db - Referencia do objeto de conexao ao banco de dados
     * @param integer $cod_objeto - Codigo do objeto
     * @param integer $cod_blob - Codigo do blob
     */
    function __construct(&$_db, $cod_objeto=0, $cod_blob=0)
    {
        if ($cod_objeto==0) $cod_objeto = _ROOT;

        $this->cod_objeto = $cod_objeto;
        $this->cod_blob = $cod_blob;
        $this->_db = $_db;
        $this->_adminobjeto = new AdminObjeto();
        $this->_objeto = new Objeto($this, $cod_objeto);
//        xd($this->_objeto);
        $this->_usuario = new Usuario($this);
        $this->_parser = new Parse();
        $this->_rss = new Rss();
        $this->_blob = new Blob();
        $this->inicio = $this->getmicrotime();
    }
        
    /**
     * Gerencia os Headers que devem ser apresentados
     * @param type $codigo
     * @param type $mensagem
     */
    function headerHtml($codigo, $mensagem)
    {
        $codigos = array("400"=>"Bad Request",
            "401"=>"Unauthorized",
            "403"=>"Forbidden",
            "404"=>"Not Found",
            "500"=>"Internal Server Error",
            "501"=>"Not Implemented");
        
        http_response_code($codigo);
        echo "<h1>".$codigo." - ".$mensagem."</h1>";
        exit(0);
    }
		
    
    /**
     * Pega tempo em milisegundos para teste de velocidade de carga
     * @return integer
     */
    function getmicrotime()
    {
        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * Inclui classes de administracao e instancia objetos
     */
    function IncluirAdmin()
    {
//        include_once ('administracao.class.php');
//        include_once ('classelog.class.php');
        $this->_administracao = new Administracao($this);
        $this->_log = new ClasseLog($this);
    }

    /**
     * Monta pagina e executa. Retorna renderização para cliente.
     * @param string $acao - Acao a ser executada
     * @param boolean $incluirheader - Inclui header ou nao
     * @param boolean $irpararaiz - Volta para raiz depois de executar a funcao
     * @return boolean
     */
    function Executar($acao,$incluirheader=false, $irpararaiz=false)
    {
        
        $acaoCompleta = "";
        // quebrando string de acao pelas barras "/"
        $vacao = preg_split("[\/]", $acao);
        
        // array para montagem da pagina
        $incluir = array();
        // define o header
        $incluir["header"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/basic_header.php";
        $incluir["header"]["parse"] = true;
        // define o footer
        $incluir["footer"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/basic_footer.php";
        $incluir["footer"]["parse"] = true;
        // define a view
        $incluir["view"]["arquivo"] = "";
        $incluir["view"]["parse"] = true;
        
        // informa se header sera incluido ou nao
        if (isset($_GET["naoincluirheader"])) $incluirheader = false;
        else $incluirheader = true;
        
        // verifica acoes com blobs
        // estas acoes foram adicionadas no inicio do metodo para que a carga de imagens seja mais rapida
        if ($acao == "/blob/ver")
        {
            $this->_blob->VerBlob($this, $this->cod_blob);
        }
        elseif ($acao == "/blob/verinterno")
        {
            $this->_blob->VerBlobInterno($this, $this->cod_blob);
        }
        elseif ($acao == "/blob/baixar")
        {
            $this->_blob->BaixarBlob($this, $this->cod_blob);
        }
        elseif ($acao == "/blob/iconeclasse")
        {
            $prefixo = $_GET["nome"];
            $this->_blob->IconeClasse($this, $prefixo);
        }
        elseif ($acao == "/blob/iconeblob")
        {
            $prefixo = $_GET["nome"];
            $this->_blob->IconeBlob($this, $prefixo);
        }
        elseif (strpos($acao,"/do/")!==false || strpos($acao,"/manage/")!==false)
        {

            if ($acao == "/do/login_post")
            {
                $incluirheader = false;
                $acaoSistema = $vacao[count($vacao)-1];
                $incluir["view"]["arquivo"] = 'manage/'.$acaoSistema.".php";
                $incluir["view"]["parse"] = false;
            }
            elseif ($this->_usuario->PodeExecutar($this, $acao))
            {
                $this->IncluirAdmin();
//                $tmpArrPerfilObjeto = $this->_usuario->PegaDireitosDoUsuario($this, $_SESSION['usuario']['cod_usuario']);
                
                // verifica se eh operacao com pilhas
                // Copiar para pilha
                if ($acao=='/do/copy')
                {
                    $this->_administracao->CopiarObjetoParaPilha($this, $cod_objeto);
                    $acao = '/content/view';
                    return true;
                }
                // Mover objeto
                elseif ($acao=='/do/paste')
                {
                    if ($this->_objeto->PodeTerFilhos())
                    {
                        $this->_administracao->MoverObjeto($this, -1, $cod_objeto);
                        $acao = '/content/view';
                    }
                    return true;
                }
                // Colar como copia
                elseif ($acao=='/do/pastecopy')
                {
                    if ($this->_objeto->PodeTerFilhos())
                    {
                        $this->_administracao->DuplicarObjeto($this, -1,$cod_objeto);
                        $acao = '/content/view';
                    }
                    return true;
                }
                // Acao new_[prefixo classe] - Criação de novo objeto
                elseif (preg_match('|\/manage\/(.*?)_.*|is', $acao, $matches))
                {
                    $incluir["view"]["arquivo"] = 'manage/'.$matches[1].'_basic.php';
                    $incluir["view"]["parse"] = false;
                }
                // Editar objeto
                elseif ($acao=='/manage/edit')
                {
                    $incluir["view"]["arquivo"] = 'manage/edit_basic.php';
                    $incluir["view"]["parse"] = false;
                }
                
                
//                xd($acao);
                // Inclui header do publicare caso nao seja post nem tenha get naoincluirheader
                if (!strpos($acao,'_post') && $incluirheader)
                {
//                    include("header_publicare.php");
                    $incluir["header"]["arquivo"] = "includes/header_publicare.php";
                    $incluir["header"]["parse"] = false;
                    
                    $incluir["footer"]["arquivo"] = "includes/footer_publicare.php";
                    $incluir["footer"]["parse"] = false;
                }
                else
                {
                    $incluirheader = false;
                }
                
                // identifica qual acao do sistema devera executar
                $acaoSistema = $vacao[count($vacao)-1];

                // Se não tiver o include definido ainda, procura arquivos para incluir
                if ($incluir["view"]["arquivo"] == "")
                {
                    // caso exista ".php" na acao, inclui o arquivo diretamente
                    if (strpos($acaoSistema, '.php')!==false)
                    {
                        $incluir["view"]["arquivo"] = 'manage/'.$acaoSistema;
                        $incluir["view"]["parse"] = false;
                    }
                    // caso nao tenha, inclui o arquivo adicionando extensao .php
                    else
                    {
                        $incluir["view"]["arquivo"] = 'manage/'.$acaoSistema.'.php';
                        $incluir["view"]["parse"] = false;
                    }
                }
            }
            else
            {
                $this->ExibirMensagemProibido($acao);
            }
        }
        // resultado de busca
        elseif ($acao == "/html/objects/search_result")
        {
            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/objects/search_result.php";
            $incluir["view"]["parse"] = true;
        }
        elseif ($acao == "/html/login")
        {
            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/view_login.php";
            $incluir["view"]["parse"] = true;
        }
        // Ver pagina
        elseif ($acao == "/content/view")
        {
            if ($this->_usuario->PodeExecutar($this, $acao))
            {
            
                if ($this->_objeto->Valor($this, 'apagado'))
                {
                    $this->ExibirMensagemProibido($acao);
                    return false;
                }

                // caso tenha view definida manualmente pega o arquivo
                $tmpScriptAtual = $this->_objeto->metadados['script_exibir'];

                // caso o objeto nao esteja protegido
                if ($this->_adminobjeto->estaSobAreaProtegida($this, $this->_objeto->metadados['cod_objeto']))
                {
                    // caso tenha passado valor execview e objeto nao estiver com view protegida
                    if ((isset($_GET['execview'])) && (!preg_match("/_protegido.*/", $tmpScriptAtual)))
                    {
                        // verifica existencia da view dentro da pasta da pele
                        if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/view_".$_GET['execview'].".php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/view_".$_GET['execview'].".php";
                            $incluir["view"]["parse"] = true;
                        }
                        // verifica existencia da view dentro da pasta template
                        elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/html/template/view_".$_GET['execview'].".php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/view_".$_GET['execview'].".php";
                            $incluir["view"]["parse"] = true;
                        }
                    }

                    // caso tenha view definida manualmente e ainda nao tenha selecionado view
                    if ($tmpScriptAtual && $tmpScriptAtual != "" && $incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT'].$tmpScriptAtual))
                    {
                        $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT'].$this->_objeto->metadados['script_exibir'];
                        $incluir["view"]["parse"] = true;
                    }

                    // caso nao tenha view selecionada, verifica se tem na PELE
                    if ($this->_objeto->metadados['cod_pele'] > 0)
                    {
                        // uma view para a classe do objeto
                        if ($incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/view_".$this->_objeto->metadados['prefixoclasse'].".php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/view_".$this->_objeto->metadados['prefixoclasse'].".php";
                            $incluir["view"]["parse"] = true;
                        }

                        // verifica se tem view padrao na pele
                        if ($incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/view_basic.php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/view_basic.php";
                            $incluir["view"]["parse"] = true;
                        }
                    }


                    // caso nao tenha view definida ainda, busca na pasta template pelo prefixo da classe
                    if ($incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT']."/html/template/view_".$this->_objeto->metadados['prefixoclasse'].".php"))
                    {
                        $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/view_".$this->_objeto->metadados['prefixoclasse'].".php";
                        $incluir["view"]["parse"] = true;
                    }
                    // caso nao tenha view definida ainda, busca na pasta template pela view default
                    if ($incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT']."/html/template/view_basic.php"))
                    {
                        $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/view_basic.php";
                        $incluir["view"]["parse"] = true;
                    }

                    // caso nao encontre nenhuma view, mesmo depois de todas as buscas, exibe mensagem de erro
                    if ($incluir["view"]["arquivo"] == "")
                    {
                        echo "<span class=\"txtErro\">N&atilde;o foram encontrados os arquivos de SCRIPT DE EXIBI&Ccedil;&Atilde;O.</span>";
                    }
                }
                // caso esteja protegido
                else
                {
                    $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/view_protegido.php";
                    $incluir["view"]["parse"] = true;
                }

                // Definindo header e footer que serao exibidos
                // caso tenha pele busca header e footer dentro da pele
                if ($this->_objeto->metadados['cod_pele'] > 0)
                {
                    // verificando header dentro da pele
                    if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/header.php"))
                    {
                        $incluir["header"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/header.php";
                        $incluir["header"]["parse"] = true;
                    }

                    //verificando footer dentro da pele
                    if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/footer.php"))
                    {
                        $incluir["footer"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/footer.php";
                        $incluir["footer"]["parse"] = true;
                    }
                }
            }
            else
            {
                $this->ExibirMensagemProibido($acao);
            }
        }
        
        $buffer = "";
        
        
//        xd($incluir);
        
        if ($incluirheader)
        {
            if ($incluir["header"]["parse"])
            {
//                $this->_parser->Start($buffer, 1);
//                $buffer .= file_get_contents($incluir["header"]["arquivo"]);
                $this->_parser->Start($incluir["header"]["arquivo"]);
            }
            else
            {
                include($incluir["header"]["arquivo"]);
            }
        }
        
//        xd($buffer);
        
        if ($incluir["view"]["parse"])
        {
//            $buffer .= "\n<!-- ".substr($incluir[0], strrpos($incluir[0], '/'))." -->";
//            $buffer .= "\n<!-- robot_contents -->\n";
//            echo "\n<!-- ".substr($incluir[0], strrpos($incluir[0], '/'))." -->";
//            echo "\n<!-- robot_contents -->\n";
//            $buffer .= file_get_contents($incluir["view"]["arquivo"]);
            $this->_parser->Start($incluir["view"]["arquivo"]);
//            echo "\n<!-- /robot_contents -->\n";
//            $buffer .= "\n<!-- /robot_contents -->\n";
        }
        else
        {
            include($incluir["view"]["arquivo"]);
        }
        
        if ($incluirheader)
        {
            if ($incluir["footer"]["parse"])
            {
//                $buffer .= file_get_contents($incluir["footer"]["arquivo"]);
                $this->_parser->Start($incluir["footer"]["arquivo"]);
            }
            else
            {
                include($incluir["footer"]["arquivo"]);
            }
        }
//        xd($incluir);
        
        $this->TempoDeExecucao = $this->getmicrotime() - $this->inicio;
        $buffer .= "\n<!-- TEMPO DE EXECUCAO: ".$this->TempoDeExecucao." -->";
        
        if ($buffer != "") $this->_parser->Start($buffer, 1);
        
        return true;
    }

	function AdicionarAviso($txt,$fatal=false)
	{
		$this->avisos[]=$txt;
		if ($fatal)
		{
			foreach ($this->avisos as $aviso)
			{
				echo $aviso.'<br>';
				exit;
			}
		}
	}

    /**
     * Exibe mensagem de proibido
     * @param string $acao
     */
    function ExibirMensagemProibido($acao)
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/template/error404.php"))
        {
            $this->_parser->Start($_SERVER['DOCUMENT_ROOT']."/html/template/error404.php");
        }
        elseif (file_exists(_PUBLICAREPATH."/includes/error404.php"))
        {
            include(_PUBLICAREPATH."/includes/error404.php");
        }
        else
        {
            $this->headerHtml(403, "Visualização não permitida");
        }
        exit();
    }

	
	
	function BoxPublicareTop($titulo,$botoes='')
	{
		global $titulo;
		
		$cols = 0;

		if ($botoes=='')
		{
			$botoes='exibir,voltar';
		}
		if (!is_array($botoes))
		{
			$botoes=explode(",",$botoes);
		}
		
		echo '
		<TABLE WIDTH="550" BORDER="0" CELLPADDING="0" CELLSPACING="0" background="/html/imagens/portalimages/form_top_bg.png">
		<TR>
			<TD width="100%"><img border=0 src="/html/imagens/portalimages/form_'.$titulo.'_top.png" ALT=""></td>';

		
		if (in_array('exibir',$botoes))
		{
			$cols++;
			echo '
			<td><a class="ABranco" href="/index.php/content/view/'.$this->_objeto->Valor($this, "cod_objeto").'.html"><img border=0 src="/html/imagens/portalimages/button_exibir.png" ALT="Exibir Objeto"</a></td>';
		}
		
		if (in_array('pai',$botoes))
		{
			if ($this->_objeto->Valor($this, "cod_objeto")!=_ROOT)
			{
				$cols++;
				echo '
			<td><a class="ABranco" href="/index.php/do/list_content/<? echo $this->objeto->Valor($this, "cod_pai")?>.html"><img border=0 src="/html/imagens/portalimages/button_parent.png" ALT="Listar Conte&uacute;do do Pai"</a></td>';
			}
		}

		if (in_array('voltar',$botoes))
		{
			$cols++;
			echo '<TD><a href="#" onclick="history.back()"><img border=0 src="/html/imagens/portalimages/button_back.png" ALT="Voltar"></a></TD>';
		}

		echo '
		</TR>
		<TR>
			<TD colspan="'.($cols+1).'" background="/html/imagens/portalimages/form_top_02.png">
				<img align="top" src="/html/imagens/portalimages/neutro.png" width=75 height=31><font class="pblFormTextTitle">'.$this->_objeto->Valor($this, "titulo").'</font></td>
		</TR>
	</table>
	<table width="550" border="0" cellpadding="3" cellspacing="0" background="/html/imagens/portalimages/form_bg.png">
	<tr>
		<td>';
	}

	function BoxPublicareBottom()
	{
?>
		</td>
	</tr>
	</table>
	<img src="/html/imagens/portalimages/form_bottom.png">
<?
	}

	function BoxSimplesTop()
	{
?>
		<img src="/html/imagens/portalimages/form_top_fio.png"><table width="550" border="0" cellpadding="4" cellspacing="2" background="/html/imagens/portalimages/form_bg.png">
		<tr>
			<td class="pblFormTitle">
<?
	}

	function BoxSimplesBottom()
	{
		BoxPublicareBottom();
	}
		
}
?>
