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
namespace Pbl;
global $page;

if (isset($_GET["ajaxtbl"]))
{
    //xd($_POST);
    $busca = isset($_POST["search"]["value"])&&$_POST["search"]["value"]!=""?htmlspecialchars($_POST["search"]["value"], ENT_QUOTES, "UTF-8"):"";
    $draw = isset($_POST["draw"])&&$_POST["draw"]!=""?htmlspecialchars($_POST["draw"], ENT_QUOTES, "UTF-8"):"1";
    $qry = "";
    if ($busca != "")
    {
        $qry .= "titulo like %".$busca."%";
        $qry .= "||classe like %".$busca."%";
        if(is_numeric($busca))
        {
            $qry .= "||cod_objeto like %".$busca."%";
        }
    }
    //$qry = "titulo like %a%";
    $ordem = isset($_POST["order"][0]["column"])?$_POST["columns"][(int)$_POST["order"][0]["column"]]["data"]:"";
    if (isset($_POST["order"][0]["dir"]) && $_POST["order"][0]["dir"]=="desc")
    {
        $ordem = "-".$ordem;
    }
    $inicio = isset($_POST["start"])&&$_POST["start"]?(int)htmlspecialchars($_POST["start"], ENT_QUOTES, "UTF-8"):-1;
    $limite = isset($_POST["length"])&&$_POST["length"]?(int)htmlspecialchars($_POST["length"], ENT_QUOTES, "UTF-8"):-1;
    $pai = $page->objeto->valor("cod_objeto");
    $niveis = 0;
    $objetos = $page->adminobjeto->localizarObjetos('*', $qry, $ordem, $inicio, $limite, $pai, $niveis);
    $objetos2 = $page->adminobjeto->localizarObjetos('*', $qry, $ordem, -1, -1, $pai, $niveis);
    $objetostotal = $page->adminobjeto->localizarObjetos('*', "", "", -1, -1, $pai, $niveis);
    $array = array(
        "draw" => $draw,
        "recordsTotal" => count($objetostotal),
        "recordsFiltered" => count($objetos2),
        "data" => array()
    );
    foreach ($objetos as $obj)
    {
        if ($obj->metadados["cod_status"] == _STATUS_PRIVADO || $obj->metadados["cod_status"] == _STATUS_REJEITADO)
        {
            $obj->metadados["titulo"] = "<font color='red'>".$obj->metadados["titulo"]."</font>";
        }
        elseif ($obj->metadados["cod_status"] == _STATUS_SUBMETIDO)
        {
            $obj->metadados["titulo"] = "<font color='blue'>".$obj->metadados["titulo"]."</font>";
        }
        $obj->metadados["checkbox"] = "";
        if ($_SESSION['usuario']['perfil'] < _PERFIL_AUTOR 
                || ($_SESSION['usuario']['perfil']==_PERFIL_AUTOR && $obj->valor("cod_usuario")==$_SESSION['usuario']['cod_usuario']))
        {
            $obj->metadados["checkbox"] .= '<input type="checkbox" '
                    . 'id="objlist_'.$obj->valor("cod_objeto").'" '
                    . 'name="objlist[]" '
                    . 'value="'.$obj->valor("cod_objeto").'" class="chkObj">';
        }
        
        $obj->metadados["acoes"] = "";
        if ($_SESSION['usuario']['perfil'] < _PERFIL_AUTOR 
                || ($_SESSION['usuario']['perfil']==_PERFIL_AUTOR && $obj->valor("cod_usuario")==$_SESSION['usuario']['cod_usuario']))
        {
            $obj->metadados["acoes"] .= '<a href="'.$page->config["portal"]["url"].'/do/edit/'.$obj->valor("cod_objeto").'.html" '
                    . 'title="Editar Objeto" '
                    . 'class="margin-left5" '
                    . 'rel="tooltip" '
                    . 'data-animate="animated fadeIn" '
                    . 'data-toggle="tooltip" '
                    . 'data-original-title="Editar Objeto" '
                    . 'data-placement="left" '
                    . 'title="Editar este objeto"><i class="fapbl fapbl-pencil-alt font-size16"></i></a>';
        }
        $obj->metadados["acoes"] .= "<a href='".$page->config["portal"]["url"].$obj->valor("url")."' "
                . "title='Exibir Objeto' "
                . "rel='tooltip' "
                . "data-animate='animated fadeIn' "
                . "data-toggle='tooltip' "
                . "data-original-title='Visualizar objeto' "
                . "data-placement='left' "
                . "title='Visualizar objeto' "
                . "class='margin-left5'><i class='fapbl fapbl-eye font-size16'></i></a>";
        if ($obj->podeTerFilhos())
        {
            $obj->metadados["acoes"] .= "<a href='".$page->config["portal"]["url"]."/do/list_content/".$obj->valor("cod_objeto").".html' "
                    . "title='Listar conteúdo' "
                    . "rel='tooltip' "
                    . "data-animate='animated fadeIn' "
                    . "data-toggle='tooltip' "
                    . "data-original-title='Listar conteúdo' "
                    . "data-placement='left' "
                    . "title='Listar conteúdo' "
                    . "class='margin-left5'><i class='fapbl fapbl-folder-open font-size16'></i></a>";
        }

        $array["data"][] = $obj->metadados;
        
    }
    
    echo(json_encode($array));
    exit();
//    $objetos = $page->adminobjeto->localizarObjetos('*', $qry, $ordem='', $inicio=-1, $limite=-1, $pai=-1, $niveis=-1, $apagados=false, $likeas='', $likenocase='', $tags='')
 //   xd($array);
}

$page->objeto->pegarListaFilhos('*');

$lstStatus = array("", "Privado", "Publicado", "Rejeitado", "Submetido");
?>
<style>trMouseAction1:hover { background: #fff; }</style>
<ul class="nav nav-tabs">
  <li class="active"><a href="do/list_content/<?php echo($page->objeto->valor('cod_objeto')) ?>.html">Listar Conteúdo</a></li>
  <li><a href="do/pilha/<?php echo($page->objeto->valor('cod_objeto')) ?>.html">Pilha</a></li>
</ul>
<script>
$(document).ready(function(){
    
    $.fn.dataTable.moment( 'DD/MM/YYYY' );
    
    $('#tabelaLista')
            .dataTable({
                "responsive": true,
                "language": linguagemDataTable,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "do/list_content/<?php echo($page->objeto->valor('cod_objeto')) ?>.html?naoincluirheader&ajaxtbl",
                    "type": "POST"
                },
                "order": [[ 2, "asc" ]],
                "columns": [
                    { 
                        "data": "checkbox",
                        "orderable": false,
                        "searchable": false
                    },
                    { "data": "cod_objeto" },
                    { "data": "titulo" },
                    { "data": "classe" },
                    { 
                        "data": "peso",
                        "searchable": false
                    },
                    { "data": "data_publicacao" },
                    { "data": "status" },
                    { 
                        "data": "acoes",
                        "orderable": false,
                        "searchable": false
                    }
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
<script src="include/javascript_datatable" type="text/javascript"></script>
<link href="include/css_datatable" rel="stylesheet" type="text/css"> 

<!-- === Listar Conteúdo === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Listar Conteúdo</b></h3></div>
	
    <form action="do/list_content_post/<?php echo($page->objeto->valor("cod_objeto")); ?>.html" name="listcontent" id="listcontent" method="post">
        <input type="hidden" name="return_obj" value="<?php echo($page->objeto->valor("cod_objeto")); ?>">

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
                        <div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($page->objeto->valor("titulo")); ?></h3></div>
                        <div class="col-sm-3 text-right titulo-icones">
                            <a class="ABranco" href="<?php echo($page->config["portal"]["url"]); ?><?php echo($page->objeto->valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
<?php 
if ($page->objeto->valor("cod_objeto") != $page->config["portal"]["objroot"])
{ 
?>
                            <a class="ABranco" href="do/list_content/<?php echo($page->objeto->valor("cod_pai"));?>.html" rel="tooltip" data-color-class = "primary" data-animate=" animated fadeIn" data-toggle="tooltip" data-original-title="Voltar para o pai" data-placement="left" title="Voltar para o pai"><i class='fapbl fapbl-ellipsis-h'></i></a>
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
                    </table>
                    <!-- === Final === Tabela Listar Conteúdo (DATATABLE) === -->

                </div>
            </div>
            <!-- === Final === Listar Conteúdo === -->

        </div>
    </form>
</div>
<!-- === Final === Listar Conteúdo === -->