<?
global $_page;
?>

<form action="index.php/do/submeter_post/<? echo $_page->_objeto->Valor($_page, "cod_objeto");?>.html" method="post">
	<div class="pblAlinhamentoTabelas">
	<TABLE WIDTH=500 BORDER=0 CELLPADDING=0 CELLSPACING=8 class="pblTabelaGeral">
	<TR>
		<TD>
			<img border=0 src="html/imagens/portalimages/peca3.gif" ALT="" align="left"><font class="pblTituloBox">Solicitar publica&ccedil;&atilde;o</font><br>
			<font class="pblTextoForm"><? echo $_page->_objeto->Valor($_page, "titulo")?></font>
		</td>

		<TD width="120" class="pblAlinhamentoBotoes">
					<a class="ABranco" href="index.php/content/view/<? echo $_page->_objeto->Valor($_page, "cod_objeto")?>.html"><img border=0 src="/html/imagens/portalimages/exibir.png" ALT="Exibir Objeto" hspace="2"></a>
					<a href="#" onclick="history.back()"><img border=0 src="html/imagens/portalimages/voltar2.gif" ALT="Voltar"></a>
				</TD>
	</TR>


	<tr>
		<td class="pblFormTitle" colspan="2">
			<P>Coment&aacute;rios<br>
			<textarea name="message" class="pblInputForm" rows=5 cols=59 ><? echo $message ?></textarea>
		</td>
	</tr>
	<tr>
		<td class="pblFormTitle"align="right" colspan="2">
			<input type="submit" class="pblBotaoForm" name="submit" value="Gravar">	
		</td>
	</tr>
	
	<tr><td colspan="2"><p class="pblAssinatura"><?php echo _VERSIONPROG; ?></p></td></tr>
	</table>
</form>