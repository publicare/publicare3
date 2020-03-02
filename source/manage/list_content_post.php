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