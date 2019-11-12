<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo list_content_post.php é responsável por executar as funcoes da listagem
 * @copyright GPL © 2007
 * @package publicare
 *
 * MCTI - Ministério da Ciência, Tecnologia e Inovação - www.mcti.gov.br
 * ANTT - Agência Nacional de Transportes Terrestres - www.antt.gov.br
 * EPL - Empresa de Planejamento e Logística - www.epl.gov.br
 * LogicBSB - LogicBSB Sistemas Inteligentes - www.logicbsb.com.br
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
foreach($_POST['objlist'] as $obj)
{
    if (isset($_POST['delete'])) 
    {
        $_page->_administracao->ApagarObjeto($_page, $obj);
    }
    if (isset($_POST['duplicate']))
    {
        $_page->_administracao->DuplicarObjeto($_page, $obj);
    }
    if (isset($_POST['copy']))
    {
        $_page->_administracao->CopiarObjetoParaPilha($_page, $obj);
    }
    if (isset($_POST['publicar']))
    {
        $_page->_administracao->PublicarObjeto($_page, 'Objeto publicado atrav&eacute;s da a&ccedil;&atilde;o listar conte&uacute;do',$obj);
    }
    if (isset($_POST['publicar_pendentes']))
    {
        $netRedirect = "pendentes";
        $_page->_administracao->PublicarObjeto($_page, 'Objeto publicado atrav&eacute;s da lista de objetos pendentes.',$obj);
    }		
    if (isset($_POST['despublicar']))
    {
        $_page->_administracao->DesPublicarObjeto($_page, 'Objeto despublicado atrav&eacute;s da a&ccedil;&atilde;o listar conte&uacute;do',$obj);
    }
    if (isset($_POST['solicitar']))
    {
        $_page->_administracao->SubmeterObjeto($_page, 'Objeto solicitado atrav&eacute;s da a&ccedil;&atilde;o listar conte&uacute;do',$obj);
    }
}
header ("Location:"._URL.'/index.php/do/'.$netRedirect.'/'.$_POST['return_obj'].'.html');
?>