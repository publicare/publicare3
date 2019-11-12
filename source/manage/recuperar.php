<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo classes.php é responsável pela montagem do formulário para administração das classes
 * @copyright GPL © 2007
 * @package publicare
 *
 * MCTI - Ministério da Ciência, Tecnologia e Inovação - www.mcti.gov.br
 * ANTT - Agência Nacional de Transportes Terrestres - www.antt.gov.br
 * EPL - Empresa de Planejamento e Logística - www.epl.gov.br
 * LogicBSB - LogicBSB Sistemas Inteligentes - www.logicbsb.com.br
 *
 * Este arquivo é parte do programa Publicare
 * Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
 * como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
 * de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
 */

global $_page;

$sql = "select count(cod_objeto) as total from objeto where apagado=1";
$rs = $_page->_db->ExecSQL($sql);
$total = $rs->fields["total"];

$ord1 = isset($_GET["ord1"])?$_GET["ord1"]:"titulo";
$ord2 = isset($_GET["ord2"])?$_GET["ord2"]:"asc";
if ($ord2=="asc") $ordf = $ord1;
else $ordf = "-".$ord1;
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
<script src="/include/javascript_datatable" type="text/javascript"></script>
<link href="/include/css_datatable" rel="stylesheet" type="text/css"> 

<!-- === Recuperar objetos apagados === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Recuperar objetos apagados</b></h3></div>

	<form action="/do/recuperar_post/<? echo $_page->_objeto->Valor($_page, 'cod_objeto')?>.html" name="listcontent" id="listcontent" method="POST">
	<div class="panel-body">

		<!-- === Listar Conteúdo === -->
		<div class="panel panel-info modelo_propriedade">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($_page->_objeto->Valor($_page, "titulo")); ?></h3></div>
					<div class="col-sm-3 text-right titulo-icones">
						<a href="<?php echo(_URL); ?><?php echo($_page->_objeto->Valor($_page, "url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
					</div>
				</div>
			</div>

			<div class="panel-body">

				<!-- === Tabela Listar Conteúdo (DATATABLE) === -->
				<table id="tabelaLista" class="display" style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<th>T&iacute;tulo</th>
							<th>Classe</th>
							<th>Data da exclusão</th>
						</tr>
					</thead>
					<tbody>
<?
	$deletedlist = $_page->_administracao->PegaListaDeApagados($_page, $inicio);

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
							<td width="10"><input type="checkbox" id="objlist[]" name="objlist[]" value="<? echo $obj["cod_objeto"];?>" class="chkObj"></td>
							<td width="60%"><a href="<? echo $obj["exibir"]?>"><strong><? echo $obj["titulo"];?></strong></a></td>
							<td width="20%"><?php echo $obj["classe"];?></td>
							<td width="20%"><? echo ConverteData($obj["data_exclusao"], 5);?></td>
						</tr>
<?
		}
	}
?>

					</tbody>
					<tfoot>
						<tr>
							<th>#</th>
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
		<input type="submit" name="undelete" value="Recuperar Objetos Selecionados" class="btn btn-success">
    </div> 
	</form>
</div>
<!-- === Final === Recuperar objetos apagados === -->