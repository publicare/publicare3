<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @file Includes.php
 * @description Classe responsável por gerenciar os includes da aplicação
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

class Includes
{
    
    private $_scripts = array();
    private $_conteudo = "";
    private $_arquivos = array();
    private $_tipo;
    private $_ext;
    private $_nome;
    public $page;
    
    function __construct($scripts = array(), $tipo="js", $ext="")
    {
        $this->_tipo = $tipo;
        $this->_ext = $ext;
        $this->adicionarArquivos($scripts);
        // xd($this->_arquivos);
    }
    
    public function adicionarArquivos($scripts = array())
    {
        global $PBLCONFIG;
        
//        xd( $PBLCONFIG["portal"]["pblpath"]);
        
        $this->_scripts = $scripts;
        $pathorigem = __DIR__."/../assets/";
        // xd(__DIR__);
        if (count($this->_scripts) > 0)
        {
            foreach ($this->_scripts as $script)
            {
                $path = $pathorigem;
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