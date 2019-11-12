<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo pilha_post.php é responsável por executar as funções da pilha
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

global $_page, $cod_objeto;

if (isset($_POST['copy'])) $_page->_administracao->CopiarObjeto($_page, $_POST['cod_objmanage'], $_page->_objeto->Valor($_page, "cod_objeto"));
if (isset($_POST['pastelink'])) $_page->_administracao->ColarComoLink($_page, $_POST['cod_objmanage'], $_page->_objeto->Valor($_page, "cod_objeto"));
if (isset($_POST['move'])) $_page->_administracao->MoverObjeto($_page, $_POST['cod_objmanage'], $_page->_objeto->Valor($_page, "cod_objeto"));
if (isset($_POST['clear'])) $_page->_administracao->LimparPilha($_page);
		
header ("Location:"._URL.'/index.php/do/list_content/'.$cod_objeto.'.html');
?>
