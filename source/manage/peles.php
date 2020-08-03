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
 
global $_page;

$cod_pele = isset($_REQUEST['cod_pele']) ? (int)htmlspecialchars($_REQUEST['cod_pele'], ENT_QUOTES, "UTF-8") : 0;
$nome = isset($_REQUEST['nome']) ? htmlspecialchars($_REQUEST['nome'], ENT_QUOTES, "UTF-8") : "";
$prefixo = isset($_REQUEST['prefixo']) ? htmlspecialchars($_REQUEST['prefixo'], ENT_QUOTES, "UTF-8") : "";
$publica = isset($_REQUEST['publica']) ? (int)htmlspecialchars($_REQUEST['publica'], ENT_QUOTES, "UTF-8") : 0;
$erro = isset($_REQUEST['erro']) ? urldecode($_REQUEST['erro']) : "";

$row = array("texto" => $nome,
    "prefixo" => $prefixo,
    "publica" => $publica,
    "codigo" => $cod_pele);


if ($cod_pele > 0) {
    $pele = $_page->_administracao->PegaListaDePeles($cod_pele);
    $row = $pele[0];
}

?>
<script>
$("document").ready(function(){
	
	$("#btn_apagar").click(function(){
		peleApagar();
	});
	
});

</script>
<!-- === Menu === -->
<ul class="nav nav-tabs">
    <li><a href="do/indexportal/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Informações do Publicare</a></li>
    <li><a href="do/gerusuario/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Gerenciar usuários</a></li>
    <li><a href="do/classes/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Gerenciar classes</a></li>
    <li class="active"><a href="do/peles/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Gerenciar Peles</a></li>
</ul>
<!-- === FInal === Menu === -->

<!-- === Gerenciar Peles === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Gerenciar Peles</b></h3></div>
    <div class="panel-body">

        <!-- === Selecione a pele === -->
        <form action="do/peles/<?php echo $_page->_objeto->Valor("cod_objeto") ?>.html" method="post">            
            <div class="panel panel-info">
                <div class="panel-heading">Selecione a Pele</div>
                <div class="panel-body">
                    <label for="InputNome" class="col-md-2 col-form-label">Apar&ecirc;ncia</label>
                    <div class="col-md-6">
                        <select name="cod_pele" class="form-control">
                            <option value="0"> -- NOVA -- </option>
<?php
$peles = $_page->_administracao->PegaListaDePeles();
foreach ($peles as $pele)
{
?>
                            <option value="<?php echo($pele["codigo"]); ?>" <?php if($row["codigo"]==$pele["codigo"]) { echo "selected"; } ?>><?php echo($pele["texto"]." (" . $pele["prefixo"] . ")"); ?></option>
<?php
}
?>

                        </select>
                    </div>

                    <div class="col-md-3">
                        <input type="submit" name="submit"  value="Selecionar" class="btn btn-primary">
                        <!--<a href="#" onclick="history.back()" class="btn btn-success">Voltar</a>-->
                    </div>
                </div>
            </div>
        </form>
        <!-- === Final === Selecione a pele === -->
<?php
if ($erro!="")
{
?>
<div class="alert alert-danger alert-dismissible fade in" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">x</span></button>
	<h4><b>Ocorreu um erro!</b></h4>
	<p><?php echo($erro); ?></p>
	<p><button type="button" class="btn btn-default" data-dismiss="alert" aria-label="Fechar">Fechar</button></p>
</div>
<?php
}
?>
        <!-- === Nova Pele === -->
        <form action="do/peles_post/<?php echo $_page->_objeto->Valor("cod_objeto") ?>.html" method="post">
            <div class="panel panel-info">
                <div class="panel-heading"><?php if ($cod_pele > 0) { ?>Editar Pele - código: <?php echo($cod_pele); ?><?php } else { ?>Nova Pele<?php } ?></div>
                <div class="panel-body">
                    <input type="hidden" name="cod_pele" value="<?php echo $cod_pele ?>">
                    <div class="form-group row ">
                        <label for="InputNome" class="col-md-2 col-form-label">Nome</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" name="nome" id="InputNome" value="<?php echo $row['texto'] ?>">
                        </div>
                    </div>

                    <div class="form-group row ">
                        <label for="InputNome" class="col-md-2 col-form-label">Prefixo</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" name="prefixo" id="InputPrefixo" value="<?php echo $row['prefixo'] ?>">
                        </div>
                    </div>

                    <div class="form-group row ">
                        &nbsp;&nbsp;&nbsp;<input type="checkbox" class="form-check-input" name="publica" id="publica" <?= ($row['publica']) ? 'checked' : '' ?> value="1">
                        <label class="form-check-label" for="publica"> Tornar P&uacute;blica</label>
                    </div>
                </div>
				<div id="container_alerta"></div>
				<div class="alert alert-danger alert-dismissible fade in modelo_apagar" role="alert" style="display: none;">
						<button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">x</span></button>
						<h4><b>Apagar pele!</b></h4>
						<p>Deseja realmente apagar a pele?</p>
						<p><button type="submit" class="btn btn-danger apagar" name="delete">Apagar</button> <button type="button" class="btn btn-default naoapagar" data-dismiss="alert" aria-label="Não Apagar">Não Apagar</button></p>
					</div>
                <div class="panel-footer" style="text-align: right">
					
                    <?php
                        if ($cod_pele)
                        {
                    ?>
                    <input class="btn btn-warning" type="submit" name="update" value="Alterar">&nbsp;&nbsp;&nbsp;<input class="btn btn-danger" type="button" name="delete" value="Remover" id="btn_apagar">
                    <?php
                        }
                        else
                        {
                    ?>
                    <input class="btn btn-success" type="submit" name="new" value="Criar">
                    <?php }?>
                </div>
            </div>
        </form>
        <!-- === Final === Nova Pele === -->
        
    </div>
</div>
<!-- === Final === Gerenciar Peles === -->
