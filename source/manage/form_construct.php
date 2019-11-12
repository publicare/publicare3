<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo form_construct.php monta o formulário de criação/edição de objetos
 * @copyright GPL © 2007
 * @package publicare/manage
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

global $_page, $action;

// Variaveis de definicao para estrutura de formulario
// data atual
$dataAtual = date("d/m/Y");
// hora atual
$horaAtual = date("H:i");
// data de validade do objeto
$dataValidade = "31/12/2036";
// lista de peles disponiveis
$peles = $_page->_administracao->PegaListaDePeles($_page);
// lista de views disponiveis
$views = $_page->_administracao->PegaListaDeViews($_page);
$dadosPai = array();

// Pegando dados da classe conforme ação.. criação ou edição
// Criação de objeto
if (strpos($action,"edit") === false)
{
    $classname = substr($action,strpos($action,'_')+1);
    $classe = $_page->_administracao->PegaInfoDaClasse($_page, $_page->_administracao->CodigoDaClasse($_page, $classname));
    $edit = false;
    $titulo = "Criar";
    // Resgata dados do objeto-pai para uso futuro
    $dadosPai = $_page->_adminobjeto->PegaDadosObjetoPeloID($_page, $_page->_objeto->Valor($_page, "cod_objeto"));
}
// Edição de objeto
else
{
    $classname = $_page->_objeto->Valor($_page, "prefixoclasse");
    $classe = $_page->_administracao->PegaInfoDaClasse($_page, $_page->_objeto->Valor($_page, "cod_classe"));
    $edit = true;
    $titulo = "Editar";
}

// view atual
$scriptAtual = ($edit)?$_page->_objeto->metadados['script_exibir']:"";
// codigo do usuario dono do objeto
$cod_usuario = ($edit)?$_page->_objeto->Valor($_page, "cod_usuario"):$_SESSION['usuario']['cod_usuario'];
// peso do objeto
$peso = ($edit)?$_page->_objeto->Valor($_page, "peso"):0;
// codigo do objeto pai
$cod_pai = ($edit)?$_page->_objeto->Valor($_page, "cod_pai"):$_page->_objeto->Valor($_page, "cod_objeto");
// código da pele
$cod_pele = ($edit)?$_page->_objeto->Valor($_page, "cod_pele"):(int)$dadosPai["cod_pele"];

// Redefinido para que o STATUS de todos os objetos, 
// independentemente do nivel do usuario, sejam sempre DESPUBLICADOS
$new_status = 0;
// o unico objeto que não pode ser despublicado é a página inicial, objeto _ROOT
if ($_page->_objeto->Valor($_page, "cod_objeto") == _ROOT) $new_status = _STATUS_PUBLICADO;
else $new_status = _STATUS_PRIVADO;
?>
<script src="/include/javascript_datepicker" type="text/javascript"></script>
<link href="/include/css_datepicker" rel="stylesheet" type="text/css">  

<form enctype="multipart/form-data" action="/do/obj_post/<?=$_page->_objeto->Valor($_page, "cod_objeto")?>.html" method="post" name="formobj" id="formobj">
    <input type="hidden" name="op" value="<?php if($edit){echo "edit";} ?>">
    <input type="hidden" name="cod_classe" value="<?php echo($classe["classe"]["cod_classe"]); ?>">
    <input type="hidden" name="cod_pai" value="<?php echo($cod_pai); ?>">
    <input type="hidden" name="cod_objeto" value="<?php echo($edit?$_page->_objeto->Valor($_page, "cod_objeto"):0); ?>">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3><strong><?php echo($titulo); ?> objeto</strong></h3>
            <p class="padding-top10">
<?php
    if ($edit)
    {
?>
                <strong>Editando</strong>: <?php echo($_page->_objeto->Valor($_page, "titulo")) ?> (<?php echo($_page->_objeto->Valor($_page, "cod_objeto")) ?>) - <strong>Classe</strong>: <?php echo($classe["classe"]["nome"]); ?> (<?php echo($classe["classe"]["cod_classe"]); ?>)</p>
<?php
    }
    else
    {
?>
                <strong>Criando em</strong>: <?php echo($_page->_objeto->Valor($_page, "titulo")) ?> (<?php echo($_page->_objeto->Valor($_page, "cod_objeto")) ?>),  <?php echo($_page->_objeto->Valor($_page, "classe")) ?> (<?php echo($_page->_objeto->Valor($_page, "cod_classe")) ?>) - <strong>Usando a classe</strong>: <?php echo($classe["classe"]["nome"]); ?> (<?php echo($classe["classe"]["cod_classe"]); ?>)</p>
<?php
    }
?>
        </div>
        <div class="panel-footer text-right">
            <!-- === Botões (Inverter, Publicar, Despublicar, Apagar, Duplicar e Copiar para a pilha) === -->
            <div class="divBotoesAcao">
                <input type="submit" value="Gravar" name="gravar" class="btn btn-info btnAcao">
<?php
if ($_SESSION['usuario']['perfil'] <= _PERFIL_EDITOR)
{
?>
                <input type="submit" value="Gravar e Publicar" name="gravarepublicar" class="btn btn-success btnAcao">
<?php
}
else
{
?>
                <input type="submit" value="Gravar e Solicitar" name="gravaresolicitar" class="btn btn-success btnAcao">
<?php
}
?>
                <input type="submit" value="Gravar e Inserir Outro" name="gravaroutro" class="btn btn-warning btnAcao">
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
            <div class="panel panel-info">
                <div class="panel-heading">
                    Dados do Objeto
                </div>
                <div class="panel-body">
                    <div class="row form-group">
                        <label class="col-md-3 col-form-label" for="titulo"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo t&iacute;tulo do objeto é obrigatório.' data-placement='top' title='O campo t&iacute;tulo do objeto é obrigatório.'></i> T&iacute;tulo do objeto <small><small>* <br />(#titulo)</small></small></label>
                        <div class="col-md-9">
                            <input type="text" name="titulo" id="titulo" class="form-control required" value="<?php echo($edit?$_page->_objeto->Valor($_page, "titulo"):"") ?>" />
                        </div>
                    </div>
                    
                    <div class="row form-group">
                        <label class="col-md-3 col-form-label" for="data_publicacao"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo data publica&ccedil;&atilde;o informa a data/hora a partir da qual o objeto ficar&aacute; vis&iacute;vel.' data-placement='top' title='O campo data publica&ccedil;&atilde;o informa a data/hora a partir da qual objeto ficar&aacute; vis&iacute;vel.'></i> Data publica&ccedil;&atilde;o <small><small>* <br />(#data_publicacao)</small></small></label>
                        <div class="col-md-3">
                            <input type="text" name="data_publicacao" id="data_publicacao" class="form-control required datepicker" value="<?php echo($edit?preg_replace("[\: ]", "", $_page->_objeto->Valor($_page, "data_publicacao")):($dataAtual." ".$horaAtual)) ?>"/>
                        </div>
                        <label class="col-md-3 col-form-label" for="data_validade"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo data validade informa a data/hora a partir da qual objeto deixar&aacute; de ser vis&iacute;vel.' data-placement='top' title='O campo data validade informa a data/hora a partir da qual o objeto deixar&aacute; de ser vis&iacute;vel.'></i> Data validade <small><small>* <br />(#data_validade)</small></small></label>
                        <div class="col-md-3">
                            <input type="text" name="data_validade" id="data_validade" class="form-control required datepicker" value="<?php echo($edit?preg_replace("[\: ]", "", $_page->_objeto->Valor($_page, "data_validade")):$dataValidade." ".$horaAtual) ?>" />
                        </div>
                    </div>
<?php
$propsegura = "";
$propobrigatoria = "";
foreach ($classe["prop"] as $prop)
{
    $obrigatorio = "";
    $visivel = "";
    $pos = "";
    $valor = $edit?$_page->_objeto->Valor($_page, $prop["nome"]):$prop["valorpadrao"];
    
    // Verifica se o campo e permitido para o PERFIL atual do usuario
    if ((int)$prop['seguranca'] < (int)$_SESSION['usuario']['perfil'])
    {
            $visivel = "display:none;";
            $propsegura .= "property___".$prop['nome'].",";
    }
    
    // Verifica se propriedade é obrigatória
    if ((int)$prop['obrigatorio'] == 1)
    {
        $propobrigatoria .= "property___".$prop['nome'].":".$prop["cod_tipodado"].",";
        $obrigatorio = "required";
        $pos = " <small><small>*</small></small> ";
    }
?>
                    <div class="row form-group" style="<?php echo($visivel); ?>">
                        <label class="col-md-3 col-form-label" for="property___<?php echo($prop["nome"]); ?>">
<?php
    if ($prop["descricao"]!="")
    {
?>
                            <i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='<?php echo($prop["descricao"]); ?>' data-placement='top' title='<?php echo($prop["descricao"]); ?>'></i> 
<?php
    }
?>
                            <?php echo($prop["rotulo"].$pos."<br /><small><small>(#".$prop["nome"].")</small></small>"); ?></label>
                        <div class="col-md-9">
<?php
    switch ($prop["cod_tipodado"])
    {
        // blob
        case 1:
            if ($valor != "") 
            {
                echo "<strong>Arquivo:</strong> ".$valor." - <strong>cod_blob:</strong> ".$_page->_objeto->propriedades[$prop["nome"]]["cod_blob"];
                echo " | <label><input type='checkbox' id='property___".$prop['nome']."' name='property___".$prop['nome']."^delete' value='1'> Apagar arquivo</label>";
                $obrigatorio = "";
            }
?>
                            <input type="file" id="property___<?php echo($prop["nome"]); ?>" name="property___<?php echo($prop["nome"]); ?>" style="border: 1px solid #ccc; width: 100%;" class="<?php echo($obrigatorio); ?>">
                            <!--<input type="file" name="property___<?php echo($prop["nome"]); ?>" class="custom-file-input" />-->
<?php
            break;
        // booleano
        case 2:
?>
                            <label class="radio-inline"> <input type="radio" name="property___<?php echo($prop["nome"]); ?>" value="1" <?php if($valor==1) {echo "checked";} ?> /> <?php echo($prop["rot1booleano"]); ?></label>
                            <label class="radio-inline"> <input type="radio" name="property___<?php echo($prop["nome"]); ?>" value="0" <?php if($valor==0) {echo "checked";} ?> /> <?php echo($prop["rot2booleano"]); ?></label>
<?php
            break;
        // data
        case 3:
?>
                            <input type="text" name="property___<?php echo($prop["nome"]); ?>" id="property___<?php echo($prop["nome"]); ?>" class="form-control data <?php echo($obrigatorio); ?>" value="<?php echo($valor); ?>" />
<?php
            break;
        // número
        case 4:
?>
                            <input type="number" name="property___<?php echo($prop["nome"]); ?>" id="property___<?php echo($prop["nome"]); ?>" class="form-control number <?php echo($obrigatorio); ?>" value="<?php echo($valor); ?>" />
<?php
            break;
        // número preciso
        case 5:
?>
                            <input type="text" name="property___<?php echo($prop["nome"]); ?>" id="property___<?php echo($prop["nome"]); ?>" class="form-control numeropreciso <?php echo($obrigatorio); ?>" value="<?php echo($valor); ?>" />
<?php
            break;
        // ref objeto
        case 6:
            $objs = $_page->_administracao->PegaListaDeObjetos($_page, $prop["cod_referencia_classe"], $prop["campo_ref"]);
?>
                            <select class="form-control <?php echo($obrigatorio); ?>" name="property___<?php echo($prop["nome"]); ?>" id="property___<?php echo($prop["nome"]); ?>">
                                <option value="">. selecione .</option>
<?php
            foreach ($objs as $obj)
            {
?>
                                <option value="<?php echo($obj["codigo"]); ?>" <?php if($valor==$obj["texto"]) {echo("selected");} ?>><?php echo($obj["texto"]); ?></option>
<?php
            }
?>
                            </select>
<?php
            break;
        // string
        case 7:
?>
                            <input type="text" name="property___<?php echo($prop["nome"]); ?>" id="property___<?php echo($prop["nome"]); ?>" class="form-control <?php echo($obrigatorio); ?>" value="<?php echo($valor); ?>" />
<?php
            break;
        // texto avançado
        case 8:
?>
                            <textarea name="property___<?php echo($prop["nome"]); ?>" id="property___<?php echo($prop["nome"]); ?>" class="form-control texto-avancado avancado <?php echo($obrigatorio); ?>"><?php echo($valor); ?></textarea>
                            
                            <script>
                                new Jodit('#property___<?php echo($prop["nome"]); ?>', {
                                    "uploader": {
                                        "insertImageAsBase64URI": true
                                    },
                                    "language": "pt_br",
                                    "direction": "ltr"
                                });
//                                CKEDITOR.replace('property___<?php echo($prop["nome"]); ?>');
                            </script>
<?php
            break;
    }
?>
                        </div>
                    </div>
<?php
}
$obrigatorio = "";
$valor = "";
$propsegura = substr($propsegura, 0, strlen($propsegura)-1);
$propobrigatoria = substr($propobrigatoria, 0, strlen($propobrigatoria)-1);
?>
                    <input type="hidden" name="propriedade_segura" value="<?php echo($propsegura); ?>" />
                    <input type="hidden" name="propriedade_obrigatoria" value="<?php echo($propobrigatoria); ?>" />
                </div>
            </div>
            <div class="panel panel-info">
                <div class="panel-heading">
                    Dados avançados
                </div>
                <div class="panel-body">
                    <div class="row form-group">
                        <label class="col-md-3 col-form-label" for="descricao"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo Descri&ccedil;&atilde;o normalmente &eacute; utilizado na MetaTag Description, para indexa&ccedil;&atilde;o por sites de busca.' data-placement='top' title='O campo descri&ccedil;&atilde;o normalmente &eacute; utilizado na MetaTag Description, para indexa&ccedil;&atilde;o por sites de busca.'></i> Descri&ccedil;&atilde;o <small><small><br />(#descricao)</small></small></label>
                        <div class="col-md-9">
                            <textarea name="descricao" id="descricao" class="form-control"><?php echo($edit?$_page->_objeto->Valor($_page, "descricao"):""); ?></textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-3 col-form-label" for="url_amigavel"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo URL Amigável define o endereço do objeto. Ex: Para o objeto "Página Inicial" do site www.site.com.br, a URL Amigável pode ser "pagina-inicial", ficando "www.site.com.br/pagina-inicial".' data-placement='top' title='O campo URL Amigável define o endereço do objeto. Ex: Para o objeto "Página Inicial" do site www.site.com.br, a URL Amigável pode ser "pagina-inicial", ficando "www.site.com.br/pagina-inicial".'></i> URL Amigável <small><small><br />(#url_amigavel)</small></small></label>
                        <div class="col-md-9">
                            <input type="text" name="url_amigavel" id="url_amigavel" class="form-control" value="<?php echo($edit?$_page->_objeto->Valor($_page, "url_amigavel"):""); ?>" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-3 col-form-label" for="tags"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo TAGS normalmente &eacute; utilizado na MetaTag KeyWords, para indexa&ccedil;&atilde;o por sites de busca. Informe as tags separadas por vírgula.' data-placement='top' title='O campo TAGS normalmente &eacute; utilizado na MetaTag KeyWords, para indexa&ccedil;&atilde;o por sites de busca. Informe as tags separadas por vírgula.'></i> TAGS <small><small><br />(#tags)</small></small></label>
                        <div class="col-md-9">
                            <textarea name="tags" id="tags" class="form-control"><?php echo($edit?$_page->_objeto->Valor($_page, "tags"):""); ?></textarea>
                        </div>
                    </div>                    
                    <div class="row form-group">
                        <label class="col-md-3 col-form-label" for="cod_pele"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='A pele do objeto...' data-placement='top' title='A pele do objeto...'></i> Pele <small><small><br />(#cod_pele)</small></small></label>
                        <div class="col-md-9">
                            <select class="form-control" name="cod_pele" id="cod_pele">
                                <option value="">- Pele Padrão -</option>
<?php

foreach ($peles as $pele)
{
?>
                                <option value="<?php echo($pele["codigo"]); ?>" <?php if($pele["codigo"]==$cod_pele) {echo("selected");} ?>><?php echo($pele["texto"]); ?></option>
<?php
}
?>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-3 col-form-label" for="script_exibir"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='A pele do objeto...' data-placement='top' title='A pele do objeto...'></i> Script de exibição    </label>
                        <div class="col-md-9">
                            <select class="form-control" name="script_exibir" id="script_exibir">
                                <option value="">. selecione .</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-3 col-form-label" for="cod_usuario"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo "Dono do objeto" indica qual usuário será o responsável pelo objeto.' data-placement='top' title='O campo "Dono do objeto" indica qual usuário será o responsável pelo objeto.'></i> Dono do objeto</label>
                        <div class="col-md-9">
<?php
$usuarios = $_page->_administracao->PegaListadeDependentes($_page, $cod_usuario);
if ($usuarios === false)
{
?>
                            <input type="hidden" value="<?php echo($cod_usuario); ?>" name="cod_usuario" /> Reservado ao administrador
<?php
}
else
{
?>
                            <select class="form-control required" name="cod_usuario" id="cod_usuario">
                                <option value="">. selecione .</option>
<?php
    foreach ($usuarios as $usu)
    {
?>
                                <option value="<?php echo($usu["codigo"]); ?>" <?php if($cod_usuario == $usu["codigo"]) {echo "selected";} ?>><?php echo("(" . $usu["secao"] . ") " . $usu["texto"]); ?></option>

<?php
    }
?>
                            </select>
<?php
}
?>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-3 col-form-label" for="peso"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo "Peso" normalmente é utilizado para ordenação dos objetos.' data-placement='top' title='O campo "Peso" normalmente é utilizado para ordenação dos objetos.'></i> Peso <small><small><br />(#peso)</small></small></label>
                        <div class="col-md-9">
                            <input type="number" name="peso" id="peso" class="form-control required" value="<?php echo($peso); ?>" />
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="panel-footer text-right">
            <!-- === Botões (Inverter, Publicar, Despublicar, Apagar, Duplicar e Copiar para a pilha) === -->
            <div class="divBotoesAcao">
                <input type="submit" value="Gravar" name="gravar" class="btn btn-info btnAcao">
<?php
if ($_SESSION['usuario']['perfil'] <= _PERFIL_EDITOR)
{
?>
                <input type="submit" value="Gravar e Publicar" name="gravarepublicar" class="btn btn-success btnAcao">
<?php
}
else
{
?>
                <input type="submit" value="Gravar e Solicitar" name="gravaresolicitar" class="btn btn-success btnAcao">
<?php
}
?>
                <input type="submit" value="Gravar e Inserir Outro" name="gravaroutro" class="btn btn-warning btnAcao">
            </div>
            <!-- === Final === Botões (Inverter, Publicar, Despublicar, Apagar, Duplicar e Copiar para a pilha) === -->
            <!-- === Mensagem de ação === -->
            <div class="alert alert-warning alert-dismissible fade in" role="alert" id="divMensagemGravar" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">x</span></button>
                <h4>Processando informa&ccedil;&otilde;es .... aguarde....</h4>
            </div>
            <!-- === Final === Mensagem de ação === -->
        </div>
    </div>
</form>

<script type="text/javascript">
var peles = <?php echo(json_encode($peles)); ?>;
var views = <?php echo(json_encode($views)); ?>;

$("document").ready(function(){
    
    objetoCarregaSelectViews("script_exibir", "cod_pele", peles, views, "/html/skin/", "/html/template/", "<?php echo($scriptAtual); ?>");
    
    $("#cod_pele").change(function(){
        objetoCarregaSelectViews("script_exibir", "cod_pele", peles, views, "/html/skin/", "/html/template/", "<?php echo($scriptAtual); ?>");
    });

    $('.datepicker').datetimepicker(config_datetime);
    $('.data').datetimepicker(config_date);
    $(".numeropreciso").mask('#.##0,00', { reverse: true });
    
    $("#formobj").validate({
        ignore: [],
        rules: {
<?php
$vprops = preg_split("[,]", $propobrigatoria);
foreach ($vprops as $prop)
{
    $vvprops = preg_split("[:]", $prop);
    if (isset($vvprops[1]) && $vvprops[1] == "8")
    {
        echo "\t\t\t".$vvprops[0].": {required: function(){CKEDITOR.instances.".$vvprops[0].".updateElement();}, minlength:10},\n";
    }
}
?>
        },
        submitHandler: function(form) {
            $("#divMensagemGravar").show();
            $(".divBotoesAcao").hide();
            form.submit();
        }
    });
});
</script>
