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
*
*
* Arquivo responsavel por montar o menu PUBLICARE nos portais
**/

global $PORTAL_NAME, $cod_objeto, $_page, $menu;
?>

<script src="include/javascript_menu" type="text/javascript"></script>
<link href="include/css_menu" rel="stylesheet" type="text/css">

<script type="text/javascript"> 
	$(document).ready(function(){	
		// Inicializar menu
		new gnMenu( document.getElementById( 'gn-menupbl' ) );
	});
</script>

		<!-- === Menu, Usuário logado e Logout === -->
		<ul id="gn-menupbl" class="gn-menu-mainpbl">
			<!-- === Menu === -->
			<li class="gn-triggerpbl">
				<a class="gn-iconpbl gn-icon-menupbl"><span>Menu</span></a>
				<nav class="gn-menu-wrapperpbl">
					<div class="gn-scrollerpbl">
						<ul class="gn-menupbl">
				<?php
					$menu = $_page->_usuario->Menu($_page);
					$cont = 0;
					foreach ($menu as $item)
					{
                                            
						if ($item["script"] == "")
						{
							$cont++;
							if ($cont > 1)
							{
							   echo "</ul></li>";
							}
							echo "
							<li><a><i class='fapbl ".$item["icone"]."'></i>".$item["acao"]."</a>
								<ul class='gn-submenupbl'>";
						}
						else
						{
                                                    if (substr($item["script"], 0, 1)=="/") $item["script"] = substr($item["script"], 1);
							echo "
								<li><a href='"._URL."/".$item["script"]."/".$_page->_objeto->Valor($_page, 'cod_objeto').".html'><i class='fapbl ".$item["icone"]."'></i>".$item["acao"]."</a></li>";
						}
					}
				?>
						</ul>
					</div>
				</nav>
			</li>			
			<!-- === Final === Menu === -->
			
			<!-- === Usuário logado === -->
			<li class="codrops-icon codrops-icon-drop logado"><div class="name"><?php echo($_SESSION["usuario"]["nome"]); ?></div></li>
			<!-- === Final === Usuário logado === -->
			
			<!-- === Logout === -->
			<li><a class="codrops-icon codrops-icon-drop logout" href="<?php echo(_URL); ?>/do/logout"><i class="fapbl fapbl-unlock-alt"></i><span>&nbsp;Logout</span></a></li>
			<!-- === Final === Logout === -->
			
		</ul> 
		<!-- === Final === Menu, Usuário logado e Logout === -->