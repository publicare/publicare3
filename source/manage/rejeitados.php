<?
global $_page;
?>

<div class="pblAlinhamentoTabelas">
	<TABLE WIDTH=570 BORDER=0 CELLPADDING=0 CELLSPACING=8 class="pblTabelaGeral">
	<TR>
		<TD>
			<img border=0 src="/html/imagens/portalimages/peca3.gif" ALT="" align="left"><font class="pblTituloBox">!!Objetos rejeitados</font><br>
			
		</td>

	</TR>
<tr><td>
<?
	$objetos = $_page->_adminobjeto->LocalizarRejeitados($_page);

	foreach ($objetos as $obj)
	{
		$loglist=$_page->_log->PegaLogWorkflow($_page, $obj["cod_objeto"]);
		if (count ($loglist))
		{
?>
	<table border="0" class="pblTabelaGeral" width=550 cellpadding="3" cellspacing="0">
		<tr>
			<td class="pblTituloLog" align="center" colspan="5">
				<? echo '<a  href="/index.php/content/view/'.$obj["cod_objeto"].'.html">'.$obj["titulo"]."</a><br>\r\n";?></td>
		</tr>
		
		<tr><td colspan="5" height="10"></td></tr>
		
		<tr>
			<td width="5">&nbsp;</td>
			<td class="pblTextoLog">
				<strong>Usu&aacute;rio</strong></td>
			<td class="pblTextoLog">
				<strong>Opera&ccedil;&atilde;o</strong></td>
			<td class="pblTextoLog" >
				<strong>Data</strong></td>
			<td class="pblTextoLog" width="280">
				<strong>Mensagem</strong></td>
		</tr>
	<?
			$count=0;
			foreach($loglist as $log)
			{
				if ($count++%2)
					$class="pblTextoLogImpar";
				else
					$class="pblTextoLogPar";
				echo '<tr><td class="'.$class.'" width="5">&nbsp;</td>'."\n";
				echo '<td class="'.$class.'">';
				echo $log['usuario'];
				echo '</td>'."\n";
				echo '<td class="'.$class.'">';
				echo $log['status'];
				echo '</td>'."\n";
				echo '<td class="'.$class.'">';
				echo $log['estampa'];
				echo '</td>'."\n";
				echo '<td class="'.$class.'">';
				echo $log['mensagem'];
				echo '</td>'."\n";
				echo '</tr>'."\n\n";
			}
			echo "</table>
			<br>\r\n";
		}
	}
?>

			</td></tr>
			<tr><td colspan="2"><p class="pblAssinatura"><?php echo _VERSIONPROG; ?></p></td></tr>
			</table></div>
