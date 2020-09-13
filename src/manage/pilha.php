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
?>
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" href="do/list_content/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Listar Conteúdo</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="do/pilha/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Pilha</a>
    </li>
</ul>
<script>
$(document).ready(function(){
    $(".btnAcao").click(function(){
        $("#divMensagemGravar").show();
        $("#divBotoesAcao").hide();
    });
});
</script>
<div class="card">
    <div class="card-header bg-primary text-white"><h3><b>Pilha</b></h3></div>
    
<?php
if (($this->container["objeto"]->podeTerFilhos()))
{
?>
    <form action="do/pilha_post/<?php echo $this->container["objeto"]->valor("cod_objeto");?>.html" method="POST" name="objmanage" id="objmanage">
        <div class="card-body">
            <select class="pblSelectForm" name="cod_objmanage">
            <?php echo $this->container["administracao"]->dropdownPilha() ?>
            </select>
        </div>
        
        <div class="card-footer">
<?php
    if ($this->container["administracao"]->temPilha())
    {
?>
            <div id="divBotoesAcao" class="text-center" >
                <input class="btn btn-info btnAcao" type="submit" name="pastelink" value="Colar Link">&nbsp;&nbsp;
                <input class="btn btn-warning btnAcao" type="submit" name="move" value="Mover">
                <input class="btn btn-success btnAcao" type="submit" name="copy" value="Colar c&oacute;pia">
                <input class="btn btn-danger btnAcao" type="submit" name="clear" value="Limpar Lista">
            </div>
            <!-- === Final === Botões (Inverter, Publicar, Despublicar, Apagar, Duplicar e Copiar para a pilha) === -->
            <!-- === Mensagem de ação === -->
            <div class="alert alert-warning alert-dismissible fade in" role="alert" id="divMensagemGravar" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">x</span></button>
                <h4>Processando informa&ccedil;&otilde;es .... aguarde....</h4>
            </div>
            <!-- === Final === Mensagem de ação === -->
<?php
    }
?>
        </div>
    </form>
<?php
}
?>
</div>
