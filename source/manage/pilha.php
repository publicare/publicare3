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
?>
<ul class="nav nav-tabs">
  <li><a href="do/list_content/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Listar Conteúdo</a></li>
  <li class="active"><a href="do/pilha/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Pilha</a></li>
</ul>
<script>
$(document).ready(function(){
    $(".btnAcao").click(function(){
        $("#divMensagemGravar").show();
        $("#divBotoesAcao").hide();
    });
});
</script>
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Pilha</b></h3></div>
    
<?php
if (($_page->_objeto->PodeTerFilhos()))
{
?>
    <form action="do/pilha_post/<?php echo $_page->_objeto->Valor("cod_objeto");?>.html" method="POST" name="objmanage" id="objmanage">
        <div class="panel-body">
            <select class="pblSelectForm" name="cod_objmanage">
            <?php echo $_page->_administracao->DropDownPilha($_page) ?>
            </select>
        </div>
        
        <div class="panel-footer">
<?php
    if ($_page->_administracao->TemPilha($_page))
    {
?>
            <div id="divBotoesAcao"><center>
                <input class="btn btn-info btnAcao" type="submit" name="pastelink" value="Colar Link">&nbsp;&nbsp;
                <input class="btn btn-warning btnAcao" type="submit" name="move" value="Mover">
                <input class="btn btn-success btnAcao" type="submit" name="copy" value="Colar c&oacute;pia">
                <input class="btn btn-danger btnAcao" type="submit" name="clear" value="Limpar Lista">
                </center></div>
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
