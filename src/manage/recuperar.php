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
$inicio = $this->container["objeto"]->valor("cod_objeto");

// xd($this->container["objeto"]);

if (isset($_GET["ajaxtbl"]))
{
    $array = $this->container["adminobjeto"]->buscaObjetoJsonDatatable($_POST, true);
    echo($array);
    exit();
}
?>

<script type="text/javascript">
$(document).ready(function(){

	$('#tabelaLista').dataTable({
		"responsive": true,
		"language": linguagemDataTable,
		"processing": true,
		"serverSide": true,
		"ajax": {
			"url": "do/recuperar/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html?naoincluirheader&ajaxtbl",
			"type": "POST"
		},
		"order": [[ 4, "desc" ]],
		"columns": [
			{ 
				"data": "checkbox",
				"orderable": false,
				"searchable": false
			},
			{ "data": "cod_objeto" },
			{ "data": "titulo" },
			{ "data": "classe" },
			{ "data": "data_exclusao" }
		]
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

<!-- === Recuperar objetos apagados === -->
<div class="card">
    <div class="card-header bg-primary text-white"><h3><b>Recuperar objetos apagados</b></h3></div>

	<form action="do/recuperar_post/<?php echo $this->container["objeto"]->valor('cod_objeto')?>.html" name="listcontent" id="listcontent" method="POST">
	<div class="card-body">

		<!-- === Listar Conteúdo === -->
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($this->container["objeto"]->valor("titulo")); ?></h3></div>
					<div class="col-sm-3 text-right titulo-icones">
						<a href="<?php echo($this->container["config"]->portal["url"]); ?><?php echo($this->container["objeto"]->valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
						<?php 
if ($this->container["objeto"]->valor("cod_objeto") != $this->container["config"]->portal["objroot"])
{ 
?>
                            <a href="do/recuperar/<?php echo($this->container["objeto"]->valor("cod_pai"));?>.html" rel="tooltip" data-color-class = "primary" data-animate=" animated fadeIn" data-toggle="tooltip" data-original-title="Voltar para o pai" data-placement="left" title="Voltar para o pai"><i class='fapbl fapbl-ellipsis-h'></i></a>
<?php
}
?>
					</div>
				</div>
			</div>

			<div class="card-body">

				<!-- === Tabela Listar Conteúdo (DATATABLE) === -->
				<table id="tabelaLista" class="display" style="width:100%">
					<thead>
						<tr>
							<th>#</th>
							<th>C&oacute;digo</th>
							<th>T&iacute;tulo</th>
							<th>Classe</th>
							<th>Data da exclusão</th>
						</tr>
					</thead>
					
				</table>
				<!-- === Final === Tabela Listar Conteúdo (DATATABLE) === -->

			</div>
		</div>
		<!-- === Final === Listar Conteúdo === -->
			
	</div>
    <div class="card-footer" style="text-align: right;">
		<input type="button" name="purge" value="Inverter Sele&ccedil;&atilde;o" class="btn btn-warning" id="btnInverter">
		<input type="submit" name="undelete" value="Recuperar Objetos Selecionados" class="btn btn-success">
    </div> 
	</form>
</div>
<!-- === Final === Recuperar objetos apagados === -->