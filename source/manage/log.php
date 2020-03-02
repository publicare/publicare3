<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo
 * @copyright GPL © 2007
 * @package publicare
 *
 * Este arquivo é parte do programa Publicare
 * Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
 * como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
 * de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
 */
	global $_page;
	
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	
	$loglist=$_page->_log->PegaLogObjeto($_page->_objeto->Valor("cod_objeto"));
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