<?
	include_once ("iniciar.php");
	$obj_comentario = $_POST['cod_objeto'];
	$email = $_POST['email'];
	$texto = $_POST['texto'];
	$sql = "insert into comentarios (cod_objeto, email, texto) values ($obj_comentario, '$email', '$texto')";
	if ($_page->db->ExecSQL($sql))
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>C&amp;T Jovem - Comentários</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK rel="STYLESHEET" type="text/css" href="/style.css">
</head>
<body leftmargin="15" topmargin="2">
<div align="center" CLASS="AAzul">Obrigado por seu comentário. Ele estará visível após aprovação.<br>
<form action="javascript:window.close()">
	<input class='pblFormButton' type='submit' value='Fechar'>
</form>
</div>
</body>
</html>
<?
	}
?>
