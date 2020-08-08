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

global $page, $action;

// Variaveis de definicao para estrutura de formulario
// data atual
$dataAtual = date("d/m/Y");
// hora atual
$horaAtual = date("H:i");
// data de validade do objeto
$dataValidade = date("d/m/Y", time() + (60*60*24*365*20));
// lista de peles disponiveis
$peles = $page->administracao->pegarListaPeles();
// lista de views disponiveis
$views = $page->administracao->pegarListaViews($page);
$dadosPai = array();
$edit = false;

// Pegando dados da classe conforme ação.. criação ou edição
// Criação de objeto
if (strpos($action,"edit") === false)
{
    $classname = substr($action,strpos($action,'_')+1);
//    xd($action);
    $classe = $page->administracao->pegarInfoDaClasse($page->administracao->codigoClasse($classname));
    $titulo = "Criar";
    // Resgata dados do objeto-pai para uso futuro
    $dadosPai = $page->adminobjeto->pegarDadosObjetoId($page->objeto->valor("cod_objeto"));
}
// Edição de objeto
else
{
    $classname = $page->objeto->valor("prefixoclasse");
    $classe = $page->administracao->pegarInfoDaClasse($page->objeto->valor("cod_classe"));
    $edit = true;
    $titulo = "Editar";
}

$objeto = clone $page->objeto;

$versao = isset($_GET['v'])?(int)htmlspecialchars($_GET["v"], ENT_QUOTES, "UTF-8"):0;
if ($versao > 0)
{
    $objtmp = $page->administracao->pegarVersao($versao);
    if (is_array($objtmp) && count($objtmp)>0)
    {
        $conteudo = json_decode($objtmp[0]["conteudo"], true);
        if ($conteudo["cod_objeto"] == $objeto->valor("cod_objeto"))
        {
            $objeto->metadados = $conteudo["metadados"];
            $objeto->propriedades = $conteudo["propriedades"];
            $objeto->data_versao = $objtmp[0]["data_criacao"];
        }
    }
}

// view atual
$scriptAtual = ($edit)?$page->objeto->metadados['script_exibir']:"";
// codigo do usuario dono do objeto
$cod_usuario = ($edit)?$page->objeto->valor("cod_usuario"):$_SESSION['usuario']['cod_usuario'];
// peso do objeto
$peso = ($edit)?$objeto->valor("peso"):0;
// codigo do objeto pai
$cod_pai = ($edit)?$page->objeto->valor("cod_pai"):$page->objeto->valor("cod_objeto");
// código da pele
$cod_pele = ($edit)?$objeto->valor("cod_pele"):(int)$dadosPai["cod_pele"];

// Redefinido para que o STATUS de todos os objetos, 
// independentemente do nivel do usuario, sejam sempre DESPUBLICADOS
$new_status = 0;
// o unico objeto que não pode ser despublicado é a página inicial, objeto _ROOT
if ($page->objeto->valor("cod_objeto") == $page->config["portal"]["objroot"]) $new_status = _STATUS_PUBLICADO;
else $new_status = _STATUS_PRIVADO;


?>
<script src="include/javascript_datepicker" type="text/javascript"></script>
<link href="include/css_datepicker" rel="stylesheet" type="text/css">  

<form enctype="multipart/form-data" action="do/obj_post/<?=$page->objeto->valor("cod_objeto")?>.html" method="post" name="formobj" id="formobj">
    <input type="hidden" name="op" value="<?php echo($edit===true?"edit":"create"); ?>">
    <input type="hidden" name="cod_classe" value="<?php echo($classe["classe"]["cod_classe"]); ?>">
    <input type="hidden" name="cod_pai" value="<?php echo($cod_pai); ?>">
    <input type="hidden" name="cod_objeto" value="<?php echo($edit?$page->objeto->valor("cod_objeto"):0); ?>">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3><strong><?php echo($titulo); ?> objeto</strong></h3>
            <p class="padding-top10">
<?php
    if ($edit)
    {
?>
                <strong>Editando</strong>: <?php echo($objeto->valor("titulo")) ?> (<?php echo($page->objeto->valor("cod_objeto")) ?>) - <strong>Classe</strong>: <?php echo($classe["classe"]["nome"]); ?> (<?php echo($classe["classe"]["cod_classe"]); ?>) [<?php echo($classe["classe"]["prefixo"]); ?>]<br />
            
                
<?php
    }
    else
    {
?>
                <strong>Criando em</strong>: <?php echo($page->objeto->valor("titulo")) ?> (<?php echo($page->objeto->valor("cod_objeto")) ?>),  <?php echo($page->objeto->valor("classe")) ?> (<?php echo($page->objeto->valor("cod_classe")) ?>) - <strong>Usando a classe</strong>: <?php echo($classe["classe"]["nome"]); ?> (<?php echo($classe["classe"]["cod_classe"]); ?>) [<?php echo($classe["classe"]["prefixo"]); ?>] 
<?php
    }
?>
            </p>
        </div>
        <div class="panel-footer text-right">
            <!-- === Botões (Inverter, Publicar, Despublicar, Apagar, Duplicar e Copiar para a pilha) === -->
            <div class="divBotoesAcao">
                <div class="row">
                    <div class="col-md-4 text-left">
<?php
if ($edit === true)
{
    $versoes = $page->administracao->pegarVersoes($page->objeto->valor("cod_objeto"));
    usort($versoes, function($a, $b){ return $a["versao"]<$b["versao"]; });
?>
                        <strong>Vers&atilde;o</strong>: <?php echo($objeto->valor("versao")) ?> 
                        | 
                        <select id="alteraVersao">
<?php
    foreach ($versoes as $ver)
    {
?>
                            <option value="<?php echo($ver["cod_versaoobjeto"]); ?>"><?php echo($ver["versao"]); ?></option>
<?php
    }
?>
                        </select>
                        <input type="button" class="btn btn-default" value="Carregar" id="btnAlteraVersao">
<?php
    if ($page->objeto->valor("versao") != $objeto->valor("versao"))
    {
?>
                        <br />
                        <strong>Atenção!</strong> Esta é uma versão antiga do objeto. Criada em <strong><?php echo($objeto->data_versao); ?></strong>.
<?php
    }
}
?>
                    </div>
                    <div class="col-md-8 text-right">
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
<?php
if ($edit === true && $page->usuario->cod_perfil == _PERFIL_ADMINISTRADOR)
{
?>
                <a href="do/qrcode/<?php echo($page->objeto->valor('cod_objeto')) ?>.html" class="btn btn-default">Gerar QRCode</a>
<?php                
}
?>
                    </div>
                </div>
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
                            <input type="text" name="titulo" id="titulo" class="form-control required" value="<?php echo($edit?$objeto->valor("titulo"):"") ?>" />
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
    $valor = $edit?$objeto->valor($prop["nome"]):$prop["valorpadrao"];
    
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
                echo "<strong>Arquivo:</strong> ".$valor." - <strong>cod_blob:</strong> ".$page->objeto->propriedades[$prop["nome"]]["cod_blob"];
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
            $objs = $page->administracao->pegarListaObjetos($prop["cod_referencia_classe"], $prop["campo_ref"]);
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
                            <textarea name="property___<?php echo($prop["nome"]); ?>" id="property___<?php echo($prop["nome"]); ?>" class="form-control texto-avancado avancado"><?php echo($valor); ?></textarea>
                            
                            <script>
                                new Jodit('#property___<?php echo($prop["nome"]); ?>', {
                                    "uploader": {
                                        "insertImageAsBase64URI": true
                                    },
                                    "language": "pt_br",
                                    "height": 350
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
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            SEO
                        </div>
                        <div class="panel-body">
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label" for="descricao"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo Descri&ccedil;&atilde;o normalmente &eacute; utilizado na MetaTag Description, para indexa&ccedil;&atilde;o por sites de busca.' data-placement='top' title='O campo descri&ccedil;&atilde;o normalmente &eacute; utilizado na MetaTag Description, para indexa&ccedil;&atilde;o por sites de busca.'></i> Descri&ccedil;&atilde;o <small><small><br />(#descricao)</small></small></label>
                                <div class="col-md-8">
                                    <textarea name="descricao" id="descricao" class="form-control"><?php echo($edit?$objeto->valor("descricao"):""); ?></textarea>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label" for="url_amigavel"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo URL Amigável define o endereço do objeto. Ex: Para o objeto "Página Inicial" do site www.site.com.br, a URL Amigável pode ser "pagina-inicial", ficando "www.site.com.br/pagina-inicial".' data-placement='top' title='O campo URL Amigável define o endereço do objeto. Ex: Para o objeto "Página Inicial" do site www.site.com.br, a URL Amigável pode ser "pagina-inicial", ficando "www.site.com.br/pagina-inicial".'></i> URL Amigável <small><small><br />(#url_amigavel)</small></small></label>
                                <div class="col-md-8">
                                    <input type="text" name="url_amigavel" id="url_amigavel" class="form-control" value="<?php echo($edit?$objeto->valor("url_amigavel"):""); ?>" />
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label" for="tags"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo TAGS normalmente &eacute; utilizado na MetaTag KeyWords, para indexa&ccedil;&atilde;o por sites de busca. Informe as tags separadas por vírgula.' data-placement='top' title='O campo TAGS normalmente &eacute; utilizado na MetaTag KeyWords, para indexa&ccedil;&atilde;o por sites de busca. Informe as tags separadas por vírgula.'></i> TAGS <small><small><br />(#tags)</small></small></label>
                                <div class="col-md-8">
                                    <textarea name="tags" id="tags" class="form-control"><?php echo($edit?$objeto->valor("tags"):""); ?></textarea>
                                </div>
                            </div> 
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label" for="peso"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo "Peso" normalmente é utilizado para ordenação dos objetos.' data-placement='top' title='O campo "Peso" normalmente é utilizado para ordenação dos objetos.'></i> Peso <small><small><br />(#peso)</small></small></label>
                                <div class="col-md-8">
                                    <input type="number" name="peso" id="peso" class="form-control required" value="<?php echo($peso); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Dados avançados
                        </div>
                        <div class="panel-body">
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label" for="data_publicacao"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo data publica&ccedil;&atilde;o informa a data/hora a partir da qual o objeto ficar&aacute; vis&iacute;vel.' data-placement='top' title='O campo data publica&ccedil;&atilde;o informa a data/hora a partir da qual objeto ficar&aacute; vis&iacute;vel.'></i> Data publica&ccedil;&atilde;o <small><small>* <br />(#data_publicacao)</small></small></label>
                                <div class="col-md-8">
                                    <input type="text" name="data_publicacao" id="data_publicacao" class="form-control required datepicker" value="<?php echo($edit?preg_replace("[\: ]", "", $objeto->valor("data_publicacao")):($dataAtual." ".$horaAtual)) ?>"/>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label" for="data_validade"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo data validade informa a data/hora a partir da qual objeto deixar&aacute; de ser vis&iacute;vel.' data-placement='top' title='O campo data validade informa a data/hora a partir da qual o objeto deixar&aacute; de ser vis&iacute;vel.'></i> Data validade <small><small>* <br />(#data_validade)</small></small></label>
                                <div class="col-md-8">
                                    <input type="text" name="data_validade" id="data_validade" class="form-control required datepicker" value="<?php echo($edit?preg_replace("[\: ]", "", $objeto->valor("data_validade")):$dataValidade." ".$horaAtual) ?>" />
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label" for="cod_pele"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='A pele do objeto...' data-placement='top' title='A pele do objeto...'></i> Pele <small><small><br />(#cod_pele)</small></small></label>
                                <div class="col-md-8">
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
                                <label class="col-md-4 col-form-label" for="script_exibir"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='A pele do objeto...' data-placement='top' title='A pele do objeto...'></i> Script de exibição <small><small><br />(#script_exibir)</small></small></label>
                                <div class="col-md-8">
                                    <select class="form-control" name="script_exibir" id="script_exibir">
                                        <option value="">. selecione .</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-md-4 col-form-label" for="cod_usuario"><i class="fapbl fapbl-info-circle" rel='tooltip' data-color-class='primary' data-animate=' animated fadeIn' data-toggle='tooltip' data-original-title='O campo "Dono do objeto" indica qual usuário será o responsável pelo objeto.' data-placement='top' title='O campo "Dono do objeto" indica qual usuário será o responsável pelo objeto.'></i> Dono do objeto <small><small><br />(#cod_usuario)</small></small></label>
                                <div class="col-md-8">
        <?php
        $usuarios = $page->administracao->pegarListaDependentes($cod_usuario);
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
        <?php
        //}
        ?>
                            
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
<?php
if ($edit === true && $page->usuario->cod_perfil == _PERFIL_ADMINISTRADOR)
{
?>
                <a href="do/qrcode/<?php echo($page->objeto->valor('cod_objeto')) ?>.html" class="btn btn-default">Gerar QRCode</a>
<?php                
}
?>
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
    
    $("#btnAlteraVersao").click(function(){
        var versao = $("#alteraVersao").val();
        document.location.href = window.location.protocol + "//" + 
                (window.location.host + "/" + window.location.pathname + "?v=" + versao).replace("//", "/");
    });
});
</script>
