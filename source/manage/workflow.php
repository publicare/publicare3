<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo workflow.php é responsável pela exibição do log workflow
 * @copyright MIT © 2020
 * @package publicare/manage
 *
 * Este arquivo é parte do programa Publicare
 * 
 * Copyright (c) 2020 Publicare
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

global $_page;

	$loglist = $_page->_log->PegaLogWorkflow($_page->_objeto->Valor("cod_objeto"));
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
	<?php
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
<?php	
	//BoxSimplesBottom();
	}
	else {
	include("manage/vazio.php");	
	}
?>