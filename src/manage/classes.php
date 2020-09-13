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

$classinfo = array();
$cod_classe = 0;
$acao = isset($_REQUEST["acao"])?htmlspecialchars($_REQUEST["acao"], ENT_QUOTES, "UTF-8"):"";
if ($acao=="")
{
    
}
elseif ($acao=="edit" || $acao=="del")
{
    $cod_classe = isset($_REQUEST['cod_classe'])?htmlspecialchars($_REQUEST["cod_classe"], ENT_QUOTES, "UTF-8"):0;
    if ((int)$cod_classe > 0) $classinfo = $this->container["administracao"]->pegarInfoDaClasse($cod_classe);
//    xd($classinfo);
}

//xd("classes");
?>

<script type="text/javascript">
$("document").ready(function(){

<?php
if ($acao=="")
{
?>
        // $.fn.dataTable.ext.order.intl("pt");
    $('#tabelaLista')
        .dataTable({
            responsive: true,
            language: linguagemDataTable,
            order: [[ 1, "asc" ]],
        });
    
    $("#btn_addclasse").click(function(){
        document.location.href = "do/classes/<?php echo($this->container["objeto"]->valor("cod_objeto")); ?>.html?acao=new";
    });
    
<?php
}
elseif ($acao=="new" || $acao=="edit")
{
?>
    validacao = $("#formClasse").validate();
    
    $("#btnSelecionar").click(function(){
        var valor = $("#sctClasse").val();
        if (valor!="")
        {
            document.location.href="do/classes/<?php echo $this->container["objeto"]->valor("cod_objeto")?>.html?cod_classe=" + valor;
        }
    });
    $("input[name='temfilhos']").click(function(){
        var valor = $(this).val();
        var valordisabled = false;
        if (valor==0) valordisabled = true;
        
        $("input[name='podeconter[]']").each(function(){
            $(this).prop("disabled", valordisabled);
        });
    });
    $("#btn_add").click(function(){
        classeAdicionarPropriedade();
    });
    $("#btnadicionarobjeto").click(function(){
        classeAdicionarObjeto();
    });
    $("#btn_cancel").click(function(){
        document.location.href = "do/classes/<?php echo($this->container["objeto"]->valor("cod_objeto")); ?>.html";
    });
<?php
}
?>
    });
</script>
 
<ul class="nav nav-tabs">
    <li class="nav-item"><a class="nav-link " href="do/indexportal/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Informações do Portal</a></li>
    <li class="nav-item"><a class="nav-link " href="do/gerusuario/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Gerenciar usuários</a></li>
    <li class="nav-item"><a class="nav-link active" href="do/classes/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Gerenciar classes</a></li>
    <li class="nav-item"><a class="nav-link" href="do/peles/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Gerenciar Peles</a></li>
</ul>

    <div class="card">
        <div class="card-header bg-primary text-white"><h3><b>Gerenciar Classes</b></h3></div>

<?php
if ($acao=="")
{
?>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-12 text-center">
                    <input type="button" value="Adicionar Classe" name="btn_addclasse" id="btn_addclasse" class="btn btn-success" />
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- === Tabela Listar Conteúdo (DATATABLE) === -->
            <table id="tabelaLista" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Prefixo</th>
                        <th>Indexada</th>
                        <th>Sistema</th>
                        <th class="none">Descrição</th>
                        <th>A&ccedil;&otilde;es</th>
                    </tr>
                </thead>
                <tbody>
<?php
$this->container["administracao"]->carregarClasses();
foreach ($_SESSION["classes"] as $class)
{
?>
                    <tr>
                        <td><?php echo($class["cod_classe"]); ?></td>
                        <td><strong><?php echo($class["nome"]); ?></strong></td>
                        <td><?php echo($class["prefixo"]); ?></td>
                        <td><?php echo($class["indexar"]==0?"Não":"Sim"); ?></td>
                        <td><?php echo($class["sistema"]==0?"Não":"Sim"); ?></td>
                        <td><?php echo($class["descricao"]); ?></td>
                        <td>
                            <a href="do/classes/<?php echo($this->container["objeto"]->valor("cod_objeto")); ?>.html?acao=edit&cod_classe=<?php echo($class["cod_classe"]); ?>" title='Editar Classe' class='margin-left5' rel='tooltip' data-animate='animated fadeIn' data-toggle='tooltip' data-original-title='Editar Classe' data-placement='left' title='Editar Classe'><i class='fapbl fapbl-pencil-alt font-size16'></i></a>

                            <?php
                            if ($class["sistema"]==0) {
                            ?>
                            <a href="do/classes/<?php echo($this->container["objeto"]->valor("cod_objeto")); ?>.html?acao=del&cod_classe=<?php echo($class["cod_classe"]); ?>" title="Apagar Classe" rel='tooltip' data-animate='animated fadeIn' data-toggle='tooltip' data-original-title='Apagar Classe' data-placement='left' title='Apagar Classe' class='margin-left5'><i class='fapbl fapbl-times-circle font-size16'></i></a>
                            <?php
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
                        <th>Nome</th>
                        <th>Prefixo</th>
                        <th>Indexada</th>
                        <th>Sistema</th>
                        <th>Descrição</th>
                        <th>A&ccedil;&otilde;es</th>
                    </tr>
                </tfoot>
            </table>
            <!-- === Final === Tabela Listar Conteúdo (DATATABLE) === -->
        </div>
<?php
}
elseif ($acao == "edit" || $acao=="new")
{
?>
        <form action="do/classes_post/<?php echo $this->container["objeto"]->valor("cod_objeto")?>.html" method="post" name="formClasse" id="formClasse" enctype="multipart/form-data">
            <input type="hidden" name="cod_classe" value="<?php echo(isset($classinfo['classe']['cod_classe'])?$classinfo['classe']['cod_classe']:""); ?>">
            <input type="hidden" name="old_prefixo" value="<?php echo(isset($classinfo['classe']['prefixo'])?$classinfo['classe']['prefixo']:""); ?>">
            <input type="hidden" name="old_indexar" value="<?php echo(isset($classinfo['classe']['indexar'])?$classinfo['classe']['indexar']:""); ?>">
            <input type="hidden" name="old_temfilhos" value="<?php echo(isset($classinfo['classe']['temfilhos'])?$classinfo['classe']['temfilhos']:""); ?>">
            
            <div class="card-body">
                <div class="card">
                    <div class="card-header">Dados da Classe</div>
                    <div class="card-body">
                        <div class="row form-group">
                            <label for="txtNome" class="col-md-3 col-form-label">Nome</label>
                            <div class="col-md-9">
                                <input class="form-control required" type="text" name="nome" id="txtNome" value="<?php echo(isset($classinfo['classe']['nome'])?$classinfo['classe']['nome']:""); ?>">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="txtPrefixo" class="col-md-3 col-form-label">Prefixo</label>
                            <div class="col-md-9">
                                <input class="form-control required" type="text" name="prefixo" id="txtPrefixo" value="<?php echo(isset($classinfo['classe']['prefixo'])?$classinfo['classe']['prefixo']:""); ?>">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="txtDescricao" class="col-md-3 col-form-label">Descrição</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="4" name="descricao" id="txtDescricao"><?php echo(isset($classinfo['classe']['descricao'])?$classinfo['classe']['descricao']:""); ?></textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="rdoContem" class="col-md-3 col-form-label">Contém outros objetos?</label>
                            <div class="col-md-9">
                                <label class="radio-inline">
                                    <input type="radio" name="temfilhos" value="0" <?php if(!isset($classinfo['classe']['temfilhos']) || !$classinfo['classe']['temfilhos']) { echo("checked"); } ?>> Não
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="temfilhos" value="1" <?php if(isset($classinfo['classe']['temfilhos']) && $classinfo['classe']['temfilhos']) { echo("checked"); } ?>> Sim
                                </label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="rdoIndexar" class="col-md-3 col-form-label">Indexar para pesquisa?</label>
                            <div class="col-md-9">

                                <label class="radio-inline">
                                    <input type="radio" name="indexar" value="0" <?php if(!isset($classinfo['classe']['indexar']) || !$classinfo['classe']['indexar']) { echo("checked"); } ?>> Não
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="indexar" value="1" <?php if(isset($classinfo['classe']['indexar']) && $classinfo['classe']['indexar']) { echo("checked"); } ?>> Sim
                                </label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="arqIcone" class="col-md-3 col-form-label">Ícone da classe</label>
                            <div class="col-md-3">
            <?php
    if (isset($_GET['cod_classe']) && !empty($_GET['cod_classe']) && $_GET['cod_classe']>0)
    {
        $prefixo = $classinfo["classe"]["prefixo"];
        $mensagem = " <input type=\"checkbox\" name=\"apagar_icone\" id=\"apagar_icone\" value=\"apagar\" /> Apagar &iacute;cone.";
    } 
    else
    {
        $prefixo = "default";
        $mensagem = "&Iacute;cone n&atilde;o definido";
    }
    echo "<img src=\"blob/iconeclasse?nome=".$prefixo."\" border=\"0\" align=\"absmiddle\" /> ".$mensagem."<br />";
    ?>
                            </div>
                            <div class="col-md-6">
                                <input type="file" id="ic_classe" name="ic_classe" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mt-1">
                            <div class="card-header"><b>Pode conter</b></div>
                            <div class="card-body">
                                <div id="list-conter-classe">
                                    <ul>
    <?php
    $classes = isset($classinfo['todas'])?$classinfo['todas']:$this->container["administracao"]->pegarListaClasses($page);
//    xd($this->container["administracao"]->pegarListaClasses($page));
    $temfilhos = $cod_classe==0?0:(isset($classinfo['classe']['temfilhos'])?$classinfo['classe']['temfilhos']:0);
    foreach ($classes as $list)
    {
        echo "<li><label><input type='checkbox' value='".(isset($list["cod_classe"])?$list["cod_classe"]:$list["codigo"])."' name='podeconter[]' ".((isset($list["permitido"])&&$list["permitido"]=="1")?"checked":"")." ".(($temfilhos=="1")?"":"disabled")." /> ".(isset($list["nome"])?$list["nome"]:$list["texto"])."</label></li>";
    }
    ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mt-1">
                            <div class="card-header"><b>Pode ser criado nas classes</b></div>
                            <div class="card-body">
                                <div id="list-conter-classe">
                                    <ul>
    <?php
//    xd($classes);
    foreach ($classes as $list)
    {
        $codigo = isset($list["cod_classe"])?$list["cod_classe"]:(isset($list["codigo"])?$list["codigo"]:"");
        $texto = isset($list["nome"])?$list["nome"]:(isset($list["texto"])?$list["texto"]:"");
        echo "<li><label><input type='checkbox' value='".$codigo."' name='criadoem[]' ".((isset($list["criadoem"])&&$list["criadoem"]=="1")?"checked":"")." /> ".$texto."</label></li>";
    }
    ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-1">
                    <div class="card-header"><b>Pode ser criado em</b></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="incluirObjeto" id="txtPode" />
                            </div>
                            <div class="col-md-2">
                                <input type="button" value="Adicionar" class="btn btn-warning" id="btnadicionarobjeto" />
                            </div>
                            <div class="col-md-7 font-size14">
                                Use o campo texto para incluir objetos (por cod_objeto ou url_amigável)<br />
                                Separe as informações com vírgula se for mais de um objeto.
                            </div>
                        </div>
                        <div class="row">
                            <div class="col_md-12">
                                <div id="list-conter-classe">
                                    <ul id="listaobjetos">
                                        <li><b>(código) Título do objeto (URL amigável)</b></li>
    <?php
    $codigos_objetos = "";
    $contobjs = 0;
    if (isset($classinfo["objetos"]) && is_array($classinfo["objetos"]) && count($classinfo["objetos"])>0)
    {
        foreach ($classinfo["objetos"] as $obj)
        {
            $contobjs++;
    ?>
                                        <li id="linha_obj_<?php echo($contobjs); ?>"><input type="hidden" name="objetos[]" value="<?php echo($obj["cod_objeto"]); ?>" /><input type="hidden" name="objetosurls[]" value="<?php echo($obj["url_amigavel"]); ?>" />(<?php echo($obj["cod_objeto"]); ?>) <?php echo($obj["titulo"]); ?> - (/<?php echo($obj["url_amigavel"]); ?>) <a href="#" onclick="classeApagaObjeto('<?php echo($contobjs); ?>'); return false;"><i class="fapbl fapbl-times-circle" title="Remover"></i></a></li>
    <?php
            $codigos_objetos .= ",".$obj["cod_objeto"];
        }
        $codigos_objetos = substr($codigos_objetos, 1);
    }
    ?>
                                    </ul>
                                    <input type="hidden" id="numeroobjs" value="<?php echo($contobjs); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-1 modelo_propriedade" style="display: none;">
                    <div class="card-header">
                        <b>Propriedade <span class="numero" />0</span></b> <span class="codigo" /></span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group col_1_1">
                                    <input type="hidden" class="ativa" name="prop_ativa" id="prop_ativa" value="" />
                                    <label for="prop_nome" class="titulo">Prefixo</label>
                                    <input type="hidden" class="nomeatual" name="prop_nomeatual" id="prop_nomeatual" value="" />
                                    <input type="text" class="form-control campo required" id="prop_nome" name="prop_nome" placeholder="Prefixo da propriedade" value="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col_1_2">
                                    <label for="prop_rotulo" class="titulo">Rótulo</label>
                                    <input type="text" class="form-control campo required" id="prop_rotulo" name="prop_rotulo" placeholder="Rótulo da propriedade" value="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col_1_3">
                                    <label for="prop_tipodado" class="titulo">Tipo de Dado</label>
                                    <input type="hidden" class="campo2" name="prop_tipodado2" value="" />
                                    <select class="form-control campo required" name="prop_tipodado" id="prop_tipodado">
                                        <?php echo($this->container["administracao"]->dropdownTipoDado("", true)); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row linhabool padding-bottom10" style="display: none;">
                            <div class="col-md-4" style="vertical-align: middle;">
                                <b>Rótulos Booleano</b>
                            </div>
                            <div class="col-md-4 col_2_2">
                                <label for="prop_bol_1" class="titulo">Campo 1</label>
                                <input type="text" class="form-control campo" id="prop_bol_1" name="prop_bol_1" placeholder="Sim" value="Sim">
                            </div>
                            <div class="col-md-4 col_2_3">
                                <label for="prop_bol_0" class="titulo">Campo 0</label>
                                <input type="text" class="form-control campo" id="prop_bol_0" name="prop_bol_0" placeholder="Não" value="Não">
                            </div>
                        </div>
                        <div class="row linharef padding-bottom10" style="display: none;">
                            <div class="col-md-8 col_3_1">
                                <label for="prop_cod_referencia_classe" class="titulo">Classe de Referência</label>
                                <select class="form-control campo" name="prop_cod_referencia_classe" id="prop_cod_referencia_classe">
                                    <?php echo($this->container["administracao"]->dropdownClasses(0, true)); ?>
                                </select>
                            </div>
                            <div class="col-md-4 col_3_2">
                                <label for="prop_campo_ref" class="titulo">Campo de Referência</label>
                                <input type="text" class="form-control campo" id="prop_campo_ref" name="prop_campo_ref" placeholder="Campo de Referência" value="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group col_4_1">
                                    <label for="prop_valorpadrao" class="titulo">Valor Padrão</label>
                                    <input type="text" class="form-control campo" id="prop_valorpadrao" name="prop_valorpadrao" placeholder="Valor Padrão" value="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col_4_2">
                                    <label for="prop_posicao" class="titulo">Posição</label>
                                    <input type="number" class="form-control campo required" id="prop_posicao" name="prop_posicao" placeholder="Posição" value="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col_4_3">
                                    <label for="prop_seguranca" class="titulo">Segurança</label>
                                    <select class="form-control campo" name="prop_seguranca" id="prop_seguranca">
                                        <option value="<?php echo(_PERFIL_AUTOR); ?>">Autor</option>
                                        <option value="<?php echo(_PERFIL_EDITOR); ?>">Editor</option>
                                        <option value="<?php echo(_PERFIL_ADMINISTRADOR); ?>">Administrador</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col_5_1">
                                <div class="form-group">
                                    <label for="prop_descricao" class="titulo">Descrição</label>
                                    <textarea class="form-control campo" rows="2" name="prop_descricao" id="prop_descricao"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4 col_5_2 padding-top20">
                                <label class="checkbox-inline"><input class="campo" type="checkbox" value="1" name="prop_obrigatorio" id="prop_obrigatorio" /> Propriedade obrigatória</label>
                            </div>
                        </div>
                    </div>
                    <div class="container_alert"></div>
                    <div class="card-footer" style="text-align: right">
                        <input type="button" value="Apagar Propriedade" class="btn btn-danger btnapagar" />
                    </div>
                </div>
                <div class="alert alert-danger alert-dismissible modelo_apagar" role="alert" style="display: none;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">x</span></button>
                    <h4><b>Deseja realmente apagar esta propriedade?</b></h4>
                    <p>Ao apagar a propriedade estará apagando as informações dos objetos, caso tenha informações cadastradas.</p>
                    <p><button type="button" class="btn btn-danger apagar">Apagar</button> <button type="button" class="btn btn-default naoapagar" data-dismiss="alert" aria-label="Não Apagar">Não Apagar</button></p>
                </div>
                <input type="hidden" name="numeroPropriedades" id="numeroPropriedades" value="0" />
                    
                <div id="container_propriedades"></div>

                <div id="container_apagarclasse"></div>

                
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-8 text-center">
                        <input type="button" value="Adicionar Propriedade" name="btn_add" id="btn_add" class="btn btn-info" />
                    </div>
                    <div class="col-md-4 text-center">
                            <!--<input type="button" value="Apagar Classe" name="btn_del" id="btn_del" class="btn btn-danger" onclick="classeApagarClasse();" />-->
                            <input type="submit" value="Gravar" name="btn_gravar" id="btn_gravar" class="btn btn-success" />
                            <input type="button" value="Cancelar" name="btn_cancel" id="btn_cancel" class="btn btn-warning" />
                            <input type="hidden" name="apagar_classe" id="apagar_classe" value="0" />
                    </div>
                </div>
            </div>
        </form>
        <?php
    $cont = 0;
    if (isset($classinfo["prop"]))
    {
        echo "<script>";
        foreach ($classinfo["prop"] as $prop)
        {
            echo "classeAdicionarPropriedade('".$prop["cod_propriedade"]."', '".$prop["nome"]."', '".$prop["rotulo"]."', '".$prop["cod_tipodado"]."', '".$prop["valorpadrao"]."', '".$prop["posicao"]."', '".$prop["seguranca"]."', '".$prop["descricao"]."', '".$prop["obrigatorio"]."', '".$prop["rot1booleano"]."', '".$prop["rot2booleano"]."', '".$prop["cod_referencia_classe"]."', '".$prop["campo_ref"]."');";
        }
        echo "</script>";
    }

}
elseif ($acao=="del")
{
//    xd($classinfo);
?>    
        <p>&nbsp;</p>
        <form action="do/classes_post/<?php echo $this->container["objeto"]->valor("cod_objeto")?>.html" method="post" name="formClasse" id="formClasse" enctype="multipart/form-data">
        <input type="hidden" name="cod_classe" value="<?php echo($classinfo["classe"]["cod_classe"]); ?>">
        <input type="hidden" name="apagar_classe" id='apagar_classe' value="">
        <div class="alert alert-danger alert-dismissible modeloapagarclasse" role="alert" id="alertapagarclasse">
                    <h4><b>Deseja realmente apagar a classe '<?php echo($classinfo["classe"]["nome"]); ?>'?</b></h4>
    <?php
    if (isset($classinfo["obj_conta"]) && $classinfo["obj_conta"]>0)
    {
    ?>
                    <p>Seu portal contém <b><?php echo($classinfo["obj_conta"]); ?> objetos</b> desta classe que <b>SERÃO APAGADOS TAMBÉM!</b></p>
    <?php
    }
    else
    {
    ?>
                    <p>Seu portal não contém nenhum objeto desta classe.</p>
    <?php
    }
    ?>
                    <p><button type="button" class="btn btn-danger apagar" onclick="classeConfirmaApagarClasse()">Apagar</button> <button type="button" class="btn btn-default naoapagar" onclick="document.location.href='do/classes/<?php echo $this->container["objeto"]->valor("cod_objeto")?>.html';">Não Apagar</button></p>
                </div>
        </form>
<?php
}
?>
    </div>


	

            