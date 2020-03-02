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

if (isset($_POST['objlist']) && is_array($_POST['objlist']) && count($_POST['objlist'])>0)
{
    rsort($_POST['objlist']);

    foreach($_POST['objlist'] as $obj)
    {
            $_page->_administracao->RecuperarObjeto($obj);
    }
}
header ("Location: ".$_page->config["portal"]["url"]."/do/recuperar/".$_page->_objeto->Valor('cod_objeto').".html");

