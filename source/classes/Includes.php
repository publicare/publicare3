<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Classe Includes é responsável pela manipulação dos objetos por parte dos internautas
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

class Includes
{
    
    private $_scripts = array();
    private $_conteudo = "";
    private $_arquivos = array();
    private $_tipo;
    private $_ext;
    private $_nome;
    
    function __construct($scripts = array(), $tipo="js", $ext="")
    {
        $this->_tipo = $tipo;
        $this->_ext = $ext;
        $this->adicionaArquivos($scripts);
    }
    
    public function adicionaArquivos($scripts = array())
    {
        $this->_scripts = $scripts;
        if (count($this->_scripts) > 0)
        {
            foreach ($this->_scripts as $script)
            {
                $path = _dirDefault."/includes/";
                if ($this->_tipo == "js") $path .= "javascript";
                if ($this->_tipo == "css") $path .= "css";
                if ($this->_tipo == "font") $path .= "fonts";
                $path .= "/".$script;
                $this->_nome = $script;
                if (file_exists($path) && is_readable($path))
                {
//                    $this->_conteudo .= "\n\n/*".$script."*/\n".file_get_contents($path);
                    $this->_conteudo .= file_get_contents($path);
                }
                else
                {
//                    echo $path;
                }
            }
        }
    }
    
    public function imprimeResultado()
    {
        if ($this->_conteudo != "")
        {
            if ($this->_tipo == "js") header("content-type: application/x-javascript");
            if ($this->_tipo == "css") header("content-type: text/css");
            if ($this->_tipo == "font")
            {
                if ($this->_ext == "svg")  header("content-type: image/svg+xml");
                else header("content-type: font/".$this->_ext);
//                header('Content-Disposition: inline; filename="'.$_this->nome.'"');
                header('Content-Disposition: inline; filename="'.$this->_nome.'"');
            }
                
            echo $this->_conteudo;
            $this->_conteudo = "";
        }
    }
    
}