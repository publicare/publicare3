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

namespace Pbl\Core;

use Pbl\Core\Base;

/**
 * Classe responsavel por gerenciar a geracao e renderizacao das paginas
 */
class Pagina extends Base
{
    /**
     * Codigo do objeto
     * @var integer 
     */
    private $cod_objeto;
    
    /**
     * Codigo do blob
     * @var integer 
     */
    private $cod_blob;
    
    public $action;

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

    public $avisos = array();

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setCodObjeto($cod_objeto)
    {
        $this->cod_objeto = $cod_objeto;
    }

    public function getCodObjeto()
    {
        return $this->cod_objeto;
    }

    public function setCodBlob($cod_blob)
    {
        $this->cod_blob = $cod_blob;
    }

    public function getCodBlob()
    {
        return $this->cod_blob;
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
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
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
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
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
        if ($acao !== false)
        {
            $this->action = $acao;
        }
        else
        {
            $acao = $this->action;
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
                // xd("a");
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
                    if ($this->container["objeto"]->podeTerFilhos())
                    {
                        $this->administracao->moverObjeto(-1, $cod_objeto);
                        $acao = '/content/view';
                    }
                    return true;
                }
                // Colar como copia
                elseif ($acao=='/do/pastecopy')
                {
                    if ($this->container["objeto"]->podeTerFilhos())
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
                    $incluir["header"]["arquivo"] = __DIR__."/../includes/header_publicare.php";
                    $incluir["header"]["parse"] = false;
                    
                    $incluir["footer"]["arquivo"] = __DIR__."/../includes/footer_publicare.php";
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
                // xd($this->usuario->podeExecutar($acao));
                if ($this->container["objeto"]->valor('apagado') && $_SESSION["usuario"]["perfil"] > _PERFIL_EDITOR)
                {
                    $this->exibirMensagemProibido($acao);
                    return false;
                }
                
                // caso tenha view definida manualmente pega o arquivo
                $tmpScriptAtual = $this->container["objeto"]->metadados['script_exibir'];
                
                
                // caso o objeto nao esteja protegido
                if ($this->adminobjeto->estaSobAreaProtegida())
                {
                    // caso tenha passado valor execview e objeto nao estiver com view protegida
                    if ((isset($_GET['execview'])) && (!preg_match("/_protegido.*/", $tmpScriptAtual)))
                    {
                        // verifica existencia da view dentro da pasta da pele
                        if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->valor('prefixopele')."/view_".$_GET['execview'].".php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/view_".$_GET['execview'].".php";
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
                        $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT'].$this->container["objeto"]->metadados['script_exibir'];
                        $incluir["view"]["parse"] = true;
                    }
                    
                    // caso nao tenha view selecionada, verifica se tem na PELE
                    if ($this->container["objeto"]->metadados['cod_pele'] > 0)
                    {
                        // uma view para a classe do objeto
                        if ($incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/view_".$this->container["objeto"]->metadados['prefixoclasse'].".php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/view_".$this->container["objeto"]->metadados['prefixoclasse'].".php";
                            $incluir["view"]["parse"] = true;
                        }
                        
                        // verifica se tem view padrao na pele
                        if ($incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/view_basic.php"))
                        {
                            $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/view_basic.php";
                            $incluir["view"]["parse"] = true;
                        }
                    }
                    
                    
                    // caso nao tenha view definida ainda, busca na pasta template pelo prefixo da classe
                    if ($incluir["view"]["arquivo"] == "" && file_exists($_SERVER['DOCUMENT_ROOT']."/html/template/view_".$this->container["objeto"]->metadados['prefixoclasse'].".php"))
                    {
                        $incluir["view"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/template/view_".$this->container["objeto"]->metadados['prefixoclasse'].".php";
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
                if ($this->container["objeto"]->metadados['cod_pele'] > 0)
                {
                    // verificando header dentro da pele
                    if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/header.php"))
                    {
                        $incluir["header"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/header.php";
                        $incluir["header"]["parse"] = true;
                    }
                    
                    //verificando footer dentro da pele
                    if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/footer.php"))
                    {
                        $incluir["footer"]["arquivo"] = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/footer.php";
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
                $this->container["parse"]->start($incluir["header"]["arquivo"]);
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
            $this->container["parse"]->start($incluir["view"]["arquivo"]);
//            echo "\n<!-- /robot_contents -->\n";
//            $buffer .= "\n<!-- /robot_contents -->\n";
        }
        else
        {
            // xd($incluir["view"]["arquivo"]);
            include(__DIR__."../../".$incluir["view"]["arquivo"]);
        }
        
        if ($incluirheader)
        {
            if ($incluir["footer"]["parse"])
            {
//                $buffer .= file_get_contents($incluir["footer"]["arquivo"]);
                $this->container["parse"]->start($incluir["footer"]["arquivo"]);
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
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
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
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("page::exibirMensagemProibido");
        }

        $header = $_SERVER['DOCUMENT_ROOT']."/html/template/basic_header.php";
        $footer = $_SERVER['DOCUMENT_ROOT']."/html/template/basic_footer.php";
        $pagina = "";
        $parse = true;
        
        if ($this->container["objeto"]->metadados['cod_pele'] > 0)
        {
            if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/header.php"))
            {
                $header = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/header.php";
            }
            if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/footer.php"))
            {
                $footer = $_SERVER['DOCUMENT_ROOT']."/html/skin/".$this->container["objeto"]->metadados['prefixopele']."/footer.php";
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
        elseif (file_exists(__DIR__."/../includes/error404.php"))
        {
            $pagina = __DIR__."/../includes/error404.php";
            $parse = false;
        }
        else
        {
            $this->headerHtml(403, "Visualização não permitida");
        }
        
        $_SESSION["escondetitulo"] = true;

        // xd("PAgina 550 - ");
        
        if ($header != "")
        {
            $this->container["parse"]->start($header);
        }
        if ($parse === true)
        {
            $this->container["parse"]->start($pagina);
        }
        else
        {
            include($pagina);
        }
        if ($footer != "")
        {
            $this->container["parse"]->start($footer);
        }
        
        exit();
    }

		
}
?>
