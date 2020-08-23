<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo
 * @copyright MIT © 2020
 * @package publicare
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
namespace Pbl;
	global $page;
	
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	
	$loglist=$page->log->PegaLogObjeto($page->objeto->valor("cod_objeto"));
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
	<?php
		$count=0;
		if (isset($loglist) && is_array($loglist)){
//                    xd($loglist);
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
<?php
	//BoxSimplesBottom();
}?>