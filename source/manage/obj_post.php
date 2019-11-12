<?php
// AJUSTES
global $_page, $cod;

// força o código do status para despublicado
$_POST['cod_status'] = 1;
// remover barras duplas, para evitar erro
$_POST['script_exibir'] = str_replace("//", "/", $_POST['script_exibir']); // Arruma uma falha

// chama a execução de scripts antes de gravar o objeto
$execAntes = $_page->_adminobjeto->ExecutaScript($_page, $_POST['cod_classe'], $_POST['cod_pele'], 'antes');
$palavra = "criação";

$cod = 0;

if ($_POST['op'] == "edit")
{
    $cod = $_page->_administracao->AlterarObjeto($_page, $_POST);
    $palavra = "edição";
}
else
{
    $cod = $_page->_administracao->CriarObjeto($_page, $_POST);
}

// pega dados do objeto gravado
$data = $_page->_adminobjeto->PegaDadosObjetoPeloID($_page, $cod);

if (isset($_POST["gravarepublicar"]) && $_SESSION['usuario']['perfil'] <= _PERFIL_EDITOR)
{
    $_page->_administracao->PublicarObjeto($_page, 'Objeto publicado durante a '.$palavra, $cod);
}
elseif (isset($_POST["gravaresolicitar"]))
{
    $_page->_administracao->SubmeterObjeto($_page, 'Objeto solicitado durante a '.$palavra, $cod);
}
elseif ($_POST['op']=="edit")
{
    $_page->_administracao->RemovePendencia($_page, 'Objeto editado ap&oacute;s solicita&ccedil;&atilde;o. Status redefinido pelo sistema.', $cod);
}

// chama a execução de scripts depois de gravar o objeto
$execDepois = $_page->_adminobjeto->ExecutaScript($_page, $_POST['cod_classe'], $_POST['cod_pele'], 'depois');

$local = _URL."/".$data["url_amigavel"];

if (isset($_POST["gravaroutro"]))
{
    $local = _URL."/manage/new_".$data['prefixoclasse']."/".$data['cod_pai'].".html";
}

header("Location: ".$local);
exit();
