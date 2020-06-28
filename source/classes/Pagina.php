<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo
 * @copyright GPL © 2007
 * @package publicare
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
    
    public $config;
    
    public $acao;

    /**
     * Método construtor
     * @param DBLayer $_db - Referencia do objeto de conexao ao banco de dados
     * @param integer $cod_objeto - Codigo do objeto
     * @param integer $cod_blob - Codigo do blob
     */
    function __construct(&$_db, $cod_objeto=0, $cod_blob=0)
    {
        $this->config = $_db->getConfig();
        unset($this->config["bd"]);
        
        if ($cod_objeto==0) $cod_objeto = $this->config["portal"]["objroot"];
        

        $this->cod_objeto = $cod_objeto;
        
        
        $this->cod_blob = $cod_blob;
        $this->_db = $_db;
        $this->_adminobjeto = new AdminObjeto($this);
        $this->_objeto = new Objeto($this, $cod_objeto);
        $this->_usuario = new Usuario($this);
        $this->_parser = new Parse();
        $this->_rss = new Rss();
        $this->_blob = new Blob($this);
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
//        xd("aa");
        $this->_administracao = new Administracao($this);
//        xd($this->_administracao);
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
        $this->acao = $acao;
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
            $this->_blob->VerBlob($this->cod_blob);
        }
        elseif ($acao == "/blob/verinterno")
        {
            $this->_blob->VerBlobInterno($this->cod_blob);
        }
        elseif ($acao == "/blob/baixar")
        {
            $this->_blob->BaixarBlob($this->cod_blob);
        }
        elseif ($acao == "/blob/iconeclasse")
        {
            $prefixo = $_GET["nome"];
            $this->_blob->IconeClasse($prefixo);
        }
        elseif ($acao == "/blob/iconeblob")
        {
            $prefixo = $_GET["nome"];
            $this->_blob->IconeBlob($prefixo);
        }
        elseif (strpos($acao,"/do/")!==false)
        {
            if ($acao == "/do/login_post" || $acao == "/do/esquecisenha_post")
            {
                $incluirheader = false;
                $acaoSistema = $vacao[count($vacao)-1];
                $incluir["view"]["arquivo"] = 'manage/'.$acaoSistema.".php";
                $incluir["view"]["parse"] = false;
            }
            elseif ($this->_usuario->PodeExecutar($acao))
            {
                $this->IncluirAdmin();
                
                // verifica se eh operacao com pilhas
                // Copiar para pilha
                if ($acao=='/do/copy')
                {
                    $this->_administracao->CopiarObjetoParaPilha($cod_objeto);
                    $acao = '/content/view';
                    return true;
                }
                // Mover objeto
                elseif ($acao=='/do/paste')
                {
                    if ($this->_objeto->PodeTerFilhos())
                    {
                        $this->_administracao->MoverObjeto(-1, $cod_objeto);
                        $acao = '/content/view';
                    }
                    return true;
                }
                // Colar como copia
                elseif ($acao=='/do/pastecopy')
                {
                    if ($this->_objeto->PodeTerFilhos())
                    {
                        $this->_administracao->DuplicarObjeto(-1, $cod_objeto);
                        $acao = '/content/view';
                    }
                    return true;
                }
                else 
                {
                    $acaoSistema = $vacao[count($vacao)-1];
                    if (($acaoSistema != "new_post" && strpos($acaoSistema, "new_")!==false)
                            || $acaoSistema=="edit")
                    {
                        $incluir["view"]["arquivo"] = 'manage/form_construct.php';
                        $incluir["view"]["parse"] = false;
                    }
                    else
                    {
                        $incluir["view"]["arquivo"] = 'manage/'.$acaoSistema.".php";
                        $incluir["view"]["parse"] = false;
                    }
                }
                
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
        elseif ($acao == "/html/cadastro")
        {
            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/view_cadastro.php";
            $incluir["view"]["parse"] = true;
        }
        elseif ($acao == "/html/esquecisenha")
        {
            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/view_esquecisenha.php";
            $incluir["view"]["parse"] = true;
        }
        // Ver pagina
        elseif ($acao == "/content/view")
        {
            if ($this->_usuario->PodeExecutar($acao))
            {
                if ($this->_objeto->Valor('apagado') && $_SESSION["usuario"]["perfil"] > _PERFIL_EDITOR)
                {
                    $this->ExibirMensagemProibido($acao);
                    return false;
                }

                // caso tenha view definida manualmente pega o arquivo
                $tmpScriptAtual = $this->_objeto->metadados['script_exibir'];

//        xd($this->_objeto->metadados['cod_objeto']);
                // caso o objeto nao esteja protegido
                if ($this->_adminobjeto->estaSobAreaProtegida())
                {
                    // caso tenha passado valor execview e objeto nao estiver com view protegida
                    if ((isset($_GET['execview'])) && (!preg_match("/_protegido.*/", $tmpScriptAtual)))
                    {
                        // verifica existencia da view dentro da pasta da pele
                        if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->Valor('prefixopele')."/view_".$_GET['execview'].".php"))
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
//        $buffer .= "\n<!-- TEMPO DE EXECUCAO: ".$this->TempoDeExecucao." -->";
        
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
        $header = $_SERVER['DOCUMENT_ROOT']."/html/template/basic_header.php";
        $footer = $_SERVER['DOCUMENT_ROOT']."/html/template/basic_footer.php";
        $pagina = "";
        $parse = true;
        
        if ($this->_objeto->metadados['cod_pele'] > 0)
        {
            if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/header.php"))
            {
                $header = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/header.php";
            }
            if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/footer.php"))
            {
                $footer = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->_objeto->metadados['prefixopele']."/footer.php";
            }
        }
        
        if (isset($_GET["naoincluirheader"]))
        {
            $header = "";
            $footer = "";
        }
        
        if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/template/error404.php"))
        {
            $pagina = $_SERVER['DOCUMENT_ROOT']."/html/template/error404.php";
            $parse = true;
        }
        elseif (file_exists($this->config["portal"]["pblpath"]."/includes/error404.php"))
        {
            $pagina = $this->config["portal"]["pblpath"]."/includes/error404.php";
            $parse = false;
        }
        else
        {
            $this->headerHtml(403, "Visualização não permitida");
        }
        
        $_SESSION["escondetitulo"] = true;
        
        if ($header != "")
        {
            $this->_parser->Start($header);
        }
        if ($parse === true)
        {
            $this->_parser->Start($pagina);
        }
        else
        {
            include($pagina);
        }
        if ($footer != "")
        {
            $this->_parser->Start($footer);
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
			<td><a class="ABranco" href="/index.php/content/view/'.$this->_objeto->Valor("cod_objeto").'.html"><img border=0 src="/html/imagens/portalimages/button_exibir.png" ALT="Exibir Objeto"</a></td>';
		}
		
		if (in_array('pai',$botoes))
		{
			if ($this->_objeto->Valor("cod_objeto")!=$_page->config["portal"]["objroot"])
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
				<img align="top" src="/html/imagens/portalimages/neutro.png" width=75 height=31><font class="pblFormTextTitle">'.$this->_objeto->Valor("titulo").'</font></td>
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
