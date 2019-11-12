<?php
/** 
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo classes.php é responsável pela montagem do formulário para administração das classes
 * @copyright GPL © 2007
 * @package publicare
 *
 * MCTI - Ministério da Ciência, Tecnologia e Inovação - www.mcti.gov.br
 * ANTT - Agência Nacional de Transportes Terrestres - www.antt.gov.br
 * EPL - Empresa de Planejamento e Logística - www.epl.gov.br
 * LogicBSB - LogicBSB Sistemas Inteligentes - www.logicbsb.com.br
 *
 * Este arquivo é parte do programa Publicare
 * Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
 * como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
 * de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
 */

global $_page, $info, $num_filhos;
?>

<!-- === Apagar este objeto === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Apagar este objeto</b></h3></div>
    <div class="panel-body">
		
		<!-- === Atenção === -->
		<?php 
			$num_filhos = $_page->_adminobjeto->PegaNumFilhos($_page, $_page->_objeto->Valor($_page, "cod_objeto"));
			//Alertar o usuario que o objeto a ser apagado contem filhos
			if ($num_filhos>0) {

				echo '
				<div class="alert alert-danger" role="alert">
					<h4 class="padding-bottom10 font-size24"><strong>ATEN&Ccedil;&Atilde;O</strong></h4>
					<p class="font-size18">O objeto cont&eacute;m filhos. Ao apag&aacute;-lo, todos os filhos ser&atilde;o apagados tamb&eacute;m.</p>
				</div>
				';
			}
		?>
		<!-- === Final === Atenção === -->
		
		<!-- === Dados do Objeto === -->
		<div class="panel panel-info">
			<div class="panel-heading">&Uacute;ltima altera&ccedil;&atilde;o do Objeto:</div>
			<div class="panel-body">
				
			<?php
				$info = $_page->_log->InfoObjeto($_page, $_page->_objeto->Valor($_page, "cod_objeto"));
				echo "<h3 class='padding-bottom20 font-size24'><strong>".$_page->_objeto->Valor($_page, "titulo")."</strong></h3>";
				echo '
				<div id="list-conter-classe"> 
					<ul>
						<li><strong>Usu&aacute;rio: </strong>'. $info['usuario'].'</li>
						<li><strong>Data: </strong>'. $info['estampa'].'</li>
						<li><strong>Mensagem: </strong>'. $info['mensagem'].'</li>
					</ul>
				</div>';
			?>
				
			</div>
		</div>
		<!-- === Final === Dados do Objeto === -->
		
    </div>
	<form action="/do/delete_post.php/<? echo $_page->_objeto->Valor($_page, "cod_objeto")?>.html" method="post" name="delete_post" id="delete_post">
	<div class="panel-footer" style="text-align: right">
		<input type="hidden" name="cod_pai" value="<? echo $_page->_objeto->Valor($_page, "cod_pai")?>">
		<input type="submit" name="submit" value="Remover Objeto" class="btn btn-danger">
	</div>
	</form>
</div>
<!-- === Final === Apagar este objeto === -->