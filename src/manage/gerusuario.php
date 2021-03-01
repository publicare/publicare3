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

use Pbl\Core\Objeto;
use Pbl\Core\Usuario;

// pega lista completa de usuários
$usuarios = $this->container["usuario"]->listarUsuarios(1);
if (isset($_GET["ajaxtbl"]))
{
    //xd($_POST);
    $busca = isset($_POST["search"]["value"])&&$_POST["search"]["value"]!=""?htmlspecialchars($_POST["search"]["value"], ENT_QUOTES, "UTF-8"):"";
    $draw = isset($_POST["draw"])&&$_POST["draw"]!=""?htmlspecialchars($_POST["draw"], ENT_QUOTES, "UTF-8"):"1";
    $inicio = isset($_POST["start"])&&$_POST["start"]?(int)htmlspecialchars($_POST["start"], ENT_QUOTES, "UTF-8"):-1;
    $limite = isset($_POST["length"])&&$_POST["length"]?(int)htmlspecialchars($_POST["length"], ENT_QUOTES, "UTF-8"):-1;
    
    $ordem = isset($_POST["order"][0]["column"])?$_POST["columns"][(int)$_POST["order"][0]["column"]]["data"]:"";
    $direcao = isset($_POST["order"][0]["dir"]) && $_POST["order"][0]["dir"]=="desc"?"desc":"asc";
    
    $usuarios = $this->container["usuario"]->listarUsuarios($busca, $ordem, $direcao, $inicio, $limite);
    $usuarios2 = $this->container["usuario"]->listarUsuarios($busca, "", $direcao, -1, -1);
    $usuarios3 = $this->container["usuario"]->listarUsuarios();
    $array = array(
        "draw" => $draw,
        "recordsTotal" => count($usuarios3),
        "recordsFiltered" => count($usuarios2),
        "data" => array()
    );
    
    foreach ($usuarios as $usu)
    {
        $usu["permissoes"] = '';
        $permissoes = $this->container["usuario"]->pegarDireitosUsuario($usu["cod_usuario"]);
        foreach ($permissoes as $cod=>$perm)                          
        {
            $objtemp = new Objeto($this->container, $cod);
            $usu["permissoes"] .= "<br/>".$objtemp->valor("titulo")." <strong>(".$cod.")</strong> - ".$this->container["usuario"]->pegarNomePerfil($perm);
        }
        
        $usu["data_atualizacao"] = ConverteData($usu['data_atualizacao'], 5);
        
        $usu["acoes"] = '';
        $usu["acoes"] .= '<a href="do/gerusuario/'.$this->container["objeto"]->valor('cod_objeto').'.html?acao=editar&cod='.$usu["cod_usuario"].'" '
                . ' title="Editar Usuário" '
                . 'rel="tooltip" '
                . 'data-animate="animated fadeIn" '
                . 'data-toggle="tooltip" '
                . 'data-original-title="Editar Usuário" '
                . 'data-placement="left" '
                . 'title="Editar este usuário"><i class="fapbl fapbl-pencil-alt"></i></a> ';
        $usu["acoes"] .= '<a href="do/gerusuario/'.$this->container["objeto"]->valor('cod_objeto').'.html?acao=bloquear&cod='.$usu["cod_usuario"].'" '
                . ' title="Apagar Usuário" '
                . 'rel="tooltip" '
                . 'data-animate="animated fadeIn" '
                . 'data-toggle="tooltip" '
                . 'data-original-title="Apagar Usuário" '
                . 'data-placement="left" '
                . 'title="Apagar este usuário"><i class="fapbl fapbl-times-circle"></i></a> ';
        
        $array["data"][] = $usu;
        
    }
    
    echo(json_encode($array));
    exit();
}
//xd($usuarios);
$data_validade = strftime("%Y%m%d", strtotime("+6 month"));

$acao = isset($_REQUEST["acao"]) ? htmlspecialchars($_REQUEST["acao"], ENT_QUOTES, "UTF-8") : "";
$cod = isset($_REQUEST["cod"]) ? (int)htmlspecialchars($_REQUEST["cod"], ENT_QUOTES, "UTF-8") : 0;

?>
<!-- === Menu === --> 
<ul class="nav nav-tabs">
    <li class="nav-item"><a class="nav-link " href="do/indexportal/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Informações do Portal</a></li>
    <li class="nav-item"><a class="nav-link active" href="do/gerusuario/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Gerenciar usuários</a></li>
    <li class="nav-item"><a class="nav-link" href="do/classes/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Gerenciar classes</a></li>
    <li class="nav-item"><a class="nav-link" href="do/peles/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Gerenciar Peles</a></li>
</ul>
<!-- === FInal === Menu === -->

<!-- === Gerenciar Usuários === -->
<div class="card">
    <div class="card-header bg-primary text-white"><h3><b>Gerenciar Usu&aacute;rios</b></h3></div>
<?php
if ($acao == "")
{
?>
    <div class="card-footer text-center" >
        <input type="button" value="Adicionar usuário" class="btn btn-success" id="btnAdicionar" />
    </div>

    
    <div class="card-body">
        <script src="include/javascript_datatable" type="text/javascript"></script>
        <link href="include/css_datatable" rel="stylesheet" type="text/css"> 
        
        <div class="card">
            <div class="card-header">
                <h3 style="line-height: 30px;">Usuários cadastrados</h3>
            </div>
            <div class="card-body">
                
                <table id="tabela_usuario" class="table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Login</th>
                            <th>E-mail</th>
                            <th>Seção</th>
                            <th>Ramal</th>
                            <th class="none">Validade</th>
                            <th class="none">Permissões</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    
                </table>
            </div>
        </div>
    </div>
<script>
$(document).ready(function() {
    $('#tabela_usuario').DataTable({
        "responsive": true,
        "columnDefs": [
            { "responsivePriority": 1, "targets": 0 },
            { "responsivePriority": 2, "targets": 1 },
            { "responsivePriority": 3, "targets": 5 }
        ],
        "language": linguagemDataTable,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "do/gerusuario/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html?naoincluirheader&ajaxtbl",
            "type": "POST"
        },
        "order": [[ 1, "asc" ]],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "pageLength": 10,
        "columns": [
        { "data": "nome" },
        { "data": "login" },
        { "data": "email" },
        { "data": "secao" },
        { "data": "ramal" },
        { "data": "data_atualizacao", "searchable": false },
        { 
            "data": "permissoes",
            "orderable": false,
            "searchable": false
        },
        { 
            "data": "acoes",
            "orderable": false,
            "searchable": false
        }
    ]
    });
    
    $("#btnAdicionar").click(function(){
        document.location.href="do/gerusuario/<?php echo $this->container["objeto"]->valor('cod_objeto') ?>.html?acao=novo";
    });
});
</script>
<?php
}
elseif ($acao == "novo" || ($acao == "editar" && $cod > 0))
{
    $tmpArrPerfilObjeto = array();
    $usuario = array();
    $tmpPerfilObjetoAtualNovo = 0;

    if ($acao == "novo")
    {
        $titPagina = "Adicionar";
        $usuario["nome"] = isset($_REQUEST['nome'])?htmlspecialchars(urldecode($_REQUEST["nome"]), ENT_QUOTES, "UTF-8"):"";
        $usuario["secao"] = isset($_REQUEST['secao'])?htmlspecialchars(urldecode($_REQUEST["secao"]), ENT_QUOTES, "UTF-8"):"";
        $usuario["login"] = isset($_REQUEST['login'])?htmlspecialchars(urldecode($_REQUEST["login"]), ENT_QUOTES, "UTF-8"):"";
        $usuario["email"] = isset($_REQUEST['email'])?htmlspecialchars(urldecode($_REQUEST["email"]), ENT_QUOTES, "UTF-8"):"";
        $usuario["ramal"] = isset($_REQUEST['ramal'])?htmlspecialchars(urldecode($_REQUEST["ramal"]), ENT_QUOTES, "UTF-8"):"";
        $usuario["chefia"] = isset($_REQUEST['chefia'])?htmlspecialchars(urldecode($_REQUEST["chefia"]), ENT_QUOTES, "UTF-8"):"";
        $usuario["altera_senha"] = isset($_REQUEST['altera_senha'])?htmlspecialchars(urldecode($_REQUEST["altera_senha"]), ENT_QUOTES, "UTF-8"):"";
        $usuario["ldap"] = isset($_REQUEST['ldap'])?htmlspecialchars(urldecode($_REQUEST["ldap"]), ENT_QUOTES, "UTF-8"):"";
        $usuario["data_atualizacao"] = isset($_REQUEST['data_atualizacao'])?htmlspecialchars(urldecode($_REQUEST["data_atualizacao"]), ENT_QUOTES, "UTF-8"):"";
        $usuario["perfil"] = isset($_REQUEST['perfil'])?htmlspecialchars(urldecode($_REQUEST["perfil"]), ENT_QUOTES, "UTF-8"):"";
        $tmpPerfilObjetoAtualNovo = $usuario["perfil"];
    }
    else
    {
        $titPagina = "Alterar";
        $usuario = $this->container["usuario"]->pegarInformacoesUsuario($cod);
        $tmpArrPerfilObjeto = $this->container["usuario"]->pegarDireitosUsuario($cod);
    }
?>
    <script src="include/javascript_datepicker" type="text/javascript"></script>
    <link href="include/css_datepicker" rel="stylesheet" type="text/css">  
    
    <div class="card-body">

        <div class="card">
            <div class="card-header">
                <h3 style="line-height: 30px;"><?php echo($titPagina); ?> usuário</h3>
            </div>
			
            <form action="do/gerusuario_post/<?php echo $this->container["objeto"]->valor('cod_objeto') ?>.html" method="POST" id="form_usuario">
                <div class="card-body">
                				
                    <input type="hidden" name="cod_usuario" id="cod_usuario" value="<?php echo isset($usuario['cod_usuario']) ? $usuario['cod_usuario'] : ""; ?>" />
                    <input type="hidden" name="nomehidden" value="<?php echo isset($usuario['nome']) ? $usuario['nome'] : ""; ?>" />
					
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="InputNome" class="form-label">Nome</label>
                                <input class="form-control required" type="text" name="nome" id="InputNome" value="<?php echo isset($usuario['nome']) ? $usuario['nome'] : ""; ?>">
                            </div>
                            <div class="form-group">
                                <label for="InputArea" class="form-label">Área vinculada</label>
                                <input class="form-control required" type="text" name="secao" id="InputArea" value="<?php echo isset($usuario['secao']) ? $usuario['secao'] : ""; ?>">
                            </div>
                            <div class="form-group">
                                <label for="InputLogin" class="form-label">Login</label>
                                <input class="form-control required" type="text" name="login" id="InputLogin" value="<?php echo isset($usuario['login']) ? $usuario['login'] : ""; ?>">
                            </div>
                            <div class="form-group">
                                <label for="InputEmail" class="form-label">E-mail</label>
                                <input class="form-control required mail" type="text" name="email" id="InputEmail" value="<?php echo isset($usuario['email']) ? $usuario['email'] : ""; ?>">
                            </div>
                            <div class="form-group">
                                <label for="InputRamal" class="form-label">Ramal (contato)</label>
                                <input class="form-control required" type="text" name="ramal" id="InputRamal" value="<?php echo isset($usuario['ramal']) ? $usuario['ramal'] : ""; ?>">
                            </div>
                            <div class="form-group">
                                <label for="InputSenha" class="form-label">Senha <meter value="0" id="PassValue" max="100"></meter></label>
                                <input class="form-control <?php if($acao != "editar"){ ?>required<?php } ?>" type="password" name="senha" id="InputSenha" value="" >
                            </div>
                            <div class="form-group">
                                <label for="InputConfSenha" class="form-label">Confirme a Senha</label>
                                <input class="form-control <?php if($acao != "editar"){ ?>required<?php } ?>" type="password" name="confsenha" id="InputConfSenha" value="" data-rule-equalTo="#InputSenha">
                            </div>
                            <div class="form-group">
                                <label for="InputChefia" class="form-label">Chefia</label>
                                <select class="form-control campo" name="chefia" id="InputChefia">
                                    <option value="0"> -- Nenhum -- </option>
<?php
foreach ($usuarios as $usu)
{
?>
                                    <option value="<?php echo($usu["cod_usuario"]); ?>" <?php if($usuario["chefia"]==$usu["cod_usuario"]) {echo "selected";} ?>><?php echo($usu["nome"]); ?></option>
<?php
}
?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="checkbox-inline" title="Define se usuário deverá alterar a senha no primeiro acesso"><input class="campo" type="checkbox" name="altera_senha" id="altera_senha" value="1" <?php if($usuario["altera_senha"]==1) {echo "checked";} ?> /> Alterar senha</label>
                            </div>
<?php
if (isset($this->container["config"]->login["ldap"]) 
&& $this->container["config"]->login["ldap"]
&& $this->container["config"]->login["ldaphost"] != "")
{
?>
                            <div class="form-group">
                                <label class="checkbox-inline" title="Define se login do usuário será realizado pelo AD/LDAP"><input class="campo" type="checkbox" name="ldap" id="ldap" value="1" <?php if($usuario["ldap"]==1) {echo "checked";} ?> /> AD/LDAP</label>
                            </div>
<?php
}
?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="InputValidade" >Validade: 
                                    <?php 
                                    $validade = isset($usuario['data_atualizacao']) ? ConverteData($usuario['data_atualizacao'], 5) : $data_validade;
                                    if ($validade=="") 
                                    {
                                        $data = date("d/m/Y");
                                        $data = \DateTime::createFromFormat('d/m/Y', $data);
                                        $data->add(new \DateInterval('P180D'));
                                        $validade = $data->format('d/m/Y');
                                    }
                                    ?></label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="data_atualizacao" value="<?= $validade ?>" class="data form-control">
                            </div>
                            <div class="form-group">
                                <label for="rdoIndexar">Perfil no Objeto:</label>
                            </div>
                            <?php
                            $tmpDisabled = "";
                            if (isset($tmpArrPerfilObjeto['1']) && ($tmpArrPerfilObjeto['1'] == _PERFIL_ADMINISTRADOR) && ($this->container["objeto"]->valor('cod_objeto') != $this->container["config"]->portal["objroot"])) {
                                $tmpPerfilObjetoAtual = _PERFIL_ADMINISTRADOR;
                                $tmpDisabled = "disabled";
                            } else {
                                if (isset($tmpArrPerfilObjeto[$this->container["objeto"]->valor('cod_objeto')]))
                                    $tmpPerfilObjetoAtual = $tmpArrPerfilObjeto[$this->container["objeto"]->valor('cod_objeto')];
                                else
                                    $tmpPerfilObjetoAtual = $tmpPerfilObjetoAtualNovo;
                            }
                            ?>
                            <div class="form-group">
                                <label class="radio-inline">
                                    <input class="mr-1" type="radio" name="perfil" value="<?= _PERFIL_ADMINISTRADOR ?>" <?= ($tmpPerfilObjetoAtual == _PERFIL_ADMINISTRADOR) ? 'checked' : '' ?> <?= $tmpDisabled; ?>>Adminstrador<BR>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline">
                                    <input class="mr-1" type="radio" name="perfil" value="<?= _PERFIL_EDITOR ?>" <?= ($tmpPerfilObjetoAtual == _PERFIL_EDITOR) ? 'checked' : '' ?> <?= $tmpDisabled; ?>>Editor<BR>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline">
                                    <input class="mr-1" type="radio" name="perfil" value="<?= _PERFIL_AUTOR ?>" <?= ($tmpPerfilObjetoAtual == _PERFIL_AUTOR) ? 'checked' : '' ?> <?= $tmpDisabled; ?>>Autor<BR>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="radio-inline">
                                    <input class="mr-1" type="radio" name="perfil" value="<?= _PERFIL_RESTRITO ?>" <?= ($tmpPerfilObjetoAtual == _PERFIL_RESTRITO) ? 'checked' : '' ?> <?= $tmpDisabled; ?>>Restrito<BR>
                                </label>
                            </div>
<!--                            <div class="form-group">
                                <label class="radio-inline">
                                    <input type="radio" name="perfil" value="<?= _PERFIL_MILITARIZADO ?>" <?= ($tmpPerfilObjetoAtual == _PERFIL_MILITARIZADO) ? 'checked' : '' ?> <?= $tmpDisabled; ?>>Militarizado<BR>
                                </label>
                            </div>-->
                            <div class="form-group">
                                <label class="radio-inline">
                                    <input class="mr-1" type="radio" name="perfil" value="<?= _PERFIL_DEFAULT ?>" <?= ($tmpPerfilObjetoAtual == _PERFIL_DEFAULT) ? 'checked' : '' ?> <?= $tmpDisabled; ?> >Default<BR>
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="InputQuadroAtual"> Quadro atual:</label>
                                <br />
                                <?php
                                foreach ($tmpArrPerfilObjeto as $key=>$val)
                                {
//                                while (list($key, $val) = each($tmpArrPerfilObjeto)) {
                                    echo '<input type="checkbox" id="checkadmperfil[]" name="checkadmperfil[]" value="' . $key . '">&nbsp;';
                                    echo " [$key] - ";
                                    $tmpCheckPerfil = Usuario::verificarPerfil($val);
                                    echo $tmpCheckPerfil;
                                    echo "<br />\n";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="text-align: right;">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="submit" value="Gravar" class="btn btn-success" name="btnGravar" id="btnGravar" />
                            <input type="button" value="Cancelar" class="btn btn-warning" name="btnCancelar" id="btnCancelar" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script>
$(document).ready(function() {
    $('#form_usuario').validate({
        submitHandler: function(form) {
            var verifica = true;
            if ($("#ldap").prop("checked")===true) verifica = false;
            if ($("#cod_usuario").val() != "" && $("#InputSenha").val() == "") verifica = false;
            if (verifica)
            {
                // var complexidade = $("#PassValue").val();
//                if (complexidade <= 40) 
//                {
//                    alert("Senha com complexidade baixa.\nUtilize uma senha combinando letras maiúsculas, minúsculas e números.");
//                    return false
//                }
            }
            form.submit();
        }
    });
    // $("#InputSenha").complexify({}, function (valid, complexity) { 
    //     document.getElementById("PassValue").value = complexity; 
    // });
    $("#btnCancelar").click(function(){
        document.location.href="do/gerusuario/<?php echo($this->container["objeto"]->valor("cod_objeto")); ?>.html";
    });
    $("#ldap").click(function(){
        if (this.checked)
        {
            $("#InputSenha").prop('disabled', true);
            $("#InputConfSenha").prop('disabled', true);
        }
        else
        {
            $("#InputSenha").prop('disabled', false);
            $("#InputConfSenha").prop('disabled', false);
        }
    });
    $('.data').datetimepicker(config_date);
});
</script>
<?php
}
elseif ($acao=="bloquear" && $cod > 0)
{
    $usuario = $this->container["usuario"]->pegarInformacoesUsuario($cod);
    
    if (!$usuario)
    {
        echo "<script>document.location.href='do/gerusuario/".$this->container["objeto"]->valor("cod_objeto").".html?msge=".urlencode("Usuário não encontrado")."';</script>";
        exit();
    }
?>
    <div class="card-body">
        <div class="card">
            <div class="card-header">
                <h3 style="line-height: 30px;">Apagar usuário</h3>
            </div>
            <form action="do/gerusuario_post/<?php echo $this->container["objeto"]->valor('cod_objeto') ?>.html" method="POST" id="form_usuario">
                <input type="hidden" name="cod_usuario" value="<?php echo($cod); ?>" />
                <div class="card-body">
                    <p>Deseja realmente apagar o usuário <b>"<?php echo($usuario["nome"]); ?>"</b>?</p>
                    <p><input type="submit" class="btn btn-danger" name="btnApagar" value="Apagar" /> 
                    <input type="button" class="btn btn-secondary" value="Não Apagar" id="btnBNao" /></p>
                </div>
            </form>
        </div>
    </div>
<script>
    $("document").ready(function(){
        $("#btnBNao").click(function(){
            document.location.href='do/gerusuario/".$this->container["objeto"]->valor($page, "cod_objeto").".html';
        });
    });
</script>
<?php
}
?>
</div>
   