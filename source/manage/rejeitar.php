<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo classes.php é responsável pela montagem do formulário para administração das classes
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


$classname = $_page->_objeto->Valor("prefixoclasse");
$classe = $_page->_administracao->PegaInfoDaClasse($_page->_objeto->Valor("cod_classe"));
?>
<!-- === Rejeitar Objeto === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Rejeitar Objeto</b></h3>
    <p class="padding-top10">
            <strong>Despublicar</strong>: <?php echo($_page->_objeto->Valor("titulo")) ?> (<?php echo($_page->_objeto->Valor("cod_objeto")) ?>)<br /><strong>Classe</strong>: <?php echo($classe["classe"]["nome"]); ?> (<?php echo($classe["classe"]["cod_classe"]); ?>) [<?php echo($classe["classe"]["prefixo"]); ?>]<br />
            <strong>Vers&atilde;o</strong>: <?php echo($_page->_objeto->Valor("versao")) ?>
        </p>
    </div>

	<form action="do/rejeitar_post/<?php echo($_page->_objeto->Valor("cod_objeto"));?>.html" method="post">
		<div class="panel-body">
			
			<!-- === Objeto === -->
			<div class="panel panel-info modelo_propriedade">
				<div class="panel-heading">
					<div class="row">
						<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($_page->_objeto->Valor("titulo")); ?></h3></div>
						<div class="col-sm-3 text-right titulo-icones">
							<a href="<?php echo($_page->config["portal"]["url"]); ?><?php echo($_page->_objeto->Valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
						</div>
					</div>
				</div>

				<div class="panel-body">									   
					<label for="message" class="padding-bottom10">Comentário/Mensagem sobre a rejeição do objeto:</label>
					<textarea name="message" id="message" rows="8" class="width100" ><? echo isset($message)?$message:""; ?></textarea>
				</div>
			</div>
			<!-- === Final === Objeto === -->

		</div>
		<div class="panel-footer" style="text-align: right;">
			<input type="submit" name="submit" value="Gravar" class="btn btn-success">	
		</div> 
	</form>
</div>
<!-- === Final === Rejeitar Objeto === -->
		
<?php
	// Inserindo Guias
	//echo "<div id=\"divGuiaB\" style=\"height: 0%; visibility: hidden;\">";
	//include_once ("log_workflow.php");
	//echo "</div>";

	//echo "<div id=\"divGuiaC\" style=\"height: 0%; visibility: hidden;\">";
	//include_once ("log_objeto.php");
	//echo "</div>";
?>