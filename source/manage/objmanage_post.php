<?
	include_once ("iniciar.php");

	if ($copy)
		$_page->objManage->ObjectPaste($cod_objmanage,$_page->obj->cod_object);
	if ($pastelink)
		$_page->objManage->ObjectPasteAsLink($cod_objmanage);
	if ($move)
		$_page->objManage->ObjectMove($cod_objmanage,$cod_object);
	if ($clear)
		$_page->objManage->ClearManageTable();
		
	header ("Location:"._URL.$return_path);
?>