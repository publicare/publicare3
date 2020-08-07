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
// AJUSTES
global $page, $cod;

// força o código do status para despublicado
$_POST['cod_status'] = 1;
// remover barras duplas, para evitar erro
$_POST['script_exibir'] = isset($_POST['script_exibir'])?preg_replace("[\/+]", "/", $_POST['script_exibir']):""; // Arruma uma falha

// chama a execução de scripts antes de gravar o objeto
$palavra = "criação";

$cod = 0;
$local = $page->config["portal"]["url"];
$acaoobj = filter_input(INPUT_POST, 'op', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$publicar = 0;

if (isset($_POST["gravaresolicitar"]))
{
    $publicar = 1;
}
elseif (isset($_POST["gravarepublicar"]))
{
    $publicar = 2;
}

$obj = $page->administracao->gravarObjeto($_POST, $acaoobj, $publicar, $cod);
$local .= $obj["obj"]->valor("url");

if (isset($_POST["gravaroutro"]))
{
    $local = $page->config["portal"]["url"]."/do/new_".$obj["obj"]->valor('prefixoclasse')."/".$obj["obj"]->valor('cod_pai').".html";
}

header("Location: ".$local);
exit();
