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
namespace Pbl\Core;
global $page;

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
					$this->container["db"]->execSQL($sql);
				}
			}
			else
			{
				$Msg="Senha diferente da confirmação ou muito simples. Digite novamente.";
			}
		
	}

	$url = "Location:".$this->container["config"]->portal["url"]."/do/gerdadospessoais/".$this->container["objeto"]->valor('cod_objeto').".html?cod_usuario=".$_POST['cod_usuario'];
	if ($Msg)
	{
		$url .= "&Msg=".urlencode($Msg)."&nome=".urlencode($_POST['nome']).'&login='.urlencode($_POST['login']).'&email='.urlencode($_POST['senha']);
		header($url);
		exit();
	}
	else
	{
		$url = "Location:".$this->container["config"]->portal["url"]."/security/logout/1.html";
		header($url);
		exit();
	}
?>
		
	
