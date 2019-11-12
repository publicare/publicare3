<?php
global $_page;
header("Content-Type: text/html; charset=ISO-8859-1",true);

	$loglist = $_page->_log->PegaLogWorkflow($_page, $_page->_objeto->Valor($_page, "cod_objeto"));
	if (count ($loglist))
	{
		//BoxSimplesTop();
?>
	<div class="pblAlinhamentoTabelas">
	<table border="0" width=570 cellpadding="3" cellspacing="0" class="pblTabelaGeral">
		<tr>
			<td class="pblTituloLog" align="center" colspan="5">
				<p class="pblTituloLog">WORKFLOW</p></td>
		</tr>
		
		<tr><td colspan="3" height="10"></td></tr>
		
		<tr>
			<td width="10">&nbsp;</td>
			
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
		}
	?>
	<tr><td colspan="5"><p class="pblAssinatura"><?php echo _VERSIONPROG; ?></p></td></tr>
	
	</table>
	</div>
<?	
	//BoxSimplesBottom();
	}
	else {
	include("manage/vazio.php");	
	}
?>