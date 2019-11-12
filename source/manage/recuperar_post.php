<?
global $_page;

	rsort($_POST['objlist']);
	
	foreach($_POST['objlist'] as $obj)
	{
		$_page->_administracao->RecuperarObjeto($_page, $obj);
	}
	
//	exit();
	
	header ("Location:/index.php/do/recuperar/".$_page->_objeto->Valor($_page, 'cod_objeto').".html");

?>
