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
	$objetos = $this->container["adminobjeto"]->LocalizarRejeitados($page);

	foreach ($objetos as $obj)
	{
		$loglist=$this->container["log"]->pegarLogWorkflow($page, $obj["cod_objeto"]);
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
