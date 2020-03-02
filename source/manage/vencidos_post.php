<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo vencidos_post.php é responsável pela execução de ações em objetos vencidos
 * @copyright GPL © 2007
 * @package publicare/manage
 *
 * Este arquivo é parte do programa Publicare
 * Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
 * como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
 * de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
 */

global $_page;

foreach($_POST['objlist'] as $obj)
{
    if ($_SESSION['usuario']['perfil']==_PERFIL_ADMINISTRADOR) $_page->_administracao->ApagarEmDefinitivo($obj);
    elseif ($_SESSION['usuario']['perfil']==_PERFIL_EDITOR) $_page->_administracao->ApagarObjeto($obj); 
}
	
header("Location:" . $_page->config["portal"]["url"] . "/do/vencidos/".$_page->_objeto->Valor('cod_objeto').'.html');

