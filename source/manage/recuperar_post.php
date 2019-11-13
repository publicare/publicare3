<?php
global $_page;

if (isset($_POST['objlist']) && is_array($_POST['objlist']) && count($_POST['objlist'])>0)
{
    rsort($_POST['objlist']);

    foreach($_POST['objlist'] as $obj)
    {
            $_page->_administracao->RecuperarObjeto($_page, $obj);
    }
}
header ("Location: "._URL."/do/recuperar/".$_page->_objeto->Valor($_page, 'cod_objeto').".html");

