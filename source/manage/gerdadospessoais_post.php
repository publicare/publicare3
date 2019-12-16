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

	if ($_POST['submit'])
	{ 	
			if (($_POST['senha']==$_POST['confirma']) && ($_POST['senha']!=$_POST['login']))
			{
				if ($_POST['cod_usuario'])
				{
					$sql = "update usuario set nome='".$_POST['nome']."', email='".$_POST['email']."', secao='".$_POST['secao']."', altera_senha='".$_POST['altera_senha']."' , ramal='".$_POST['ramal']."'";
					if ($_POST['senha']!=$_POST['nomehidden'])
						$sql .= ",senha='".md5($_POST['senha'])."'";
					$sql .= " where cod_usuario=".$_POST['cod_usuario'];
					$_page->_db->ExecSQL($sql);
				}
			}
			else
			{
				$Msg="Senha diferente da confirmação ou muito simples. Digite novamente.";
			}
		
	}

	$url = "Location:"._URL."/do/gerdadospessoais/".$_page->_objeto->Valor($_page, 'cod_objeto').".html?cod_usuario=".$_POST['cod_usuario'];
	if ($Msg)
	{
		$url .= "&Msg=".urlencode($Msg)."&nome=".urlencode($_POST['nome']).'&login='.urlencode($_POST['login']).'&email='.urlencode($_POST['senha']);
		header($url);
		exit();
	}
	else
	{
		$url = "Location:"._URL."/security/logout/1.html";
		header($url);
		exit();
	}
?>
		
	
