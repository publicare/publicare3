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
global $page;

// define valores de maximo de tentativas e tempo de espera
$maximotentativas = 5;
$tempoespera = 10;

// recebe dados do formulário
// recebe dados de login e trata para evitar sql injection
$usuario = isset($_POST['login']) ? htmlspecialchars($_POST['login'], ENT_QUOTES, "UTF-8") : "";
// recebe dados de senha e trata para evitar sql injection
$senha = isset($_POST["password"]) ? htmlspecialchars($_POST['password'], ENT_QUOTES, "UTF-8") : "";
// recebe codigo do objeto e trata para evitar sql injection
$cod_objeto = isset($_REQUEST["cod_objeto"]) ? (int) htmlspecialchars($_REQUEST['cod_objeto'], ENT_QUOTES, "UTF-8") : $page->config["portal"]["objroot"];



// se tiver informado usuario e senha
if ($usuario != "" && $senha != "") {

    // se não tiver sessão de tentativas iniciada, inicia a sessão
    if (!isset($_SESSION['_LOGIN_TENTATIVAS'])) {
        $_SESSION['_LOGIN_TENTATIVAS'] = 0;
        $_SESSION['_LOGIN_DATA'] = date("Y-m-d H:i:s");
    }
    // se ja tiver sessão iniciada
    else {
        $dataAtual = date("Y-m-d H:i:s");
        $dataPrimeiraTentativa = $_SESSION['_LOGIN_DATA'];

        $data1 = new \DateTime($dataAtual);
        $data2 = new \DateTime($dataPrimeiraTentativa);

        $dateDiff = $data1->diff($data2);
        // se tiver passado o tempo de espera, reinicia contagem
        if ($dateDiff->i >= $tempoespera) {
            $_SESSION['_LOGIN_TENTATIVAS'] = 0;
            $_SESSION['_LOGIN_DATA'] = date("Y-m-d H:i:s");
        }
    }

    // se tiver estourado o maximo de tentativas, exibe mensagem para usuario esperar
    if ($_SESSION['_LOGIN_TENTATIVAS'] >= $maximotentativas * 100) {
        $_SESSION['_LOGIN_DATA'] = date("Y-m-d H:i:s");
        header("Location:" . $page->config["portal"]["url"] . "/login/?cod_objeto=" . $cod_objeto . "&LoginMessage=" . urlencode("Muitas tentativas. Aguarde " . $tempoespera . " minutos para tenta novamente."));
        exit();
    }

    // se não tiver estourado o maximo de tentativas, envia dados para login
    else {
        if (!$page->usuario->Login($usuario, $senha)) {
            $_SESSION['_LOGIN_TENTATIVAS'] ++;
            $_SESSION['_LOGIN_DATA'] = date("Y-m-d H:i:s");
            header("Location:" . $page->config["portal"]["url"] . "/login/?cod_objeto=" . $cod_objeto . "&LoginMessage=" . urlencode("Usuário/Senha incorretos."));
            exit();
        } else {
            unset($_SESSION['_LOGIN_TENTATIVAS']);
            unset($_SESSION['_LOGIN_DATA']);
//xd($_SESSION);
            if ($_SESSION["usuario"]['altera_senha'] == 1)
            {
                header("Location: /do/gerdadospessoais/" . $cod_objeto . ".html?LoginMessage=" . urlencode("É necessário alterar a senha"));
                exit();
            }
            $obj = new Objeto($page, $cod_objeto);
            header("Location:" . $page->config["portal"]["url"] . $obj->Valor("url") . "?LoginMessage=" . urlencode("Login realizado com sucesso"));
        }
    }
} else {
    header("Location:" . $page->config["portal"]["url"] . "/login/?cod_objeto=" . $cod_objeto . "&LoginMessage=" . urlencode("Informe os dados de usuário e senha"));
    exit();
}
