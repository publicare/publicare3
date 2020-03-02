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

class Objeto
{
    public $ponteiro=0;
    public $quantidade=0;
    public $CaminhoObjeto;
    public $metadados;
    public $propriedades;
    public $ArrayMetadados;
    
    public $_page;

    /**
     * Método construtor da classe objeto
     * @param Pagina $_page - Referencia do objeto page
     * @param mixed $cod_objeto - Pode ser string ou inteiro
     * @return boolean
     */
    function __construct(&$_page, $cod_objeto=-1)
    {
        $this->_page = $_page;
        
        $this->ArrayMetadados = $_page->_db->metadados;
        if ($cod_objeto!=-1)
        {
            if (is_numeric($cod_objeto))
            {
                $dados = $this->_page->_adminobjeto->PegaDadosObjetoPeloID($cod_objeto);
            }
            else
            {
                $dados = $this->_page->_adminobjeto->PegaDadosObjetoPeloTitulo($cod_objeto);
            }

            if (is_array($dados) && sizeof($dados)>2)
            {
                $this->povoar($dados);
                $this->CaminhoObjeto = explode(",", $this->PegaCaminho());
                return true;
            }

        }

        //Nao conseguiu selecionar o objeto
        return false;
    }

    /**
     * Povoa objeto do tipo Objeto com os metadados, datas de publicação e validade, url amigavel e tags
     * @param array $dados - Array com os metadados
     */
    function povoar($dados)
    {
        $this->metadados = $dados;
        $this->metadados['data_publicacao'] = ConverteData($this->metadados['data_publicacao'],1);
        $this->metadados['data_validade'] = ConverteData($this->metadados['data_validade'],1);
        //INCLUIDO O TITULO DO OBJETO NA URL
        if ($this->metadados['url_amigavel'] && $this->metadados['url_amigavel']!="") $this->metadados['url'] = "/".$this->metadados['url_amigavel'];
        else $this->metadados['url']='/index.php/content/view/'.$this->metadados['cod_objeto']."/".limpaString($this->metadados['titulo']).".html";
        $this->metadados['tags'] = $this->_page->_adminobjeto->PegaTags($this->metadados['cod_objeto']);
    }

    /**
     * Retorna caminho do objeto, em string separado por ","
     * @return string
     */
    function PegaCaminho()
    {
//        xd($_SESSION);
        return $this->_page->_adminobjeto->PegaCaminhoObjeto($this->metadados['cod_objeto']);
    }

    /**
     * Retorna array com caminho do objeto
     * @return array
     */
    function PegaCaminhoComTitulo()
    {
        $resultado=$this->_page->_adminobjeto->PegaCaminhoObjetoComTitulo($this->metadados['cod_objeto']);
        return $resultado;
    }

    /**
     * Verifica se objeto est´qa com status publicado
     * @return bool
     */
    function Publicado()
    {
        return ($this->metadados['cod_status']==_STATUS_PUBLICADO);
    }

    /**
     * Retorna valor da propriedade ou metadado
     * @param string $campo
     * @return type
     */
    function Valor($campo)
    {
        if (in_array($campo,$this->ArrayMetadados))
        {
            return trim($this->metadados[$campo]);
        }
        elseif ($campo=="tags")
        {

        }
        else
        {
            return trim ($this->Propriedade($campo));
        }
    }
		
    /**
     * Retorna URL para download do blob. Alias de DownloadBlob.
     * @param string $campo - nome da propriedade que contem o blob
     * @return string
     */
    function LinkDiretoBlob($campo)
    {
        return $this->DownloadBlob($campo);
    }

    /**
     * Retorna URL para download do blob. Alias de DownloadBlob.
     * @param string $campo - nome da propriedade que contem o blob
     * @return string
     */
    function LinkBlob($campo)
    {
        return $this->DownloadBlob($campo);
    }

    /**
     * Retorna URL para realizar download do blob atraves da funcionalidade downloadblob
     * @param string $campo - nome da propriedade que contem o blob
     * @return string
     */
    function DownloadBlob($campo)
    {
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $this->_page->_adminobjeto->PegaPropriedades($this->metadados['cod_objeto']);
        }
        return $this->_page->config["portal"]["url"]."/blob/baixar/".$this->propriedades[$campo]['cod_blob'];
    }
    
    /**
     * Exibe blob na tela utilizando funcionalidade viewblob.
     * Utilizado somente para imagens
     * @param string $campo - Nome da propriedade blob
     * @param integer $width - Largura da imagem
     * @param integer $height - Altura da imagem
     * @return bytes - Retorna bytes da imagem para exibição
     */
    function ExibirBlob($campo, $width=0, $height=0)
    {
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
                $this->propriedades = $this->_page->_adminobjeto->PegaPropriedades($this->metadados['cod_objeto']);
        }
//        return _URL."/html/objects/_viewblob.php?cod_blob=".$this->propriedades[$campo]['cod_blob']."&width=$width&height=$height";
        return $this->_page->config["portal"]["url"]."/blob/ver/".$this->propriedades[$campo]['cod_blob']."?w=".$width."&h=".$height;
    }

    /**
     * Exibe miniatura das imagens na tela utilizando funcionalidade viewthumb.
     * Utilizado somente para imagens
     * @param string $campo - Nome da propriedade blob
     * @param integer $width - Largura da imagem
     * @param integer $height - Altura da imagem
     * @return bytes - Retorna bytes da imagem para exibição
     */
    function ExibirThumb($campo, $width=0, $height=0)
    {
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $this->_page->_adminobjeto->PegaPropriedades($this->metadados['cod_objeto']);
        }
        
        $largura = $width>0?$width:$this->_page->config["portal"]["largurathumb"];
//        return _URL."/html/objects/_viewthumb.php?cod_blob=".$this->propriedades[$campo]['cod_blob']."&width=$width&height=$height";
        return $this->_page->config["portal"]["url"]."/blob/ver/".$this->propriedades[$campo]['cod_blob']."?w=".$largura."&h=".$height;
    }

    function ValorParaEdicao($campo)
    {
        if (in_array($campo,$this->ArrayMetadados))
        {
            return (trim($this->metadados[$campo]));
        }
        else
        {
            return (trim($this->Propriedade($campo)));
        }
    }

    function PegaListaDePropriedades()
    {
        if (!is_array($this->propriedades))
        {
                $this->propriedades = $this->_page->_adminobjeto->PegaPropriedades($this->metadados['cod_objeto']);
        }
        return $this->propriedades;
    }

    function Propriedade($campo)
    {
        $campo = strtolower($campo);
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $this->_page->_adminobjeto->PegaPropriedades($this->metadados['cod_objeto']);
        }
        if (isset($this->propriedades[$campo])) return $this->propriedades[$campo]['valor'];
        else return "";
    }

    /**
     * Retorna o tamanho do blob em Bytes
     * @param string $campo - Nome da propriedade que contem o blob
     * @return int
     */
    function TamanhoBlob($campo)
    {
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $this->_page->_adminobjeto->PegaPropriedades($this->metadados['cod_objeto']);
        }
        return ($this->propriedades[$campo]['tamanho_blob']);
    }

    function TipoBlob($campo)
    {
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $this->_page->_adminobjeto->PegaPropriedades($this->metadados['cod_objeto']);
        }
        return ($this->propriedades[$campo]['tipo_blob']);
    }
		
    function IconeBlob($campo)
    {
        $arquivo ='/html/imagens/icnx_'.$this->TipoBlob($campo).'.gif';
        if (file_exists($_SERVER['DOCUMENT_ROOT'].$arquivo))
        {
            return $arquivo;
        }
        else
        {
            return '/html/imagens/icnx_generic.gif';
        }
    }

    function PegaListaDeFilhos($classe='*',$ordem='peso,titulo',$inicio=-1,$limite=-1)
    {
        if ($this->metadados['temfilhos'])
        {
            $this->filhos = $this->_page->_adminobjeto->ListaFilhos($this->metadados['cod_objeto'], $classe, $ordem, $inicio, $limite);
            $this->ponteiro = 0;
            $this->quantidade = count($this->filhos);
            return $this->quantidade;
        }
        else
            return false;
    }

    function PodeTerFilhos()
    {
        return $this->metadados['temfilhos'];
    }

    function PegaProximoFilho()
    {
        if ($this->ponteiro < $this->quantidade)
            return $this->filhos[$this->ponteiro++];
        else
            return false;
    }

    function VaiParaFilho($posicao)
    {
        if ($posicao>$this->quantidade)
            return false;
        else
        {
            $this->ponteiro=$posicao;
            return $this->filhos[$this->ponteiro++];
        }
    }

    function EFilho ($cod_pai)
    {
            //echo "cod_objeto:".$this->Valor("cod_objeto");
            //exit;
            return $this->_page->_adminobjeto->EFilho($this->Valor("cod_objeto"), $cod_pai);
    }
                
}