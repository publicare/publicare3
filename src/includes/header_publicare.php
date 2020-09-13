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
global $container;

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

		<?php include(__DIR__."/menu_publicare.php"); ?>
		
		<div id="container-tela">
			<div class="container-tela-int">
				
