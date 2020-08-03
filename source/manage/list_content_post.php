<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo
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

global $_page;

$netRedirect = "list_content";
if (isset($_POST['objlist']) && is_array($_POST['objlist']))
{
    foreach($_POST['objlist'] as $obj)
    {
        if (isset($_POST['delete'])) 
        {
            $_page->_administracao->ApagarObjeto($obj);
        }
        if (isset($_POST['duplicate']))
        {
            $_page->_administracao->DuplicarObjeto($obj);
        }
        if (isset($_POST['copy']))
        {
            $_page->_administracao->CopiarObjetoParaPilha($obj);
        }
        if (isset($_POST['publicar']))
        {
            $_page->_administracao->PublicarObjeto('Objeto publicado atrav&eacute;s da a&ccedil;&atilde;o listar conte&uacute;do',$obj);
        }
        if (isset($_POST['publicar_pendentes']))
        {
            $netRedirect = "pendentes";
            $_page->_administracao->PublicarObjeto('Objeto publicado atrav&eacute;s da lista de objetos pendentes.',$obj);
        }		
        if (isset($_POST['despublicar']))
        {
            $_page->_administracao->DesPublicarObjeto('Objeto despublicado atrav&eacute;s da a&ccedil;&atilde;o listar conte&uacute;do',$obj);
        }
        if (isset($_POST['solicitar']))
        {
            $_page->_administracao->SubmeterObjeto('Objeto solicitado atrav&eacute;s da a&ccedil;&atilde;o listar conte&uacute;do',$obj);
        }
    }
}
header ("Location:".$_page->config["portal"]["url"].'/do/'.$netRedirect.'/'.$_POST['return_obj'].'.html');
?>