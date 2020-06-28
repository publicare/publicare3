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
// AJUSTES
global $_page, $cod;

// força o código do status para despublicado
$_POST['cod_status'] = 1;
// remover barras duplas, para evitar erro
$_POST['script_exibir'] = preg_replace("[\/+]", "/", $_POST['script_exibir']); // Arruma uma falha

// chama a execução de scripts antes de gravar o objeto
$palavra = "criação";

$cod = 0;
$local = $_page->config["portal"]["url"];
$acaoobj = filter_input(INPUT_POST, 'op', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$publicar = 0;

if (isset($_POST["gravaresolicitar"]))
{
    $publicar = 1;
}
elseif (isset($_POST["gravarepublicar"]))
{
    $publicar = 2;
}

$obj = $_page->_administracao->GravarObjeto($_POST, $acaoobj, $publicar, $cod);
$local .= $obj["obj"]->Valor("url");

if (isset($_POST["gravaroutro"]))
{
    $local = $_page->config["portal"]["url"]."/do/new_".$obj["obj"]->Valor('prefixoclasse')."/".$obj["obj"]->Valor('cod_pai').".html";
}

header("Location: ".$local);
exit();
