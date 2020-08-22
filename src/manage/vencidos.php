<?php 
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo vencidos.php - é responsável pela montagem do formulário de objetos vencidos
 * @copyright MIT © 2020
 * @package publicare/manage
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
//
//$total = $page->administracao->PegaTotalDeVencidos($page, $cod_objeto);
//$inicio = !isset($inicio)?0:$inicio;
?>
<script src="include/javascript_datatable" type="text/javascript"></script>
<link href="include/css_datatable" rel="stylesheet" type="text/css"> 
 
<script>
$(document).ready(function(){
     
    $.fn.dataTable.moment( 'DD/MM/YYYY' );
    
    $('#tabelaLista')
            .dataTable({
                responsive: true,
                language: linguagemDataTable,
                order: [[ 2, "desc" ]],
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

<!-- === Objeto Vencidos === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Objeto Vencidos</b></h3></div>
	
		<form action="do/vencidos_post/<?=$page->objeto->valor("cod_objeto")?>.html" name="listcontent" id="listcontent" method="POST">
		<input type="hidden" name="return_obj" value="<?php echo $page->objeto->valor("cod_objeto")?>">
			
		<!-- === Botões (Inverter, Publicar) === -->
		<div class="panel-footer">
			<center>
				
				<input type="button" value="Inverter Sele&ccedil;&atilde;o" name="purge" class="btn btn-warning" id="btnInverter">
<?php
if ($_SESSION['usuario']['perfil'] <= _PERFIL_EDITOR)
{ 
?>
				<input type="submit" name="undelete" value="Apagar Selecionados em Definitivo" class="btn btn-success" onclick="trGravarTop2.style.display='';trGravarTop1.style.display='none';">
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

			<h4 class="padding-bottom20 padding-top10 font-size20">ATEN&Ccedil;&Atilde;O: Objeto deletados aqui n&atilde;o poder&atilde;o ser recuperados!</h4>
			
			<!-- === Listar Conteúdo === -->
			<div class="panel panel-info modelo_propriedade">
				<div class="panel-heading">
					<div class="row">
						<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo $page->objeto->valor("titulo")?></h3></div>
						<div class="col-sm-3 text-right titulo-icones">
                            <a href="<?php echo($page->config["portal"]["url"]); ?><?php echo($page->objeto->valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
<?php 
if ($page->objeto->valor("cod_objeto") != $page->config["portal"]["objroot"])
{ 
?>
                            <a href="do/list_content/<?php echo($page->objeto->valor("cod_pai"));?>.html" rel="tooltip" data-color-class = "primary" data-animate=" animated fadeIn" data-toggle="tooltip" data-original-title="Voltar para o pai" data-placement="left" title="Voltar para o pai"><i class='fapbl fapbl-ellipsis-h'></i></a>
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
                                <th width="65%">T&iacute;tulo</th>
                                <th width="25%">Data de validade</th>
                                <th width="10%">A&ccedil;&otilde;es</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
$arrListaObjetoVencidos = $page->administracao->pegarListaVencidos();
foreach ($arrListaObjetoVencidos as $ListaChave => $ListaTexto)
{
	$show = true;
	if ($_SESSION['usuario']['perfil']==_PERFIL_AUTOR || $_SESSION['usuario']['perfil']==_PERFIL_RESTRITO)
	{
		if ($obj['cod_usuario'] == $_SESSION['usuario']['cod_usuario'])
			$show=true;
		else
			$show=false;
	}

?>
							<tr>
								<td><?php if ($show){ ?><input type="checkbox" id="objlist[]" name="objlist[]" value="<?php echo($ListaTexto['cod_objeto']); ?>" class="chkObj"><?php } ?></td>
								<td width="60%"><?=$ListaTexto['titulo']?></td>
								<td width="30%"><?=ConverteData($ListaTexto['data_validade'],5)?>&nbsp;</td>
								<td width="10%"><?php if ($show){ ?><a href="/index.php/do/edit/<?=$ListaTexto['cod_objeto']?>.html" title='Editar este objeto' class=' margin-left5' rel='tooltip' data-color-class = 'primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='Editar este objeto' data-placement='left' title='Editar este objeto'><i class='fapbl fapbl-pencil-alt font-size16'></i></a><?php } ?></td>
							</tr>
<?php
}
?>	
						</tbody>
					</table>
					<!-- === Final === Tabela Listar Conteúdo (DATATABLE) === -->
					
				</div>
			</div>
			<!-- === Final === Listar Conteúdo === -->
				
		</div>
	</form>
</div>
<!-- === Final === Objeto Vencidos === -->
<?php
	//echo "<div id=\"divGuiaB\" style=\"height: 0%; visibility: hidden;\">";
	//include ("pilha.php");
	//echo "</div>";
?>
