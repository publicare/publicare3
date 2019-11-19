<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Arquivo de LOGIN, apresenta o formulário de login
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
* Este programa é distribuído na esperança de que possa ser útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
* de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
* Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
*/

// recebe codigo do objeto e trata para evitar sql injection
$cod_objeto = isset($_REQUEST["cod_objeto"])?(int)htmlspecialchars($_REQUEST['cod_objeto'], ENT_QUOTES, "UTF-8"):_ROOT;
?>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>Login - Publicare</title>
        <meta name="description" content="Login do Sistema de Gestão de Conteúdo (PUBLICARE)" />
        <meta name="keywords" content="Login, Formulário, Explicação, Gestão de Conteúdo, CMS, PHP, Fácil de usar, PUBLICARE, Formulário, CMS Público Brasileiro" />
        
        <base href="<?php echo(_URL); ?>/" target="_self" />

        <script src="include/javascript_login" type="text/javascript"></script>
        <link href="include/css_login" rel="stylesheet" type="text/css">
        
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
                        <h1 class="padding-bottom30"><div class="font-size30">Bem vindos ao</div><div class="bitter-regular font-size70"><?php echo(defined("_PORTAL_NAME")&&_PORTAL_NAME!=""?_PORTAL_NAME:"PUBLICARE"); ?></div></h1>
                        <p class="lead"><?php echo(defined("_ORGAO_NAME")&&_ORGAO_NAME!=""?_ORGAO_NAME:"Publicare, o CMS Público Brasileiro"); ?></p>
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
                                <?php if (defined("_PERMITE_CADASTRO") && _PERMITE_CADASTRO===true) { ?>
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