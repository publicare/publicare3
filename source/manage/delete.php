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

global $page, $info, $num_filhos;
?>

<!-- === Apagar este objeto === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Apagar este objeto</b></h3></div>
    <div class="panel-body">
		
		<!-- === Atenção === -->
		<?php 
			$num_filhos = $page->adminobjeto->pegarNumFilhos($page->objeto->valor("cod_objeto"));
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
				$info = $page->log->InfoObjeto($page->objeto->valor("cod_objeto"));
                                
				echo "<h3 class='padding-bottom20 font-size24'><strong>".$page->objeto->valor("titulo")."</strong></h3>";
                                if(count($info) > 0)
                                {
                                    echo('<div id="list-conter-classe">'
                                            . '<ul>'
                                            . '<li><strong>Usu&aacute;rio: </strong>'. $info['usuario'].'</li>'
                                            . '<li><strong>Data: </strong>'. $info['estampa'].'</li>'
                                            . '<li><strong>Mensagem: </strong>'. $info['mensagem'].'</li>'
                                            . '</ul>'
                                            . '</div>');
                                }
                                else
                                {
                                    echo('Nenhuma aleração realizada');
                                }
			?>
				
			</div>
		</div>
		<!-- === Final === Dados do Objeto === -->
		
    </div>
	<form action="do/delete_post/<?php echo($page->objeto->valor("cod_objeto")); ?>.html" method="post" name="delete_post" id="delete_post">
	<div class="panel-footer" style="text-align: right">
		<input type="hidden" name="cod_pai" value="<?php echo($page->objeto->valor("cod_pai")); ?>">
		<input type="submit" name="submit" value="Remover Objeto" class="btn btn-danger">
	</div>
	</form>
</div>
<!-- === Final === Apagar este objeto === -->