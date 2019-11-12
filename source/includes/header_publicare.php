<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Classe Administração, responsável por administrar os objetos (criar, editar objetos e classes)
* @author Diogo Corazolla <diogocorazolla@gmail.com>, Thiago Borges <thiago.m2r@gmail.com>, Manuel Poppe <manuelpoppe@gmail.com>
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

/**
 * Arquivo responsavel por montar o header do Publicare
 * 
 */
global $PORTAL_NAME, $cod_objeto, $_page;

$msg = isset($_REQUEST["msg"]) ? htmlspecialchars(urldecode($_REQUEST["msg"]), ENT_QUOTES, "UTF-8") : "";
$msge = isset($_REQUEST["msge"]) ? htmlspecialchars(urldecode($_REQUEST["msge"]), ENT_QUOTES, "UTF-8") : "";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br">
    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title> <? echo $PORTAL_NAME ;?> -- <?php echo _VERSIONPROG; ?></title>
		<meta name="description" content="Sistema de Gestão de Conteúdo (PUBLICARE)" />
		<meta name="keywords" content="Gestão de Conteúdo, CMS, PHP, Fácil de usar, PUBLICARE, Formulário, CMS Público Brasileiro" />
		<meta name="author" content="EPL" />

		<script src="/include/javascript" type="text/javascript"></script>
		<link href="/include/css" rel="stylesheet" type="text/css">    
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
					$menu = $_page->_usuario->Menu($_page);
//                  xd($menu);
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
							echo "
								<li><a href='".$item["script"]."/".$_page->_objeto->Valor($_page, 'cod_objeto').".html'><i class='fapbl ".$item["icone"]."'></i>".$item["acao"]."</a></li>";
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
			<li><a class="codrops-icon codrops-icon-drop logout" href="/do/logout"><i class="fapbl fapbl-unlock-alt"></i><span>&nbsp;Logout</span></a></li>
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