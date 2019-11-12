<?
global $_page;

	$_page->_administracao->SubmeterObjeto($_page, $_POST['message'], $_page->_objeto->Valor($_page, 'cod_objeto'));
	header("Location:".$_page->_objeto->Valor($_page, 'url')."?PortalMessage=Status+Alterado");
?>
