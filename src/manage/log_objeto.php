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
global $loglist, $log, $count;

	$loglist = $this->container["log"]->PegaLogObjeto($this->container["objeto"]->valor("cod_objeto"));
//	if (count ($loglist))
//	{
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#tabelaLista').dataTable({
			responsive: true,
			language: linguagemDataTable,
			order: [[ 2, "desc" ]],
		});
	});
</script>
<script src="include/javascript_datatable" type="text/javascript"></script>
<link href="include/css_datatable" rel="stylesheet" type="text/css">

<!-- === Menu === -->
<ul class="nav nav-tabs">
  <li><a href="do/preview/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Indice do Objeto</a></li>
  <li><a href="do/log_workflow/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Workflow</a></li>
  <li class="active"><a href="do/log_objeto/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Log Status</a></li>
</ul>
<!-- === FInal === Menu === -->

<!-- === Log Status === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Log Status</b></h3></div>
	<div class="panel-body">
		
		<!-- === Listagem do Log Status === -->
		<div class="panel panel-info modelo_propriedade">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($this->container["objeto"]->valor("titulo")); echo " <i>[cod: ".$this->container["objeto"]->valor("cod_objeto")."]</i>"; ?></h3></div>
					<div class="col-sm-3 text-right titulo-icones">
						<a class="ABranco" href="<?php echo($this->container["config"]->portal["url"]); ?><?php echo($this->container["objeto"]->valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
					</div>
				</div>
			</div>

			<div class="panel-body">
								   
				<!-- === Tabela Listar Conteúdo (DATATABLE) === -->
				<table id="tabelaLista" class="display" style="width:100%">
					<thead>
						<tr>
							<th>Usuário</th>
							<th>Operação</th>
							<th>Data</th>
						</tr>
					</thead>
					<tbody>
<?php
	$count=0;
//        xd($loglist);
	if (isset($loglist) && is_array($loglist)){
		foreach($loglist as $log)
		{
			if ($count++%2)
				$class="pblTextoLogImpar";
			else
				$class="pblTextoLogPar";
			echo '<tr>';
			echo '<td width="62%">';
			echo $log['usuario'];
			echo '</td>'."\n";
			echo '<td width="18%">';
			echo $log['operacao'];
			echo '</td>'."\n";
			echo '<td width="18%">';
			echo $log['estampa'];
			echo '</td>'."\n";
			echo '</tr>'."\n\n";
		}
	}
?>
					</tbody>
					<tfoot>
						<tr>
							<th>Usuário</th>
							<th>Operação</th>
							<th>Data</th>
						</tr>
					</tfoot>
				</table>
				<!-- === Final === Tabela Listar Conteúdo (DATATABLE) === -->

			</div>
		</div>
		<!-- === Final === Listagem do Log Status === -->
		
	</div>
</div>
<!-- === Log Status === -->
<?php
//	}
//	else {
//	include("manage/vazio.php");	
//	}
?>