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
//
//$sql = "select count(cod_objeto) as total from objeto where apagado=1";
//$rs = $_page->_db->ExecSQL($sql);
//$total = $rs->fields["total"];
//
//$ord1 = isset($_GET["ord1"])?$_GET["ord1"]:"titulo";
//$ord2 = isset($_GET["ord2"])?$_GET["ord2"]:"asc";
//if ($ord2=="asc") $ordf = $ord1;
//else $ordf = "-".$ord1;
?>

<script type="text/javascript">
$(document).ready(function(){
    $('#tabelaLista')
            .dataTable({
                responsive: true,
                language: linguagemDataTable,
                order: [[ 1, "asc" ]],
            });
            
    $(".btnAcao").click(function(){
        $("#divMensagemGravar").show();
        $("#divBotoesAcao").hide();
    });
    
    $("#btnInverter").click(function(){
        $(".chkObj").each(function(){
            if ($(this).prop("checked")) {
                $(this).prop("checked", false);
            } else {
                $(this).prop("checked", true);
            }
        });
    });
});
</script>
<script src="include/javascript_datatable" type="text/javascript"></script>
<link href="include/css_datatable" rel="stylesheet" type="text/css"> 

<!-- === Apagar em definitivo === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Apagar em definitivo</b></h3></div>

	<form action="do/apagar_definitivo_post.php/<?=$_page->_objeto->Valor("cod_objeto")?>.html" name="listcontent" id="listcontent" method="POST">
	<div class="panel-body">

		<!-- === Listar Conteúdo === -->
		<div class="panel panel-info modelo_propriedade">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($_page->_objeto->Valor("titulo")); ?></h3></div>
					<div class="col-sm-3 text-right titulo-icones">
						<a href="<?php echo($_page->config["portal"]["url"]); ?><?php echo($_page->_objeto->Valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
					</div>
				</div>
			</div>

			<div class="panel-body">

				<!-- === Tabela Listar Conteúdo (DATATABLE) === -->
				<table id="tabelaLista" class="display" style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Código</th>
							<th>T&iacute;tulo</th>
							<th>Classe</th>
							<th>Data da exclusão</th>
						</tr>
					</thead>
					<tbody>

<?php
	$deletedlist=$_page->_administracao->PegaListaDeApagados();
	$count=0;
	foreach ($deletedlist as $obj)
	{
		$show=true;
		if ($_SESSION['usuario']['perfil']==_PERFIL_AUTOR || $_SESSION['usuario']['perfil']==_PERFIL_RESTRITO)
		{
			if ($obj['cod_usuario']==$_SESSION['usuario']['cod_usuario'])
				$show=true;
			else
				$show=false;
		}
		if ($show)
		{
			if ($count++%2)
				$classe="pblTextoLogImpar";
			else
				$classe="pblTextoLogPar";
?>
						<tr>
							<td><input type="checkbox" id="objlist[]" name="objlist[]" value="<?php echo $obj["cod_objeto"];?>" class="chkObj"></td>
							<td><?php echo $obj["cod_objeto"];?></td>
							<td><a href="<?php echo $obj["exibir"]?>"><strong><? echo $obj["titulo"];?></strong></a></td>
							<td><?php echo $obj["classe"];?></td>
							<td><?php echo ConverteData($obj["data_exclusao"], 5);?></td>
						</tr>
<?php
		}
	}
?>
					</tbody>
					<tfoot>
						<tr>
							<th>#</th>
                                                        <th>Código</th>
							<th>T&iacute;tulo</th>
							<th>Classe</th>
							<th>Data da exclusão</th>
						</tr>
					</tfoot>
				</table>
				<!-- === Final === Tabela Listar Conteúdo (DATATABLE) === -->

			</div>
		</div>
		<!-- === Final === Listar Conteúdo === -->

	</div>
    <div class="panel-footer" style="text-align: right;">
		<input type="button" name="purge" value="Inverter Sele&ccedil;&atilde;o" class="btn btn-warning" id="btnInverter">
		<input type="submit" name="undelete" value="Apagar Selecionados em Definitivo" class="btn btn-danger">
    </div> 
	</form>
</div>
<!-- === Final === Recuperar objetos apagados === -->