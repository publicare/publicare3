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
?>
<!-- === Menu === -->
<ul class="nav nav-tabs">
  <li class="active"><a href="do/preview/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Indice do Objeto</a></li>
  <li><a href="do/log_workflow/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Workflow</a></li>
  <li><a href="do/log_objeto/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Log Status</a></li>
</ul>
<!-- === FInal === Menu === -->

<!-- === Indice do Objeto === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Indice do Objeto</b></h3></div>
	<div class="panel-body">
		
		<!-- === Objeto === -->
		<div class="panel panel-info modelo_propriedade">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($_page->_objeto->Valor("titulo")); echo " <i>[cod: ".$_page->_objeto->Valor("cod_objeto")."]</i>"; ?></h3></div>
					<div class="col-sm-3 text-right titulo-icones">
						<a class="ABranco" href="<?php echo($_page->config["portal"]["url"]); ?><?php echo($_page->_objeto->Valor("url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
					</div>
				</div>
			</div>

			<div class="panel-body">
								   
				<div id="list-conter-classe"> 
					<ul>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Nome do Site:</strong></div>
								<div class="col-md-9 col-sm-8"><?php echo $_page->config["portal"]["nome"] . " [<i>" . $_page->config["portal"]["linguagem"] . "</i>]"; ?></div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Hierarquia:</strong></div>
								<div class="col-md-9 col-sm-8">
<?php
	$tmpCaminhoObjeto=$_page->_objeto->PegaCaminhoComTitulo();
	foreach ($tmpCaminhoObjeto as $item)
	{
		echo '<a href="do/preview/'.$item['cod_objeto'].'.html">'.$item['titulo'].'</a><i> [cod: '.$item['cod_objeto'].']</i><br>';
	}
?>
								</div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Classe:</strong></div>
								<div class="col-md-9 col-sm-8"><?php echo $_page->_objeto->Valor("classe")." [".$_page->_objeto->Valor("prefixoclasse")."]"; ?></div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Pele:</strong></div>
								<div class="col-md-9 col-sm-8">
<?php
	if ($_page->_objeto->metadados['cod_pele'])
	{
		echo $_page->_objeto->metadados['prefixopele'];
		echo "<i> [cod: ".$_page->_objeto->metadados['cod_pele']."]</i>";
	}
	else
	echo "N&atilde;o utilizada [cod: 0]"
?>
								</div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Script:</strong></div>
								<div class="col-md-9 col-sm-8">
<?php
	if ($_page->_objeto->metadados['script_exibir']) {
	 if (file_exists($_SERVER['DOCUMENT_ROOT'].$_page->_objeto->metadados['script_exibir']))
		echo $_page->_objeto->metadados['script_exibir'];
	 else
		echo "<b>A view do objeto foi deletada! <i>[".$_page->_objeto->metadados['script_exibir']."]</i></b>";}
	else
		echo "Sele&ccedil;&atilde;o autom&aacute;tica [cod: 0]";
?>
								</div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Objeto pode ter filhos:</strong></div>
								<div class="col-md-9 col-sm-8">
									<?php
									if ($_page->_objeto->Valor("temfilhos"))
										echo "Sim";
									else
										echo "Nao";
									?>
								</div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Status do objeto:</strong></div>
								<div class="col-md-9 col-sm-8">
<?php
	if ($_page->_objeto->Valor("cod_status")!=_STATUS_PUBLICADO)
		echo "<b>N&atilde;o publicado</b>";
	else
		echo "Publicado";
?>
								</div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Publica&ccedil;&atilde;o:</strong></div>
								<div class="col-md-9 col-sm-8"><?php echo $_page->_objeto->Valor("data_publicacao"); ?></div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Validade:</strong></div>
								<div class="col-md-9 col-sm-8"><?php echo $_page->_objeto->Valor("data_validade"); ?></div>
							</div>
						</li>
					</ul>
				</div>
								   
			</div>
		</div>
		<!-- === Final === Objeto === -->
		
		<!-- === Informaçães do Usuário === -->
		<div class="panel panel-info modelo_propriedade">
			<div class="panel-heading"><h3 class="font-size20" style="line-height: 30px;">Informaçães do Usuário</h3></div>

			<div class="panel-body">
								   
				<div id="list-conter-classe"> 
					<ul>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Nome do usu&aacute;rio:</strong></div>
								<div class="col-md-9 col-sm-8">
								<?php
									if (isset($_page->user->anonymous))
									echo "<font color=red size=11><b>Anonimo</b></font>";
									else
									echo "<font color=red>".$_SESSION['usuario']['nome']."</font>";
								?>
								</div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Perfil no objeto:</strong></div>
								<div class="col-md-9 col-sm-8">
								<?php
									$recebePerfil = Usuario::VerificaPerfil($_SESSION['usuario']['perfil']);
									echo "<font color=red>".$recebePerfil."</font>";
								?>
								</div>
							</div>
						</li>
					</ul>
				</div>
								   
			</div>
		</div>
		<!-- === Final === Informaçães do Usuário === -->

	</div>
</div>
<!-- === Final === Indice do Objeto === -->