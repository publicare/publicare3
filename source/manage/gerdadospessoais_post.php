<?php
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

	$url = "Location:/index.php/do/gerdadospessoais/".$_page->_objeto->Valor($_page, 'cod_objeto').".html?cod_usuario=".$_POST['cod_usuario'];
	if ($Msg)
	{
		$url .= "&Msg=".urlencode($Msg)."&nome=".urlencode($_POST['nome']).'&login='.urlencode($_POST['login']).'&email='.urlencode($_POST['senha']);
		header($url);
		exit();
	}
	else
	{
		$url = "Location:/index.php/security/logout/1.html";
		header($url);
		exit();
	}
?>
		
	
