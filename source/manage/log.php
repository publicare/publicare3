<?php
	global $_page;
	
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	
	$loglist=$_page->_log->PegaLogObjeto($_page, $_page->_objeto->Valor($_page, "cod_objeto"));
//	var_dump($loglist);
//	exit();
	if (count ($loglist))
	{
		//BoxSimplesTop();
?>
	
	
	<div class="pblAlinhamentoTabelas">
	<table border="0" width=570 cellpadding="3" cellspacing="0" class="pblTabelaGeral">
		<tr>
			<td colspan="3">
				<p class="pblTituloLog">HIST&Oacute;RICO</p></td>
		</tr>
		<tr><td colspan="3" height="10"></td></tr>
		<tr>
			<td width="10">&nbsp;</td>
			<td class="pblTextoLog">
				<strong>Usu&aacute;rio</strong></td>
			<td class="pblTextoLog">
				<strong>Opera&ccedil;&atilde;o</strong></td>
			<td class="pblTextoLog">
				<strong>Data</strong></td>
		</tr>
	<?
		$count=0;
		if (isset($loglist) && is_array($loglist)){
			foreach($loglist as $log)
			{
				if ($count++%2)
					$class="pblTextoLogImpar";
				else
					$class="pblTextoLogPar";
				echo '<tr>';
				echo '<td class="'.$class.'">&nbsp;</td><td class="'.$class.'">';
				echo $log['usuario'];
				echo '</td>'."\n";
				echo '<td class="'.$class.'">';
				echo $log['operacao'];
				echo '</td>'."\n";
				echo '<td class="'.$class.'">';
				echo $log['estampa'];
				echo '</td>'."\n";
				echo '</tr>'."\n\n";
			}
		}
	?>
	
	<tr><td colspan="4"><p class="pblAssinatura"><?php echo _VERSIONPROG; ?></p></td></tr>
	
	</table>
	</div>
<?
	//BoxSimplesBottom();
}?>