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
namespace Pbl;

global $page;

$cod_objeto = $this->container["objeto"]->valor("cod_objeto");

if (isset($_POST['copy'])) $this->container["administracao"]->copiarObjeto($_POST['cod_objmanage'], $this->container["objeto"]->valor("cod_objeto"));
elseif (isset($_POST['pastelink'])) $this->container["administracao"]->colarComoLink($_POST['cod_objmanage'], $this->container["objeto"]->valor("cod_objeto"));
elseif (isset($_POST['move'])) $this->container["administracao"]->moverObjeto($_POST['cod_objmanage'], $this->container["objeto"]->valor("cod_objeto"));
elseif (isset($_POST['clear'])) $this->container["administracao"]->limparPilha();
		
header ("Location:".$this->container["config"]->portal["url"].'/do/list_content/'.$cod_objeto.'.html');
?>
