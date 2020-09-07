<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @file 
 * @description 
 * @copyright MIT © 2020
 * @package publicare/classes
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
/*
*
* Arquivo responsavel por montar o menu PUBLICARE nos portais
**/

global $PORTAL_NAME, $cod_objeto, $page, $menu;
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
					$menu = $container["usuario"]->menu();
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
								<li><a href='".$container["config"]->portal["url"]."/".$item["script"]."/".$container["objeto"]->valor('cod_objeto').".html'><i class='fapbl ".$item["icone"]."'></i>".$item["acao"]."</a></li>";
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
			<li><a class="codrops-icon codrops-icon-drop logout" href="<?php echo($container["config"]->portal["url"]); ?>/do/logout"><i class="fapbl fapbl-unlock-alt"></i><span>&nbsp;Sair</span></a></li>
			<!-- === Final === Logout === -->
			
		</ul> 
		<!-- === Final === Menu, Usuário logado e Logout === -->