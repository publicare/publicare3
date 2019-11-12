<?
global $_page;

	$_page->_administracao->RejeitarObjeto($_page, $_POST['message'], $_page->_objeto->Valor($_page, 'cod_objeto'));
	header("Location:".$_page->_objeto->Valor($_page, 'url')."?PortalMessage=Status+Alterado");
?>
