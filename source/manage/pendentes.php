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
 
global $page, $cod_objeto;

$sql = "SELECT count(t2.cod_objeto) as total  
from pendencia t1 
inner join objeto t2 on t1.cod_objeto=t2.cod_objeto 
inner join parentesco t3 on t1.cod_objeto=t3.cod_objeto 
where t3.cod_pai=".$cod_objeto." 
and t2.apagado=0"; 
$rs = $page->db->ExecSQL($sql);

$total = $rs->fields["total"];

$tam = 20;
$pag = intval((isset($_GET['pag'])?$_GET['pag']:'1'));
$inicio = ($pag>1)?(($tam*($pag-1))):0;
$fim = $inicio + $tam;
if ($fim > $total) $fim = $total;

$ord1 = isset($_GET["ord1"])?$_GET["ord1"]:"peso";
$ord2 = isset($_GET["ord2"])?$_GET["ord2"]:"asc";
if ($ord2=="asc") $ordf = $ord1;
else $ordf = "-".$ord1;
?>
<script>
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

<!-- === Objetos aguardando aprovação === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Objetos aguardando aprovação</b></h3></div>
		<form action="/do/list_content_post/<?=$page->objeto->Valor("cod_objeto")?>.html" name="pendentes" id="pendentes" method="POST">
		<input type="hidden" name="return_obj" value="<?php echo $page->objeto->Valor("cod_objeto")?>">
			
		<!-- === Botões (Inverter, Publicar) === -->
		<div class="panel-footer">
			<center>
				<input type="button" value="Inverter Sele&ccedil;&atilde;o" name="purge" class="btn btn-warning" id="btnInverter">
<?php
if ($_SESSION['usuario']['perfil'] <= _PERFIL_EDITOR)
{ 
?>
				<input type="submit" value="Publicar Objeto" name="publicar_pendentes" class="btn btn-success" onclick="trGravarTop2.style.display='';trGravarTop1.style.display='none';">
<?php 
}
?>
			</center>
		</div>
		<!-- === Final === Botões (Inverter, Publicar) === -->
			
		<div class="panel-body">

			<!-- === Mensagem de ação === -->
            <div class="alert alert-info alert-dismissible fade in modeloapagarclasse" role="alert" id="trGravarTop2" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">x</span></button>
                <h4>Processando informa&ccedil;&otilde;es .... aguarde....</h4>
            </div>
			<!-- === Final === Mensagem de ação === -->
			
			<!-- === Listar Conteúdo === -->
			<div class="panel panel-info modelo_propriedade">
				<div class="panel-heading">
					<div class="row">
						<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><? echo $page->objeto->Valor($page, "titulo")?></h3></div>
						<div class="col-sm-3 text-right titulo-icones">
                            <a href="<?php echo($page->config["portal"]["url"]); ?><?php echo($page->objeto->Valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
<?php 
if ($page->objeto->Valor("cod_objeto") != $page->config["portal"]["objroot"])
{ 
?>
                            <a href="/do/list_content/<?php echo($page->objeto->Valor("cod_pai"));?>.html" rel="tooltip" data-color-class = "primary" data-animate=" animated fadeIn" data-toggle="tooltip" data-original-title="Voltar para o pai" data-placement="left" title="Voltar para o pai"><i class='fapbl fapbl-ellipsis-h'></i></a>
<?php
}
?>
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
                                <th>Op&ccedil;&otilde;es</th>
                            </tr>
                        </thead>
                        <tbody>
						
<?php
	$objetos = $page->adminobjeto->localizarPendentes($cod_objeto, $_SESSION["usuario"]["cod_usuario"], $ord1, $ord2, $inicio);
	$cont = $inicio;
	foreach ($objetos as $obj)
	{
		$show = true;
		if ($_SESSION['usuario']['perfil']==_PERFIL_AUTOR || $_SESSION['usuario']['perfil']==_PERFIL_RESTRITO)
		{
			if ($obj['cod_usuario']==$_SESSION['usuario']['cod_usuario'])
				$show=true;
			else
				$show=false;
		}
		$cont++;
		$loglist=$page->log->PegaLogWorkflow($obj["cod_objeto"]);
?>
							<tr>
								<td><?php if ($show){ ?><input type="checkbox" id="objlist[]" name="objlist[]" value="<?=$obj["cod_objeto"]?>" class="chkObj"><?php } ?></td>
								<td width="85%"><?=$obj["titulo"]?></td>
								<td width="15%">
									<? if ($show){ ?><a href="/index.php/do/edit/<?=$obj["cod_objeto"]?>.html" title='Editar este objeto' class=' margin-left5' rel='tooltip' data-color-class = 'primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='Editar este objeto' data-placement='left' title='Editar este objeto'><i class='fapbl fapbl-pencil-alt font-size16'></i></a><? } ?>
									<a href="/index.php/content/view/<?=$obj['cod_objeto']?>.html" title="Exibir Objeto" rel='tooltip' data-color-class = 'primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='Visualizar objeto' data-placement='left' title='Visualizar objeto' class=' margin-left5'><i class='fapbl fapbl-eye font-size16'></i></a>
							<?php
								foreach($loglist as $log)
								{
									echo '<a rel="popover" data-animate=" animated fadeIn " data-container="body" data-toggle="popover" data-placement="top" data-content="'. $log['estampa']."<br>".$log['mensagem'].'" data-title="Top popover" data-trigger="hover" data-html="true" data-original-title="'. $log['usuario'] .'" class="margin-left5"><i class="fapbl fapbl-info-circle font-size16"></i></a>';
								}
							?>
								</td>
							</tr>
<?php
    } 
?>
						</tbody>
						<tfoot>
							<tr>
								<th>#</th>
								<th width="85%">T&iacute;tulo</th>
								<th width="15%">Op&ccedil;&otilde;es</th>
							</tr>
						</tfoot>
					</table>
					<!-- === Final === Tabela Listar Conteúdo (DATATABLE) === -->
					
				</div>
			</div>
			<!-- === Final === Listar Conteúdo === -->
				
		</div>
	</form>
</div>
<!-- === Final === Objetos aguardando aprovação === -->
