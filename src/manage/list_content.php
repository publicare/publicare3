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
if (isset($_GET["ajaxtbl"]))
{
    $array = $this->container["adminobjeto"]->buscaObjetoJsonDatatable($_POST);
    echo($array);
    exit();
}

$this->container["objeto"]->pegarListaFilhos('*');

$lstStatus = array("", "Privado", "Publicado", "Rejeitado", "Submetido");
?>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" href="do/list_content/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Listar Conteúdo</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="do/pilha/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Pilha</a>
    </li>
</ul>

<script>
$(document).ready(function(){
    
    $('#tabelaLista').dataTable({
                "responsive": true,
                "language": linguagemDataTable,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "do/list_content/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html?naoincluirheader&ajaxtbl",
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
                    { "data": "data_validade" },
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

<!-- === Listar Conteúdo === -->
<div class="card">
    <div class="card-header bg-primary text-white"><h3><b>Listar Conteúdo</b></h3></div>
	
    <form action="do/list_content_post/<?php echo($this->container["objeto"]->valor("cod_objeto")); ?>.html" name="listcontent" id="listcontent" method="post">
        <input type="hidden" name="return_obj" value="<?php echo($this->container["objeto"]->valor("cod_objeto")); ?>">

        <div class="card-footer">
            <!-- === Botões (Inverter, Publicar, Despublicar, Apagar, Duplicar e Copiar para a pilha) === -->
            <div id="divBotoesAcao text-center">
                <input type="button" value="Inverter Sele&ccedil;&atilde;o" name="purge" class="btn btn-warning" id="btnInverter">
<?php
if ($_SESSION['usuario']['perfil'] <= _PERFIL_EDITOR)
{ 
?>
                <input type="submit" value="Publicar itens" name="publicar" class="btn btn-success btnAcao">
                <input type="submit" value="Despublicar itens" name="despublicar" class="btn btn-secondary btnAcao">
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
            </div>
            <!-- === Final === Botões (Inverter, Publicar, Despublicar, Apagar, Duplicar e Copiar para a pilha) === -->
            <!-- === Mensagem de ação === -->
            <div class="alert alert-warning alert-dismissible fade in" role="alert" id="divMensagemGravar" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">x</span></button>
                <h4>Processando informa&ccedil;&otilde;es .... aguarde....</h4>
            </div>
            <!-- === Final === Mensagem de ação === -->
        </div>

        <div class="card-body">
            <!-- === Listar Conteúdo === -->
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-9"><h3 style="line-height: 30px;"><?php echo($this->container["objeto"]->valor("titulo")); ?></h3></div>
                        <div class="col-sm-3 text-right titulo-icones">
                            <a class="ABranco" href="<?php echo($this->container["config"]->portal["url"]); ?><?php echo($this->container["objeto"]->valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
<?php 
if ($this->container["objeto"]->valor("cod_objeto") != $this->container["config"]->portal["objroot"])
{ 
?>
                            <a class="ABranco" href="do/list_content/<?php echo($this->container["objeto"]->valor("cod_pai"));?>.html" rel="tooltip" data-color-class = "primary" data-animate=" animated fadeIn" data-toggle="tooltip" data-original-title="Voltar para o pai" data-placement="left" title="Voltar para o pai"><i class='fapbl fapbl-ellipsis-h'></i></a>
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
                                <th>Código</th>
                                <th>T&iacute;tulo</th>
                                <th>Classe</th>
                                <th>Peso</th>
                                <th>Data publicação</th>
                                <th>Data validade</th>
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