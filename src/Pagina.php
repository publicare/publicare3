<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @file Pagina.php
 * @description Classe responsável por gerenciar a exibição de páginas
 * @copyright MIT © 2020
 * @package publicare/classes
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

namespace Pbl;

/**
 * Classe responsavel por gerenciar a geracao e renderizacao das paginas
 */
class Pagina
{
		
    /**
     * Propriedade com instancia do dblayer
     * @var DBLayer
     */
    public $db;
    
    /**
     * Propriedade com instancia da classe AdminObjeto
     * @var AdminObjeto 
     */
    public $adminobjeto;
    
    /**
     * Propriedade com instancia da classe Objeto
     * @var Objeto 
     */
    public $objeto;
    
    /**
     * Propriedade com instancia da classe Usuario
     * @var Usuario 
     */
    public $usuario;
    
    /**
     * Propriedade com instancia da classe Parse
     * @var Parse 
     */
    public $parser;
    
    /**
     * Propriedade com instancia da classe Administracao
     * @var Administracao 
     */
    public $administracao;
    
    /**
     * Propriedade com instancia da classe ClasseLog
     * @var ClasseLog 
     */
    public $log;
    
    /**
     * Propriedade com instancia da classe Blob
     * @var Blob 
     */
    public $blob;
    
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
    function __construct(&$db, $cod_objeto=0, $cod_blob=0)
    {

        
        $this->config = $db->getConfig();
        unset($this->config["bd"]);
        if (isset($this->config["portal"]["debug"]) && $this->config["portal"]["debug"] === true)
        {
            x("page::construct cod_objeto=".$cod_objeto);
        }

        // caso nao tenha objeto informado, pega objeto root
        if ($cod_objeto==0) $cod_objeto = $this->config["portal"]["objroot"];
        
        $this->cod_objeto = $cod_objeto;
        $this->cod_blob = $cod_blob;
        $this->db = $db;
        $this->adminobjeto = new AdminObjeto($this);
        $this->objeto = new Objeto($this, $cod_objeto);
        $this->usuario = new Usuario($this);
        $this->parser = new Parse($this);
        $this->blob = new Blob($this);
        $this->administracao = new Administracao($this);
        $this->log = new ClasseLog($this);
        $this->inicio = $this->getmicrotime();
    }

    public static function iniciar($config)
    {
        // $page = $this;
        // if (isset($config["portal"]["debug"]) && $config["portal"]["debug"] === true)
        // {
        //     x("page::iniciar");
        // }
        // iniciando funções compartilhadas
        require_once(__DIR__ . "/extra/constantes.php");
        require_once(__DIR__ . "/extra/funcoes.php");
        require_once(__DIR__ . "/extra/data.php");

        // inicia sessao caso não esteja iniciada ainda
        if (!isset($_SESSION)) 
        {
            // definindo nome de sessao proprio, para evitar roubo de sessao/cookie
        //    session_name(md5('pbl'.$_SERVER["REMOTE_ADDR"]));
            
            // utilizando cookies
            ini_set("session.use_cookies", true);
            // apenas cookies
            ini_set("session.use_only_cookies", true);
            // permite apenas sessoes inicializadas por aqui
            ini_set("session.use_strict_mode", true);
            // bloqueia acesso ao cookie de sessao por scripts
            ini_set("session.cookie_httponly", true);
            
            session_start();
        }

        // inicia tratamento de URL
        $url = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, "UTF-8");
        // removendo index.php da url
        $url = preg_replace('/index\.php/', '', $url);
        // removendo slash repetido
        $url = preg_replace('/(\/)\1+/', '/', $url);
// $_SERVER['REQUEST_URI'] = $url;
// retira variaveis passadas por _GET da url
        $aurl = preg_split("[\?]", $url);
        $url = $aurl[0];

        $vurlpbl = preg_split("[\/]", $config["portal"]["url"]);

        // iniciando banco de dados
        $db = new DBLayer($config);

        $incluir = "";
        $amigavel = "";
        $action = "";
        $path = $_SERVER["DOCUMENT_ROOT"];
        $cod_root = $config["portal"]["objroot"];

        // caso tenha array de dominios definido, verifica codigo objeto root do dominio
        // if (isset($_dominios))
        // {
        //    $dominio = htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES, "UTF-8");
        //    foreach ($_dominios as $dom => $dom1) 
        //    {
        //        if ($dominio == $dom) $cod_root = $_dominios[$dominio];
        //    }
        // }

        $cod_objeto = $cod_root;
        $cod_blob = 0;
        $url = $config["portal"]["pasta"]!=""?str_ireplace("/".$config["portal"]["pasta"]."/", "/", $url):$url;
        $url = preg_replace("[\/+]", "/", $url);
        $arrUrl = preg_split("[\/]", $url);

        if (isset($arrUrl[1]))
        {
            switch ($arrUrl[1])
            {
                // blob
                case "blob":
                    $cod_blob = isset($arrUrl[3])?$arrUrl[3]:$cod_blob;
                    $action = "/blob/".$arrUrl[2];
                    break;
                // includes
                case "include":
                    include('includes/' . $arrUrl[2] . '.php');
                    exit();
                    break;

                // formulario de login
                case "login":
        //        case "cadastro":
        //        case "esquecisenha":
                    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/html/template/view_".$arrUrl[1].".php"))
                    {
                    //     $action = "/html/".$arrUrl[1];
                    //     if (isset($arrUrl[2])) 
                    //     {
                    //         $cod_objeto = identificaCodigoObjeto($arrUrl[2], 0);
                    //         if ($cod_objeto==0)
                    //         {
                    //             $amigavel = $arrUrl[2];
                    //         }
                    //     }
                    }
                    else
                    {
                        $incluir = "includes/".$arrUrl[1].".php";
                    }
                    break;
                    
                // chamando arquivos pasta objects
                case "html":
                    if ($arrUrl[2] == "objects")
                    {
                        $tempFile = "";
                        for ($i = 3; $i < count($arrUrl); $i++)
                        {
                            $tempFile .= "/" . $arrUrl[$i];
                        }
                        if (file_exists($path . "/html/objects" . $tempFile))
                        {
                            $incluir = $path . "/html/objects" . $tempFile;
                        }
                        if (file_exists($path . "/html/objects" . $tempFile.".php"))
                        {
                            $incluir = $path . "/html/objects" . $tempFile.".php";
                        }
        //                xd($incluir);
                    }
                    break;

                // /content/view
                case "content":
                    if ($arrUrl[2] == "view")
                    {
                        $action = "/content/view";
                        $cod_objeto = identificaCodigoObjeto($arrUrl[3], $cod_root);
                    }
                    break;

                // manage ou do
                case "manage":
                case "do":
                    $action = "/" . $arrUrl[1] . "/" . $arrUrl[2];
                    if (isset($arrUrl[3]))
                    {
                        $cod_objeto = identificaCodigoObjeto($arrUrl[3], $cod_root);
                    }
        //            xd($cod_objeto);
                    break;

                // nenhum caso conhecido, tenta url amigavel
                default:
                    $action = "/content/view";
                    // remove extensoes
                    $temp = preg_split("[\.]", $arrUrl[1]);
                    $arrUrl[1] = $temp[0];
                    // garante url sem caracteres especiais
                    $arrUrl[1] = limpaString($arrUrl[1]);
                    // tudo para minusculo
                    $arrUrl[1] = strtolower($arrUrl[1]);
                    $amigavel = filter_var($arrUrl[1], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    break;
            }
        }
        else
        {
            $action = "/content/view";
        }

        

        // verifica se eh url amigavel
        if ($amigavel != "")
        {
            // procura no banco pela url amigavel
            $sql = "SELECT ".$db->tabelas["objeto"]["nick"].".".$db->tabelas["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto "
                    . " FROM ".$db->tabelas["objeto"]["nome"]." ".$db->tabelas["objeto"]["nick"]." "
                    . " WHERE ".$db->tabelas["objeto"]["nick"].".".$db->tabelas["objeto"]["colunas"]["url_amigavel"]." = '" . $amigavel . "'";
            $rs = $db->ExecSQL($sql, 0, 1);
            while ($row = $rs->FetchRow())
            {
                $cod_objeto = (int)$row["cod_objeto"];
            }
        }

        // verifica existencia de action
        if ($action=="" || isset($_GET["action"]) || isset($_POST["action"]))
        {
            // se nao tiver, verifica action em get
            if (isset($_GET["action"])) $action = htmlspecialchars($_GET["action"], ENT_QUOTES, "UTF-8");
            // se nao tiver em get, verifica em post
            elseif (isset($_POST["action"])) $action = htmlspecialchars($_POST["action"], ENT_QUOTES, "UTF-8");
            // se nao tiver em post, define action como "/content/view"
            else $action = "/content/view";
        }

        $page = new Pagina($db, $cod_objeto, $cod_blob);
        $page->setAction($action);

        if ($incluir != "") {
            $incluir = str_replace("\.\.\/", "", $incluir);
            include($incluir);
            exit();
        }

        return $page;
    }

    public function setAction($action)
    {
        $this->acao = $action;
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
        if (isset($this->config["portal"]["debug"]) && $this->config["portal"]["debug"] === true)
        {
            x("page::getmicrotime");
        }

        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * Inclui classes de administracao e instancia objetos
     */
    function incluirAdmin()
    {
        if (isset($this->config["portal"]["debug"]) && $this->config["portal"]["debug"] === true)
        {
            x("page::incluirAdmin");
        }
//        xd("aa");
        // $this->administracao = new Administracao($this);
//        xd($this->administracao);
        // $this->log = new ClasseLog($this);
    }

    /**
     * Monta pagina e executa. Retorna renderização para cliente.
     * @param string $acao - Acao a ser executada
     * @param boolean $incluirheader - Inclui header ou nao
     * @param boolean $irpararaiz - Volta para raiz depois de executar a funcao
     * @return boolean
     */
    function executar($acao=false, $incluirheader=false, $irpararaiz=false)
    {
        if (isset($this->config["portal"]["debug"]) && $this->config["portal"]["debug"] === true)
        {
            x("page::executar acao=".$acao);
        }

        if ($acao === false)
        {
            $acao = $this->acao;
        }
        else
        {
            $this->acao = $acao;
        }
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
        $incluirheader = isset($_GET["naoincluirheader"])?false:true;
        
        // verifica acoes com blobs
        // estas acoes foram adicionadas no inicio do metodo para que a carga de imagens seja mais rapida
        if ($acao == "/blob/ver")
        {
            $this->blob->VerBlob($this->cod_blob);
        }
        elseif ($acao == "/blob/verinterno")
        {
            $this->blob->VerBlobInterno($this->cod_blob);
        }
        elseif ($acao == "/blob/baixar")
        {
            $this->blob->BaixarBlob($this->cod_blob);
        }
        elseif ($acao == "/blob/iconeclasse")
        {
            $prefixo = $_GET["nome"];
            $this->blob->IconeClasse($prefixo);
        }
        elseif ($acao == "/blob/iconeblob")
        {
            $prefixo = $_GET["nome"];
            $this->blob->iconeBlob($prefixo);
        }
        elseif (strpos($acao,"/do/")!==false 
            || strpos($acao,"/manage/")!==false)
        {
            if ($acao == "/do/login_post" 
                || $acao == "/do/esquecisenha_post")
            {
                $incluirheader = false;
                $acaoSistema = $vacao[count($vacao)-1];
                $incluir["view"]["arquivo"] = 'manage/'.$acaoSistema.".php";
                $incluir["view"]["parse"] = false;
            }
            elseif ($this->usuario->podeExecutar($acao))
            {
                $this->incluirAdmin();
                
                // verifica se eh operacao com pilhas
                // Copiar para pilha
                if ($acao=='/do/copy')
                {
                    $this->administracao->copiarObjetoParaPilha($cod_objeto);
                    $acao = '/content/view';
                    return true;
                }
                // Mover objeto
                elseif ($acao=='/do/paste')
                {
                    if ($this->objeto->podeTerFilhos())
                    {
                        $this->administracao->moverObjeto(-1, $cod_objeto);
                        $acao = '/content/view';
                    }
                    return true;
                }
                // Colar como copia
                elseif ($acao=='/do/pastecopy')
                {
                    if ($this->objeto->podeTerFilhos())
                    {
                        $this->administracao->duplicarObjeto(-1, $cod_objeto);
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
                //if (isset($_GET["naoincluirheader"])) $incluirheader = false;
            }
            else
            {
                $this->exibirMensagemProibido($acao);
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
            if ($this->usuario->podeExecutar($acao))
            {
                if ($this->objeto->valor('apagado') && $_SESSION["usuario"]["perfil"] > _PERFIL_EDITOR)
                {
                    $this->exibirMensagemProibido($acao);
                    return false;
                }
                
                // caso tenha view definida manualmente pega o arquivo
                $tmpScriptAtual = $this->objeto->metadados['script_exibir'];
                
                // caso o objeto nao esteja protegido
                if ($this->adminobjeto->estaSobAreaProtegida())
                {
                    // caso tenha passado valor execview e objeto nao estiver com view protegida
                    if ((isset($_GET['execview'])) && (!preg_match("/_protegido.*/", $tmpScriptAtual)))
                    {
                        // verifica existencia da view dentro da pasta da pele
                        if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->valor('prefixopele')."/view_".$_GET['execview'].".php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/view_".$_GET['execview'].".php";
                            $incluir["view"]["parse"] = true;
                        }
                        // verifica existencia da view dentro da pasta template
                        elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/html/template/view_".$_GET['execview'].".php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/view_".$_GET['execview'].".php";
                            $incluir["view"]["parse"] = true;
                        }
                    }
                    // xd($tmpScriptAtual);
                    
                    // caso tenha view definida manualmente e ainda nao tenha selecionado view
                    if ($tmpScriptAtual && $tmpScriptAtual != "" && $incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT'].$tmpScriptAtual))
                    {
                        $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT'].$this->objeto->metadados['script_exibir'];
                        $incluir["view"]["parse"] = true;
                    }
                    
                    // caso nao tenha view selecionada, verifica se tem na PELE
                    if ($this->objeto->metadados['cod_pele'] > 0)
                    {
                        // uma view para a classe do objeto
                        if ($incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/view_".$this->objeto->metadados['prefixoclasse'].".php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/view_".$this->objeto->metadados['prefixoclasse'].".php";
                            $incluir["view"]["parse"] = true;
                        }
                        
                        // verifica se tem view padrao na pele
                        if ($incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/view_basic.php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/view_basic.php";
                            $incluir["view"]["parse"] = true;
                        }
                    }
                    
                    
                    // caso nao tenha view definida ainda, busca na pasta template pelo prefixo da classe
                    if ($incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT']."/html/template/view_".$this->objeto->metadados['prefixoclasse'].".php"))
                    {
                        $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/view_".$this->objeto->metadados['prefixoclasse'].".php";
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
                if ($this->objeto->metadados['cod_pele'] > 0)
                {
                    // verificando header dentro da pele
                    if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/header.php"))
                    {
                        $incluir["header"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/header.php";
                        $incluir["header"]["parse"] = true;
                    }
                    
                    //verificando footer dentro da pele
                    if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/footer.php"))
                    {
                        $incluir["footer"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/footer.php";
                        $incluir["footer"]["parse"] = true;
                    }
                }
            }
            else
            {
                $this->exibirMensagemProibido($acao);
            }
        }
        
        $buffer = "";
        //xd($incluirheader);
        
        if ($incluirheader)
        {
            if ($incluir["header"]["parse"])
            {
                //                $this->parser->start($buffer, 1);
                //                $buffer .= file_get_contents($incluir["header"]["arquivo"]);
                $this->parser->start($incluir["header"]["arquivo"]);
            }
            else
            {
                include($incluir["header"]["arquivo"]);
            }
        }
        
        if ($incluir["view"]["parse"])
        {
//            $buffer .= "\n<!-- ".substr($incluir[0], strrpos($incluir[0], '/'))." -->";
//            $buffer .= "\n<!-- robot_contents -->\n";
//            echo "\n<!-- ".substr($incluir[0], strrpos($incluir[0], '/'))." -->";
//            echo "\n<!-- robot_contents -->\n";
//            $buffer .= file_get_contents($incluir["view"]["arquivo"]);
            $this->parser->start($incluir["view"]["arquivo"]);
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
                $this->parser->start($incluir["footer"]["arquivo"]);
            }
            else
            {
                include($incluir["footer"]["arquivo"]);
            }
        }
//        xd($incluir);
        
        $this->TempoDeExecucao = $this->getmicrotime() - $this->inicio;
//        $buffer .= "\n<!-- TEMPO DE EXECUCAO: ".$this->TempoDeExecucao." -->";
        
        if ($buffer != "") $this->parser->start($buffer, 1);
        
        return true;
    }

	function adicionarAviso($txt,$fatal=false)
	{
        if (isset($this->config["portal"]["debug"]) && $this->config["portal"]["debug"] === true)
        {
            x("page::adicionarAviso");
        }

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
    function exibirMensagemProibido($acao)
    {
        if (isset($this->config["portal"]["debug"]) && $this->config["portal"]["debug"] === true)
        {
            x("page::exibirMensagemProibido");
        }

        $header = $_SERVER['DOCUMENT_ROOT']."/html/template/basic_header.php";
        $footer = $_SERVER['DOCUMENT_ROOT']."/html/template/basic_footer.php";
        $pagina = "";
        $parse = true;
        
        if ($this->objeto->metadados['cod_pele'] > 0)
        {
            if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/header.php"))
            {
                $header = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/header.php";
            }
            if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/footer.php"))
            {
                $footer = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->objeto->metadados['prefixopele']."/footer.php";
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
            $this->parser->start($header);
        }
        if ($parse === true)
        {
            $this->parser->start($pagina);
        }
        else
        {
            include($pagina);
        }
        if ($footer != "")
        {
            $this->parser->start($footer);
        }
        
        exit();
    }

		
}
?>
