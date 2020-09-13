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


$classname = $this->container["objeto"]->valor("prefixoclasse");
$classe = $this->container["administracao"]->pegarInfoDaClasse($this->container["objeto"]->valor("cod_classe"));
?>
<!-- === Rejeitar Objeto === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Rejeitar Objeto</b></h3>
    <p class="padding-top10">
            <strong>Despublicar</strong>: <?php echo($this->container["objeto"]->valor("titulo")) ?> (<?php echo($this->container["objeto"]->valor("cod_objeto")) ?>)<br /><strong>Classe</strong>: <?php echo($classe["classe"]["nome"]); ?> (<?php echo($classe["classe"]["cod_classe"]); ?>) [<?php echo($classe["classe"]["prefixo"]); ?>]<br />
            <strong>Vers&atilde;o</strong>: <?php echo($this->container["objeto"]->valor("versao")) ?>
        </p>
    </div>

	<form action="do/rejeitar_post/<?php echo($this->container["objeto"]->valor("cod_objeto"));?>.html" method="post">
		<div class="panel-body">
			
			<!-- === Objeto === -->
			<div class="panel panel-info modelo_propriedade">
				<div class="panel-heading">
					<div class="row">
						<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($this->container["objeto"]->valor("titulo")); ?></h3></div>
						<div class="col-sm-3 text-right titulo-icones">
							<a href="<?php echo($this->container["config"]->portal["url"]); ?><?php echo($this->container["objeto"]->valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
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