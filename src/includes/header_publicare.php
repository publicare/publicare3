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


/**
 * Arquivo responsavel por montar o header do Publicare
 * 
 */
global $PORTAL_NAME, $cod_objeto, $container;

$msg = isset($_REQUEST["msg"]) ? htmlspecialchars(urldecode($_REQUEST["msg"]), ENT_QUOTES, "UTF-8") : "";
$msge = isset($_REQUEST["msge"]) ? htmlspecialchars(urldecode($_REQUEST["msge"]), ENT_QUOTES, "UTF-8") : "";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br">
    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title> <?php echo($container["config"]->portal["nome"]); ?> -- <?php echo _VERSIONPROG; ?></title>
		<meta name="description" content="Sistema de Gestão de Conteúdo (PUBLICARE)" />
		<meta name="keywords" content="Gestão de Conteúdo, CMS, PHP, Fácil de usar, PUBLICARE, Formulário, CMS Público Brasileiro" />
                
		<base href="<?php echo($container["config"]->portal["url"]); ?>/" target="_self" />

		<script src="include/javascript" type="text/javascript"></script>
		<link href="include/css" rel="stylesheet" type="text/css">    
    </head>

    <body>
		
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
			<li><a class="codrops-icon codrops-icon-drop logout" href="<?php echo($container["config"]->portal["url"]); ?>/do/logout"><i class="fapbl fapbl-unlock-alt"></i><span>&nbsp;Logout</span></a></li>
			<!-- === Final === Logout === -->
			
		</ul> 
		<!-- === Final === Menu, Usuário logado e Logout === -->
		
		<!-- === Container (Conteúdo) === -->
		<div id="container-tela">
			<div class="container-tela-int">
				
				<!-- === Conteúdo === --> 
                                
<?php
if ($msge!="")
{
?>
    <div class="alert alert-danger alert-dismissible fade in modelo_apagar" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">x</span></button>
        <h4><b>Erro!</b></h4>
        <p><?php echo($msge); ?></p>
        <p><button type="button" class="btn btn-default naoapagar" data-dismiss="alert" aria-label="Fechar">Fechar</button></p>
    </div>
<?php
}
if ($msg!="")
{
?>
    <div class="alert alert-success alert-dismissible fade in modelo_apagar" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">x</span></button>
        <h4><b>Sucesso!</b></h4>
        <p><?php echo($msg); ?></p>
        <p><button type="button" class="btn btn-default naoapagar" data-dismiss="alert" aria-label="Fechar">Fechar</button></p>
    </div>
<?php
}
?>