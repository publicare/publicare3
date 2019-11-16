<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Classe AdminObjeto é responsável pela manipulação dos objetos por parte dos internautas
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

// inicia sessao caso não esteja iniciada ainda
if (!isset($_SESSION)) session_start();

// inclui funcoes requeridas pelo publicare
require ("funcoes.php");

// define timezone
date_default_timezone_set(_TZ);

// inicia tratamento de URL
$url = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, "UTF-8");

//xd($url);

// removendo index.php da url
$url = preg_replace('/index\.php/', '', $url);
// removendo slash repetido
$url = preg_replace('/(\/)\1+/', '/', $url);
$_SERVER['REQUEST_URI'] = $url;
// retira variaveis passadas por _GET da url
$aurl = preg_split("[\?]", $url);
$url = $aurl[0];

$vurlpbl = preg_split("[\/]", _URL);


// inclusao das classes publicare
require ("constantes.php");
require ("lib/adodb/adodb-exceptions.inc.php");
require ("lib/adodb/adodb.inc.php");
require ("data.php");

// iniciando banco de dados
$_db = new DBLayer();

$incluir = "";
$amigavel = "";
$action = "";
$path = $_SERVER["DOCUMENT_ROOT"];
$cod_root = _ROOT;

// caso tenha array de dominios definido, verifica codigo objeto root do dominio
if (isset($_dominios))
{
    $dominio = htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES, "UTF-8");
    foreach ($_dominios as $dom => $dom1) 
    {
        if ($dominio == $dom) $cod_root = $_dominios[$dominio];
    }
}

$cod_objeto = $cod_root;
$cod_blob = 0;
$url = defined("_PASTA")?str_ireplace(_PASTA, "", $url):$url;
$url = preg_replace("[\/+]", "/", $url);
$arrUrl = preg_split("[\/]", $url);

//xd($arrUrl);

if (isset($arrUrl[1]))
{
    switch ($arrUrl[1])
    {
        // blob
        case "blob":
            $cod_blob = isset($arrUrl[3])?$arrUrl[3]:$cod_blob;
            $action = "/blob/".$arrUrl[2];
            break;
        // includes
        case "include":
            include('includes/' . $arrUrl[2] . '.php');
            exit();
            break;

        // formulario de login
        case "login":
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/html/template/view_login.php"))
            {
                $action = "/html/login";
                if (isset($arrUrl[3])) 
                {
                    $cod_objeto = identificaCodigoObjeto($arrUrl[3], $cod_root);
                }
            }
            else
            {
                $incluir = "includes/login.php";
            }
            break;

        // chamando arquivos pasta objects
        case "html":
            if ($arrUrl[2] == "objects")
            {
                $tempFile = "";
                for ($i = 3; $i < count($arrUrl); $i++)
                {
                    $tempFile .= "/" . $arrUrl[$i];
                }
                $incluir = $path . "/html/objects" . $tempFile;
            }
            break;

        // /content/view
        case "content":
            if ($arrUrl[2] == "view")
            {
                $action = "/content/view";
                $cod_objeto = identificaCodigoObjeto($arrUrl[3], $cod_root);
            }
            break;

        // manage ou do
        case "manage":
        case "do":
            $action = "/" . $arrUrl[1] . "/" . $arrUrl[2];
            if (isset($arrUrl[3]))
            {
                $cod_objeto = identificaCodigoObjeto($arrUrl[3], $cod_root);
            }
//            xd($cod_objeto);
            break;

        // nenhum caso conhecido, tenta url amigavel
        default:
            $action = "/content/view";
            // remove extensoes
            $temp = preg_split("[\.]", $arrUrl[1]);
            $arrUrl[1] = $temp[0];
            // garante url sem caracteres especiais
            $arrUrl[1] = limpaString($arrUrl[1]);
            // tudo para minusculo
            $arrUrl[1] = strtolower($arrUrl[1]);
            $amigavel = $arrUrl[1];
            break;
    }
}
else
{
    $action = "/content/view";
}

// verifica se eh url amigavel
if ($amigavel != "")
{
    // procura no banco pela url amigavel
    $sql = "select cod_objeto from objeto where url_amigavel='" . $amigavel . "'";
    $rs = $_db->ExecSQL($sql, 0, 1);
    while ($row = $rs->FetchRow())
    {
        $cod_objeto = (int)$row["cod_objeto"];
    }
}

// verifica existencia de action
if ($action=="" || isset($_GET["action"]) || isset($_POST["action"]))
{
    // se nao tiver, verifica action em get
    if (isset($_GET["action"])) $action = htmlspecialchars ($_GET["action"], ENT_QUOTES, "UTF-8");
    // se nao tiver em get, verifica em post
    elseif (isset($_POST["action"])) $action = htmlspecialchars ($_POST["action"], ENT_QUOTES, "UTF-8");
    // se nao tiver em post, define action como "/content/view"
    else $action = "/content/view";
}

$_page = new Pagina($_db, $cod_objeto, $cod_blob);

// se for inclusao de arquivo, inclui o mesmo e termina execucao
if ($incluir != "") {
    $incluir = str_replace("\.\.\/", "", $incluir);
    include($incluir);
    exit();
}
