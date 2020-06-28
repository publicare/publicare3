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

global $_page, $cod_objeto;
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
<form action="do/new_post/<?=$_page->_objeto->Valor("cod_objeto")?>.html" name="listcontent" id="listcontent" method="post">
    <input type="hidden" name="cod_objeto" value="<?php echo($cod_objeto) ?>">
    <input type="hidden" name="prefixo" id="prefixo" value="">
    <!-- === Selecione a classe === -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3><strong>Selecione a Classe</strong></h3>
            <p class="padding-top10"><b>Objeto atual</b>: <?php echo($_page->_objeto->Valor("titulo")) ?> (<?php echo($_page->_objeto->Valor("cod_objeto")) ?>) - <b>Classe</b>: <?php echo($_page->_objeto->Valor("classe")) ?> (<?php echo($_page->_objeto->Valor("cod_classe")) ?>)</p>
        </div>
        <div class="panel-body">
            <!-- === Classes === -->
            <h4 class="padding-bottom20"><strong>Classes</strong></h4>
            <p>A lista abaixo apresenta as classes que podem ser utilizadas a partir do objeto atual.</p>
<?php
$lista = $_page->_administracao->ListaDeClassesPermitidas($_page->_objeto->Valor("cod_classe"));
$lista2 = $_page->_administracao->ListaDeClassesPermitidasNoObjeto($_page->_objeto->Valor("cod_objeto"));
$lista=array_merge($lista,$lista2);
//xd($lista);
foreach($lista as $row)
{
?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-9"><h4 class="padding-bottom10"><strong><?php echo($row['nome']) ?></strong></h4><i><?php echo($row['descricao']) ?></i></div>
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