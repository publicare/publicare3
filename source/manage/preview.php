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
<!-- === Menu === -->
<ul class="nav nav-tabs">
  <li class="active"><a href="do/preview/<?php echo($_page->_objeto->Valor($_page, 'cod_objeto')) ?>.html">Indice do Objeto</a></li>
  <li><a href="do/log_workflow/<?php echo($_page->_objeto->Valor($_page, 'cod_objeto')) ?>.html">Workflow</a></li>
  <li><a href="do/log_objeto/<?php echo($_page->_objeto->Valor($_page, 'cod_objeto')) ?>.html">Log Status</a></li>
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
					<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($_page->_objeto->Valor($_page, "titulo")); echo " <i>[cod: ".$_page->_objeto->Valor($_page, "cod_objeto")."]</i>"; ?></h3></div>
					<div class="col-sm-3 text-right titulo-icones">
						<a class="ABranco" href="<?php echo(_URL); ?><?php echo($_page->_objeto->Valor($_page, "url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
					</div>
				</div>
			</div>

			<div class="panel-body">
								   
				<div id="list-conter-classe"> 
					<ul>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Nome do Site:</strong></div>
								<div class="col-md-9 col-sm-8"><?php echo _PORTAL_NAME . " [<i>" . _LANGUAGE . "</i>]"; ?></div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Hierarquia:</strong></div>
								<div class="col-md-9 col-sm-8">
<?php
	$tmpCaminhoObjeto=$_page->_objeto->PegaCaminhoComTitulo($_page);
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
								<div class="col-md-9 col-sm-8"><?php echo $_page->_objeto->Valor($_page, "classe")." [".$_page->_objeto->Valor($_page, "prefixoclasse")."]"; ?></div>
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
									if ($_page->_objeto->Valor($_page, "temfilhos"))
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
	if ($_page->_objeto->Valor($_page, "cod_status")!=_STATUS_PUBLICADO)
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
								<div class="col-md-9 col-sm-8"><?php echo $_page->_objeto->Valor($_page, "data_publicacao"); ?></div>
							</div>
						</li>
						<li>
							<div class="row">
								<div class="col-md-3 col-sm-4"><strong>Validade:</strong></div>
								<div class="col-md-9 col-sm-8"><?php echo $_page->_objeto->Valor($_page, "data_validade"); ?></div>
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