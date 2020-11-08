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
global $cod_objeto;
?>
<script type="text/javascript">
$("document").ready(function(){
    $(".btn_usarclasse").click(function(event){
        event.preventDefault();
        $("#prefixo").val($(this).prop("id"));
        $("#listcontent").submit();
    });
});
</script>
<form action="do/new_post/<?php echo($this->container["objeto"]->valor("cod_objeto"));?>.html" name="listcontent" id="listcontent" method="post">
    <input type="hidden" name="cod_objeto" value="<?php echo($this->container["objeto"]->valor("cod_objeto")) ?>">
    <input type="hidden" name="prefixo" id="prefixo" value="">
    <!-- === Selecione a classe === -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3><strong>Selecione a Classe</strong></h3>
            <p><b>Objeto atual</b>: <?php echo($this->container["objeto"]->valor("titulo")) ?> (<?php echo($this->container["objeto"]->valor("cod_objeto")) ?>) - <b>Classe</b>: <?php echo($this->container["objeto"]->valor("classe")) ?> (<?php echo($this->container["objeto"]->valor("cod_classe")) ?>)</p>
        </div>
        <div class="card-body">
            <!-- === Classes === -->
            <h4><strong>Classes</strong></h4>
            <p>A lista abaixo apresenta as classes que podem ser utilizadas a partir do objeto atual.</p>
<?php
$lista = $this->container["administracao"]->listarClassesPermitidas($this->container["objeto"]->valor("cod_classe"));
$lista2 = $this->container["administracao"]->listarClassesPermitidasObjeto($this->container["objeto"]->valor("cod_objeto"));
//x($lista);
//x($lista2);
foreach ($lista2 as $l)
{
    $encontrado = false;
    foreach ($lista as $li)
    {
        if ($l["cod_classe"] == $li["cod_classe"])
        {
            $encontrado = true;
            break;
        }
    }
    if (!$encontrado)
    {
        $lista[] = $l;
    }
}

usort($lista, function($a, $b){
    return strtolower($a["nome"])>strtolower($b["nome"]);
});

foreach($lista as $row)
{
?>
            <div class="card mt-1">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-9"><h4><strong><?php echo($row['nome']) ?></strong></h4><i><?php echo($row['descricao']) ?></i></div>
                        <div class="col-sm-3 text-right"><a href="#" id="<?=$row['prefixo']?>" class="btn btn-success btn_usarclasse">Usar classe</a></div>
                    </div>
                </div>
            </div>
<?php 
} 
?>
            <!-- === Final === Classes === -->
        </div>
    </div>
    <!-- === Final === Selecione a classe === -->
</form>