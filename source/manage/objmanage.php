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
?>

<form action="/manage/objmanage_post.php/<?=$_page->_objeto->Valor($_page, "cod_objeto")?>.html" method="get" name="objmanage" id="objmanage">
	<input type="hidden" name="cod_object" value="<? echo $_page->obj->cod_object?>">
	<input type="hidden" name="return_path" value="/index.php?action=/content/view_<? echo $_page->obj->prefix ?>&cod_object=<? echo $cod_object?>">
	<table width="578" border="0" cellpadding="4" cellspacing="2">
		<tr>
			<? if ($_page->obj->CanAddChildren()) 
				{
			?>
			<td class="pblFormTitle">
				<select class="FormSelect" name="cod_objmanage">
				<? echo $_page->objManage->GetObjManageDropDown() ?>
				</select>
				<?
					if ($_page->objManage->HasManageObjectList)
					{
				?>
				<input class="pblFormButton" type="submit" name="pastelink" value=" Colar Link ">&nbsp;
				<input class="pblFormButton" type="submit" name="move" value="Mover">
				<input class="pblFormButton" type="submit" name="copy" value="Copiar aqui">
				<input class="pblFormButton" type="submit" name="clear" value="Limpar Lista">

				<?
					}
				?>
			</td>
			<?
				}
			?>

		</tr>
	</table>

</form>
