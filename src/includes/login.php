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

// recebe codigo do objeto e trata para evitar sql injection
$cod_objeto = isset($_REQUEST["cod_objeto"])?(int)htmlspecialchars($_REQUEST['cod_objeto'], ENT_QUOTES, "UTF-8"):$container["config"]->portal["objroot"];
?>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>Login - Publicare</title>
        <meta name="description" content="Login do Sistema de Gestão de Conteúdo (PUBLICARE)" />
        <meta name="keywords" content="Login, Formulário, Explicação, Gestão de Conteúdo, CMS, PHP, Fácil de usar, PUBLICARE, Formulário, CMS Público Brasileiro" />
        
        <base href="<?php echo($container["config"]->portal["url"]); ?>/" target="_self" />

        <script src="include/javascript" type="text/javascript"></script>
        <link href="include/css" rel="stylesheet" type="text/css">
        
        <script type="text/javascript">
            $("document").ready(function(){
                $("#formLogin").validate();
            });
        </script>
    </head>

    <body>

        <!-- === Explição da tela (Lado Esquerdo) e Formulário de Login (Lado Direito) === -->
        <div class="container-login">
            <div class="row linha-login">
				
                <!-- === Explição da tela (Lado Esquerdo) === -->
                <div class="col-sm-12 col-md-6 col-lg-7 list">
                    <div class="text-center text-white">
                        <h1 class="padding-bottom30"><div class="font-size30">Bem vindos ao</div><div class="bitter-regular font-size70"><?php echo(isset($page->config["portal"]["nome"]) && $page->config["portal"]["nome"]!=""?$page->config["portal"]["nome"]:"PUBLICARE"); ?></div></h1>
                        <p class="lead"><?php echo(isset($page->config["portal"]["orgao"]) && $page->config["portal"]["orgao"]!=""?$page->config["portal"]["orgao"]:"Publicare, o CMS Público Brasileiro"); ?></p>
                    </div>
                </div>
                <!-- === FInal === Explição da tela (Lado Esquerdo) === -->
				
                <!-- === Formulário de Login (Lado Direito) === -->
                <div class="col-sm-12 col-md-6 col-lg-5 list bg-white">
                    <div class="form-login">
                        <h2 class="bitter-regular font-size50 padding-bottom30">LOGIN</h2>
<?php

if (isset($_GET["LoginMessage"]) && strlen($_GET["LoginMessage"])>0)
{
?>
                        <div class="alert alert-danger"><?php
                        echo htmlspecialchars(urldecode($_GET["LoginMessage"]), ENT_QUOTES, "UTF-8");
                        ?></div>
<?php
}
?>
                        <form action="do/login_post/" method="post" name="formLogin" id="formLogin">
                            <fieldset id="login">
                                <ul>
                                    <li><label for="login">Login</label><input type="text" name="login" id="login" value="" class="required"></li>
                                    <li><label for="password">Senha</label><input type="password" name="password" id="password" value="" class="required"></li>
                                    <input type="hidden" name="cod_objeto" value="<?php echo($cod_objeto); ?>" />
                                    <li><input class="btn btn-primary border pblBotaoForm" type="submit" name="submit" value="Logar"></li>
                                </ul>
                            </fieldset>
                            <div class="row">
                                <?php if (isset($page->config["portal"]["permitecadastro"]) && $page->config["portal"]["permitecadastro"] === true) { ?>
                                <div class="col-md-6 text-left"><a href="cadastro">Cadastre-se</a></div>
                                <?php } ?>
                                <div class="col-md-6 text-right">Esqueci a senha</div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- === Final === Formulário de Login (Lado Direito) === -->
				
            </div>
        </div>
        <!-- === Final === Explição da tela (Lado Esquerdo) e Formulário de Login (Lado Direito) === -->

    </body>
</html>