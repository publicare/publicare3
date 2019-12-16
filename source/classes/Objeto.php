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

    /**
     * Método construtor da classe objeto
     * @param Page $_page - Referencia do objeto page
     * @param mixed $cod_objeto - Pode ser string ou inteiro
     * @return boolean
     */
    function __construct(&$_page, $cod_objeto=-1)
    {
        $this->ArrayMetadados=$_page->_db->metadados;
        if ($cod_objeto!=-1)
        {
            if (is_numeric($cod_objeto))
            {
                $dados = $_page->_adminobjeto->PegaDadosObjetoPeloID($_page, $cod_objeto);
            }
            else
            {
                $dados = $_page->_adminobjeto->PegaDadosObjetoPeloTitulo($_page, $cod_objeto);
            }

            if (is_array($dados) && sizeof($dados)>2)
            {
                $this->povoar($_page, $dados);
                $this->CaminhoObjeto = explode(",",$this->PegaCaminho($_page));
                return true;
            }

        }

        //Nao conseguiu selecionar o objeto
        return false;
    }

    /**
     * Povoa objeto do tipo Objeto com os metadados, datas de publicação e validade, url amigavel e tags
     * @param Page $_page - Referencia do objeto page
     * @param array $dados - Array com os metadados
     */
    function povoar(&$_page, $dados)
    {
        $this->metadados = $dados;
        $this->metadados['data_publicacao'] = ConverteData($this->metadados['data_publicacao'],1);
        $this->metadados['data_validade'] = ConverteData($this->metadados['data_validade'],1);
        //INCLUIDO O TITULO DO OBJETO NA URL
        if ($this->metadados['url_amigavel'] && $this->metadados['url_amigavel']!="") $this->metadados['url'] = "/".$this->metadados['url_amigavel'];
        else $this->metadados['url']='/index.php/content/view/'.$this->metadados['cod_objeto']."/".limpaString($this->metadados['titulo']).".html";
        $this->metadados['tags'] = $_page->_adminobjeto->PegaTags($_page, $this->metadados['cod_objeto']);
    }

		function PegaCaminho(&$_page)
		{
			return $_page->_adminobjeto->PegaCaminhoObjeto($_page, $this->metadados['cod_objeto']);
		}

		function PegaCaminhoComTitulo(&$_page)
		{
			$resultado=$_page->_adminobjeto->PegaCaminhoObjetoComTitulo($_page, $this->metadados['cod_objeto']);
			return $resultado;
		}

		function Publicado()
		{
			return ($this->metadados['cod_status']==_STATUS_PUBLICADO);
		}

		function Valor(&$_page, $campo)
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
				return trim ($this->Propriedade($_page, $campo));
			}
		}
		
    /**
     * Retorna URL para download do blob. Alias de DownloadBlob.
     * @param Page $_page - Referencia do objeto page
     * @param string $campo - nome da propriedade que contem o blob
     * @return string
     */
    function LinkDiretoBlob(&$_page, $campo)
    {
        return $this->DownloadBlob($_page, $campo);
    }

    /**
     * Retorna URL para download do blob. Alias de DownloadBlob.
     * @param Page $_page - Referencia do objeto page
     * @param string $campo - nome da propriedade que contem o blob
     * @return string
     */
    function LinkBlob(&$_page, $campo)
    {
        return $this->DownloadBlob($_page, $campo);
    }

    /**
     * Retorna URL para realizar download do blob atraves da funcionalidade downloadblob
     * @param Page $_page - Referencia do objeto page
     * @param string $campo - nome da propriedade que contem o blob
     * @return string
     */
    function DownloadBlob(&$_page, $campo)
    {
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $_page->_adminobjeto->PegaPropriedades($_page, $this->metadados['cod_objeto']);
        }
//        return _URL."/html/objects/_downloadblob.php?cod_blob=".$this->propriedades[$campo]['cod_blob'];
        return _URL."/blob/baixar/".$this->propriedades[$campo]['cod_blob'];
    }
    
    /**
     * Exibe blob na tela utilizando funcionalidade viewblob.
     * Utilizado somente para imagens
     * @param Page $_page - Referência de objeto da classe Pagina
     * @param string $campo - Nome da propriedade blob
     * @param integer $width - Largura da imagem
     * @param integer $height - Altura da imagem
     * @return bytes - Retorna bytes da imagem para exibição
     */
    function ExibirBlob(&$_page, $campo, $width=0, $height=0)
    {
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
                $this->propriedades = $_page->_adminobjeto->PegaPropriedades($_page, $this->metadados['cod_objeto']);
        }
//        return _URL."/html/objects/_viewblob.php?cod_blob=".$this->propriedades[$campo]['cod_blob']."&width=$width&height=$height";
        return _URL."/blob/ver/".$this->propriedades[$campo]['cod_blob']."?w=".$width."&h=".$height;
    }

    /**
     * Exibe miniatura das imagens na tela utilizando funcionalidade viewthumb.
     * Utilizado somente para imagens
     * @param Page $_page - Referência de objeto da classe Pagina
     * @param string $campo - Nome da propriedade blob
     * @param integer $width - Largura da imagem
     * @param integer $height - Altura da imagem
     * @return bytes - Retorna bytes da imagem para exibição
     */
    function ExibirThumb(&$_page, $campo, $width=0, $height=0)
    {
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $_page->_adminobjeto->PegaPropriedades($_page, $this->metadados['cod_objeto']);
        }
        
        $largura = $width>0?$width:_THUMBWIDTH;
//        return _URL."/html/objects/_viewthumb.php?cod_blob=".$this->propriedades[$campo]['cod_blob']."&width=$width&height=$height";
        return _URL."/blob/ver/".$this->propriedades[$campo]['cod_blob']."?w=".$largura."&h=".$height;
    }

		function ValorParaEdicao(&$_page, $campo)
		{
			if (in_array($campo,$this->ArrayMetadados))
			{
				return (trim($this->metadados[$campo]));
			}
			else
			{
				return (trim($this->Propriedade($_page, $campo)));
			}
		}

		function PegaListaDePropriedades(&$_page)
		{
			if (!is_array($this->propriedades))
			{
				$this->propriedades = $_page->_adminobjeto->PegaPropriedades($_page, $this->metadados['cod_objeto']);
			}
			return $this->propriedades;
		}

		function Propriedade(&$_page, $campo)
		{
                    $campo = strtolower($campo);
                    if (!isset($this->propriedades) || !is_array($this->propriedades))
                    {
                        $this->propriedades = $_page->_adminobjeto->PegaPropriedades($_page, $this->metadados['cod_objeto']);
                    }
                    if (isset($this->propriedades[$campo])) return $this->propriedades[$campo]['valor'];
                    else return "";
		}

    /**
     * Retorna o tamanho do blob em Bytes
     * @param Page $_page - Referencia do objeto page
     * @param string $campo - Nome da propriedade que contem o blob
     * @return int
     */
    function TamanhoBlob(&$_page, $campo)
    {
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $_page->_adminobjeto->PegaPropriedades($_page, $this->metadados['cod_objeto']);
        }
        return ($this->propriedades[$campo]['tamanho_blob']);
    }

		function TipoBlob(&$_page, $campo)
		{
                    if (!isset($this->propriedades) || !is_array($this->propriedades))
                    {
                        $this->propriedades = $_page->_adminobjeto->PegaPropriedades($_page, $this->metadados['cod_objeto']);
                    }
                    return ($this->propriedades[$campo]['tipo_blob']);
		}
		
		function IconeBlob(&$_page, $campo)
		{
			$arquivo ='/html/imagens/icnx_'.$this->TipoBlob($_page, $campo).'.gif';
			if (file_exists($_SERVER['DOCUMENT_ROOT'].$arquivo))
			{
				return $arquivo;
			}
			else
			{
				return '/html/imagens/icnx_generic.gif';
			}
		
		}

		function PegaListaDeFilhos(&$_page, $classe='*',$ordem='peso,titulo',$inicio=-1,$limite=-1)
		{
			if ($this->metadados['temfilhos'])
			{
				$this->filhos = $_page->_adminobjeto->ListaFilhos($_page, $this->metadados['cod_objeto'], $classe, $ordem, $inicio, $limite);
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

		function EFilho (&$_page, $cod_pai)
		{
			//echo "cod_objeto:".$this->Valor("cod_objeto");
			//exit;
			return $_page->_adminobjeto->EFilho($_page, $this->Valor("cod_objeto"), $cod_pai);
		}
                
	}

?>