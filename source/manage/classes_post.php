<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Arquivo classes_post.php - é responsável pelas ações no gerenciamento de classes
* @copyright GPL © 2007
* @package publicare/manage
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

$cod_classe = isset($_POST['cod_classe'])?(int)htmlspecialchars($_POST["cod_classe"], ENT_QUOTES, "UTF-8"):0;
$old_prefixo = isset($_POST['old_prefixo'])?htmlspecialchars($_POST["old_prefixo"], ENT_QUOTES, "UTF-8"):"";
$old_indexar = isset($_POST['old_indexar'])?(int)htmlspecialchars($_POST["old_indexar"], ENT_QUOTES, "UTF-8"):1;
$old_temfilhos = isset($_POST['old_temfilhos'])?(int)htmlspecialchars($_POST["old_temfilhos"], ENT_QUOTES, "UTF-8"):1;
$nome_classe = isset($_POST['nome'])?trim(htmlspecialchars($_POST["nome"], ENT_QUOTES, "UTF-8")):"";
$prefixo_classe = isset($_POST['prefixo'])?trim(mb_strtolower(htmlspecialchars($_POST["prefixo"], ENT_QUOTES, "UTF-8"), "UTF-8")):"";
$descricao_classe = isset($_POST['descricao'])?trim(htmlspecialchars($_POST["descricao"], ENT_QUOTES, "UTF-8")):"";
$temfilhos_classe = isset($_POST['temfilhos'])?(int)htmlspecialchars($_POST["temfilhos"], ENT_QUOTES, "UTF-8"):"";
$indexar_classe = isset($_POST['indexar'])?(int)htmlspecialchars($_POST["indexar"], ENT_QUOTES, "UTF-8"):"";
$ic_classe = isset($_POST['ic_classe'])?htmlspecialchars($_POST["ic_classe"], ENT_QUOTES, "UTF-8"):"";

//xd($_POST);

// Apaga a classe
if (isset($_POST['apagar_classe']) && $_POST['apagar_classe'] == "1" && $cod_classe > 0) {
    $_page->_administracao->ApagarClasse($_page, $_POST['cod_classe']);
    $cod_classe = 0;
}
// criar / editar classe
elseif ($_POST['btn_gravar'] && $_POST['btn_gravar']=="Gravar")
{
    $numeroprops = isset($_POST["numeroPropriedades"])?(int)htmlspecialchars($_POST["numeroPropriedades"], ENT_QUOTES, "UTF-8"):0;
    
    // Montando array com propriedades
    $props = array();
    for ($i=1; $i <= $numeroprops; $i++)
    {
        $ativa = isset($_POST["prop_" . $i . "_ativa"])?(int)htmlspecialchars($_POST["prop_" . $i . "_ativa"], ENT_QUOTES, "UTF-8"):0;
        $nomeatual = isset($_POST["prop_" . $i . "_nomeatual"])?htmlspecialchars($_POST["prop_" . $i . "_nomeatual"], ENT_QUOTES, "UTF-8"):"";
        $nome = isset($_POST["prop_" . $i . "_nome"])?mb_strtolower(htmlspecialchars($_POST["prop_" . $i . "_nome"], ENT_QUOTES, "UTF-8"), "UTF-8"):"";
        $rotulo = isset($_POST["prop_" . $i . "_rotulo"])?trim(htmlspecialchars($_POST["prop_" . $i . "_rotulo"], ENT_QUOTES, "UTF-8")):"";
        $tipodado = isset($_POST["prop_" . $i . "_tipodado"])?(int)htmlspecialchars($_POST["prop_" . $i . "_tipodado"], ENT_QUOTES, "UTF-8"):1;
        $bol_1 = isset($_POST["prop_" . $i . "_bol_1"])?trim(htmlspecialchars($_POST["prop_" . $i . "_bol_1"], ENT_QUOTES, "UTF-8")):"Sim";
        $bol_0 = isset($_POST["prop_" . $i . "_bol_0"])?trim(htmlspecialchars($_POST["prop_" . $i . "_bol_0"], ENT_QUOTES, "UTF-8")):"Não";
        $cod_referencia_classe = isset($_POST["prop_" . $i . "_cod_referencia_classe"])?(int)htmlspecialchars($_POST["prop_" . $i . "_cod_referencia_classe"], ENT_QUOTES, "UTF-8"):0;
        $campo_ref = isset($_POST["prop_" . $i . "_campo_ref"])?trim(htmlspecialchars($_POST["prop_" . $i . "_campo_ref"], ENT_QUOTES, "UTF-8")):"";
        $valorpadrao = isset($_POST["prop_" . $i . "_valorpadrao"])?trim(htmlspecialchars($_POST["prop_" . $i . "_valorpadrao"], ENT_QUOTES, "UTF-8")):"";
        $posicao = isset($_POST["prop_" . $i . "_posicao"])?(int)htmlspecialchars($_POST["prop_" . $i . "_posicao"], ENT_QUOTES, "UTF-8"):0;
        $seguranca = isset($_POST["prop_" . $i . "_seguranca"])?(int)htmlspecialchars($_POST["prop_" . $i . "_seguranca"], ENT_QUOTES, "UTF-8"):3;
        $descricao = isset($_POST["prop_" . $i . "_descricao"])?trim(htmlspecialchars($_POST["prop_" . $i . "_descricao"], ENT_QUOTES, "UTF-8")):"";
        $obrigatorio = isset($_POST["prop_" . $i . "_obrigatorio"])?(int)htmlspecialchars($_POST["prop_" . $i . "_obrigatorio"], ENT_QUOTES, "UTF-8"):0;
        
        $nomefinal = $nomeatual!=""?$nomeatual:$nome;
        
        $props[$nomefinal] = array("ativa" => $ativa,
            "nome" => $nome,
            "nomeatual" => $nomeatual,
            "rotulo" => $rotulo,
            "tipodado" => $tipodado,
            "bol1" => $bol_1,
            "bol0" => $bol_0,
            "ref_classe" => $cod_referencia_classe,
            "ref_campo" => $campo_ref,
            "padrao" => $valorpadrao,
            "posicao" => $posicao,
            "seguranca" => $seguranca,
            "descriao" => $descricao,
            "obrigatorio" => $obrigatorio);
    }
    
    $dados_classe = array("nome" => $nome_classe,
        "prefixo" => $prefixo_classe,
        "descricao" => $descricao_classe,
        "temfilhos" => $temfilhos_classe,
        "index" => $indexar_classe);
    
    // Editar classe
    if ($cod_classe > 0) $_page->_administracao->AtualizarClasse($_page, $cod_classe, $dados_classe);
    // Criar classe
    else $cod_classe = $_page->_administracao->CriarClasse($_page, $dados_classe);
        
    // Recupera dados da classe
    $classinfo = $_page->_administracao->PegaInfoDaClasse($_page, $cod_classe);
    
    // Verifica / apaga / altera / adiciona propriedades
    foreach ($props as $propp)
    {
        $dados = array("tipodado"=>$propp["tipodado"], 
            "nome" => $propp["nome"],
            "rotulo" => $propp["rotulo"],
            "descricao" => $propp["descriao"],
            "valorpadrao" => $propp["padrao"],
            "rot1booleano" => $propp["bol1"],
            "rot2booleano" => $propp["bol0"],
            "codrefclasse" => $propp["ref_classe"],
            "camporef" => $propp["ref_campo"],
            "obrigatorio" => $propp["obrigatorio"],
            "seguranca" => $propp["seguranca"],
            "posicao" => $propp["posicao"]);

        // propriedade existe
        if (isset($classinfo["prop"][$propp["nome"]]))
        {
            $propex = $classinfo["prop"][$propp["nome"]];
            $cod_propriedade = $propex["cod_propriedade"];
            // apagar propriedade
            if ($propp["ativa"] == 0)
            {
                $_page->_administracao->ApagarPropriedadeDaClasse($_page, $cod_propriedade);
            }
            // alterar propriedade
            else
            {
                $_page->_administracao->AtualizarDadosDaPropriedade($_page, $cod_propriedade, $dados);
            }
        }
        // nova propriedade
        else
        {
            $_page->_administracao->AcrescentarPropriedadeAClasse($_page, $cod_classe, $dados);
        }
    }
    
    // Cria view de modelo para classe
//    $_page->_administracao->CriarTemplateClasse($_page, $cod_classe);
    
    // Atualiza informações sobre classes que pode conter
    if (isset($_POST["podeconter"])) $_page->_administracao->MontaRelacionamentoClasses($_page, $cod_classe, $_POST["podeconter"], 1);
    
    // Atualiza informação sobre onde pode ser criado
    if (isset($_POST["criadoem"])) $_page->_administracao->MontaRelacionamentoClasses($_page, $cod_classe, $_POST["criadoem"], 2);
    
    // Atualiza lista de objetos onde pode ser criada
    if (isset($_POST["objetos"])) 
    {
        $_page->_administracao->MontaRelacionamentoClassesObjetos($_page, $cod_classe, $_POST["objetos"], $_POST["objetosurls"]);
    }
    
    if (isset($_POST["apagar_icone"]) && $_POST["apagar_icone"] == "apagar")
    {
        $_page->_blob->apagaIconeClasse($_page, $prefixo_classe);
    }
    
    if (isset($_FILES["ic_classe"]["name"]) && !empty($_FILES["ic_classe"]["name"])) 
    {
        $_page->_blob->gravarIconeClasse($_page, $_FILES, $prefixo_classe);
    }
}

// limpa cache
$_page->_administracao->cacheFlush($_page);

$_SESSION['classesPrefixos'] = array();
$_SESSION['classesNomes'] = array();
$_SESSION['classes'] = array();
$_SESSION['classesIndexaveis'] = array();

$header = "Location:" . _URL . "/do/classes/" . $_page->_objeto->Valor($_page, "cod_objeto") . ".html";
header($header);

exit();
