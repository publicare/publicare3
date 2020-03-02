<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo rejeitar_post.php recebe formulario para rejeitar publicação de objeto e executa funcoes
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

$mensagem = (string)filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$_page->_administracao->RejeitarObjeto("Rejeitada publicação da versão ".$_page->_objeto->Valor("versao").($mensagem!=""?" - Comentários: ".$mensagem:""), $_page->_objeto->Valor('cod_objeto'));
header("Location:".$_page->config["portal"]["url"].$_page->_objeto->Valor('url'));

