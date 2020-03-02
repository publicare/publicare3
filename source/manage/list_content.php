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

$_page->_objeto->PegaListaDeFilhos('*');

$lstStatus = array("", "Privado", "Publicado", "Rejeitado", "Submetido");
?>
<style>trMouseAction1:hover { background: #fff; }</style>
<ul class="nav nav-tabs">
  <li class="active"><a href="do/list_content/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Listar Conteúdo</a></li>
  <li><a href="do/pilha/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Pilha</a></li>
</ul>
<script>
$(document).ready(function(){
    
    $.fn.dataTable.moment( 'DD/MM/YYYY' );
    
    $('#tabelaLista')
            .dataTable({
                responsive: true,
                language: linguagemDataTable,
                order: [[ 1, "asc" ]]
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

<!-- === Listar Conteúdo === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Listar Conteúdo</b></h3></div>
	
    <form action="do/list_content_post.php/<?php echo($_page->_objeto->Valor("cod_objeto")); ?>.html" name="listcontent" id="listcontent" method="post">
        <input type="hidden" name="return_obj" value="<?php echo($_page->_objeto->Valor("cod_objeto")); ?>">

        <div class="panel-footer">
            <!-- === Botões (Inverter, Publicar, Despublicar, Apagar, Duplicar e Copiar para a pilha) === -->
            <div id="divBotoesAcao">
                <center>
                    <input type="button" value="Inverter Sele&ccedil;&atilde;o" name="purge" class="btn btn-warning" id="btnInverter">
<?php
if ($_SESSION['usuario']['perfil'] <= _PERFIL_EDITOR)
{ 
?>
                    <input type="submit" value="Publicar itens" name="publicar" class="btn btn-success btnAcao">
                    <input type="submit" value="Despublicar itens" name="despublicar" class="btn btn-black-opaco btnAcao">
<?php 
}
elseif ($_SESSION['usuario']['perfil'] == _PERFIL_AUTOR)
{ 
?>
                    <input type="submit" value="Solicitar itens" name="solicitar" class="btn btn-warning">
<?php
}
?>
                    <input type="submit" value="Apagar itens" name="delete" class="btn btn-danger">
                    <input type="submit" value="Duplicar itens" name="duplicate" class="btn btn-info">
                    <input type="submit" value="Copiar para a pilha" name="copy" class="btn btn-warning">
                </center>
            </div>
            <!-- === Final === Botões (Inverter, Publicar, Despublicar, Apagar, Duplicar e Copiar para a pilha) === -->
            <!-- === Mensagem de ação === -->
            <div class="alert alert-warning alert-dismissible fade in" role="alert" id="divMensagemGravar" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">x</span></button>
                <h4>Processando informa&ccedil;&otilde;es .... aguarde....</h4>
            </div>
            <!-- === Final === Mensagem de ação === -->
        </div>

        <div class="panel-body">

            

            <!-- === Listar Conteúdo === -->
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($_page->_objeto->Valor("titulo")); ?></h3></div>
                        <div class="col-sm-3 text-right titulo-icones">
                            <a class="ABranco" href="<?php echo($_page->config["portal"]["url"]); ?><?php echo($_page->_objeto->Valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
<?php 
if ($_page->_objeto->Valor("cod_objeto") != $_page->config["portal"]["objroot"])
{ 
?>
                            <a class="ABranco" href="do/list_content/<?php echo($_page->_objeto->Valor("cod_pai"));?>.html" rel="tooltip" data-color-class = "primary" data-animate=" animated fadeIn" data-toggle="tooltip" data-original-title="Voltar para o pai" data-placement="left" title="Voltar para o pai"><i class='fapbl fapbl-ellipsis-h'></i></a>
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
                                <th>Código</th>
                                <th>T&iacute;tulo</th>
                                <th>Classe</th>
                                <th>Peso</th>
                                <th>Data publicação</th>
                                <th>Status</th>
                                <th>A&ccedil;&otilde;es</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
for ($i=0; $i < $_page->_objeto->quantidade; $i++)
{
    $obj = $_page->_objeto->filhos[$i];
    $style_status = "";
    if ($obj->Valor("cod_status") == _STATUS_SUBMETIDO) $style_status = "color: blue !important;";
    elseif ($obj->Valor("cod_status") == _STATUS_PRIVADO || $obj->Valor("cod_status") == _STATUS_REJEITADO) $style_status = "color: red !important;";
?>
                            <tr style="<?php echo($style_status); ?>">
                                <td>
<?php
    if (($_SESSION['usuario']['perfil']==_PERFIL_AUTOR && $obj->Valor("cod_usuario")==$_SESSION['usuario']['cod_usuario']) 
            || $_SESSION['usuario']['perfil']<=_PERFIL_EDITOR)
    {
?>
                                    <input type="checkbox" id="objlist[]" name="objlist[]" value="<?php echo($obj->Valor("cod_objeto"));?>" class="chkObj">
<?php
    } else { echo("&nbsp;"); }
?>
                                </td>
                                <td><?php echo($obj->Valor("cod_objeto")); ?></td>
                                <td><?php echo($obj->Valor("titulo")); ?></td>
                                <td><?php echo($obj->Valor("classe")); ?></td>
                                <td><?php echo($obj->Valor("peso")); ?></td>
                                <td><?php 
                                $vdata = preg_split("[ - ]", $obj->Valor("data_publicacao"));
                                echo($vdata[0]); 
                                ?></td>
                                <td><?php echo($lstStatus[$obj->Valor("cod_status")]); ?></td>
                                <td>
<?php
    if ($_SESSION['usuario']['perfil']<=_PERFIL_AUTOR || ($_SESSION['usuario']['perfil']==_PERFIL_AUTOR && $obj->Valor("cod_usuario")==$_SESSION['usuario']['cod_usuario']))
    {
?>
                                    <a href="<?php echo($_page->config["portal"]["url"]); ?>/manage/edit/<?php echo($obj->Valor("cod_objeto")); ?>.html" title='Editar Objeto' class='margin-left5' rel='tooltip' data-animate='animated fadeIn' data-toggle='tooltip' data-original-title='Editar Objeto' data-placement='left' title='Editar este objeto'><i class='fapbl fapbl-pencil-alt font-size16'></i></a>
<?php
    }
?>
                                    <a href="<?php echo($_page->config["portal"]["url"].$obj->Valor("url")); ?>" title="Exibir Objeto" rel='tooltip' data-animate='animated fadeIn' data-toggle='tooltip' data-original-title='Visualizar objeto' data-placement='left' title='Visualizar objeto' class='margin-left5'><i class='fapbl fapbl-eye font-size16'></i></a>
<?php
    if ($obj->PodeTerFilhos())
    {
?>
                                    <a href="<?php echo($_page->config["portal"]["url"]); ?>/do/list_content/<?php echo($obj->Valor("cod_objeto")); ?>.html" title="Listar conteúdo" rel='tooltip' data-animate='animated fadeIn' data-toggle='tooltip' data-original-title='Listar conteúdo' data-placement='left' title='Listar conteúdo' class='margin-left5'><i class='fapbl fapbl-folder-open font-size16'></i></a>
<?php
    } 
?>
                                </td>
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
<!-- === Final === Listar Conteúdo === -->