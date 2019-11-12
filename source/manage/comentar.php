<?
	include ("initdocroot2.pinc");
	$obj_comentario = $_page->AdminObjeto->CriarObjeto($_GET['cod_obj_comentario']);
	$titulo = $obj_comentario->Valor("titulo");
	$obj_atual = $_GET['cod_obj_comentario'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>C&amp;T Jovem - Comentários</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK rel="STYLESHEET" type="text/css" href="/style.css">
</head>
<body leftmargin="15" topmargin="2">
<table width="525" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="121"><img src="/imagens/logo.gif" width="121" height="102"></td>
          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><img src="/imagens/cabecalho_comentario.gif" width="404" height="71"></td>
              </tr>
              <tr>
                <td height="31"><div align="center"><font color="#0000FF" size="3" face="Comic Sans MS"><? echo $titulo;?></font></div></td>
              </tr>
            </table></td>
        </tr>
      </table>
	 </td>
	</tr>
	<tr>
		<td>
			<form id="comentar" name="comentar" action="/manage/comentar_post.php/<?=$GLOBALS['_page']->Objeto->Valor("cod_objeto")?>.html" method="post">
				<input type="hidden" name="cod_objeto" id="cod_objeto" value=<?echo $obj_atual;?>>
				<font CLASS='TextoPreto'>Digite seu email: </font><input type="text" name="email" id="email" value="" maxlength=50 size=50><br>
				<font CLASS='TextoPreto'>Digite seu Comentário: </font><textarea name="texto" id="texto" cols=50 rows=5></textarea><br>
				<input class='pblFormButton' type="reset" value="Apagar">&nbsp;<input class='pblFormButton' type="submit" value="Enviar"><br>
			</form>
		</td>
	</tr>
	<tr>
		<td>
	<table width=525 border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td>
				<img src="/imagens/rodape_comentario_01.gif" width=452 height=22></td>
			<td>
				<a href="javascript:window.close()"><img src="/imagens/rodape_comentario_02.gif" width=73 height=22 border=0></a></td>
		</tr>
	</table>
  </tr>
</table>
</body>
</html>
