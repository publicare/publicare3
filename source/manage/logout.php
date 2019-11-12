<?php
global $_page;
$_page->_usuario->Logout();
?>
<script>
    window.location.href="<?php echo($_page->_objeto->Valor($_page, "url")); ?>?LoginMessage=Logout+efetuado+com+sucesso";
</script>