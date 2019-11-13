<?php
	include_once ("iniciar.php");
	$_page->objManage->Undelete($objlist);
	header("Location:"._URL.$return_path);

?>