<?php
global $_page, $cod_objeto;

	$_page->_administracao->ApagarObjeto($_page, $cod_objeto);
	header("Location:"._URL."/index.php/content/view/".$_POST['cod_pai'].".html?PortalMessage=Objeto+Apagado");
?>
