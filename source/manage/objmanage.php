<?php
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
