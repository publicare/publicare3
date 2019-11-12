<?php

/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo login_post, recebe os dados de login e realiza tratativas para logar o usuario
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
global $_page;

// define valores de maximo de tentativas e tempo de espera
$maximotentativas = 5;
$tempoespera = 10;

// recebe dados do formulário
// recebe dados de login e trata para evitar sql injection
$usuario = isset($_POST['login']) ? htmlspecialchars($_POST['login'], ENT_QUOTES, "UTF-8") : "";
// recebe dados de senha e trata para evitar sql injection
$senha = isset($_POST["password"]) ? htmlspecialchars($_POST['password'], ENT_QUOTES, "UTF-8") : "";
// recebe codigo do objeto e trata para evitar sql injection
$cod_objeto = isset($_REQUEST["cod_objeto"]) ? (int) htmlspecialchars($_REQUEST['cod_objeto'], ENT_QUOTES, "UTF-8") : _ROOT;

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
        header("Location:" . _URL . "/login/?cod_objeto=" . $cod_objeto . "&LoginMessage=" . urlencode("Muitas tentativas. Aguarde " . $tempoespera . " minutos para tenta novamente."));
        exit();
    }

    // se não tiver estourado o maximo de tentativas, envia dados para login
    else {
        if (!$_page->_usuario->Login($_page, $usuario, $senha)) {
            $_SESSION['_LOGIN_TENTATIVAS'] ++;
            $_SESSION['_LOGIN_DATA'] = date("Y-m-d H:i:s");
            header("Location:" . _URL . "/login/?cod_objeto=" . $cod_objeto . "&LoginMessage=" . urlencode("Usuário/Senha incorretos."));
            exit();
        } else {
            unset($_SESSION['_LOGIN_TENTATIVAS']);
            unset($_SESSION['_LOGIN_DATA']);
            if ($_SESSION["usuario"]['altera_senha'] == 1)
            {
                header("Location: /do/gerdadospessoais/" . $cod_objeto . ".html?LoginMessage=" . urlencode("É necessário alterar a senha"));
                exit();
            }
            $obj = new Objeto($_page, $cod_objeto);
            header("Location:" . _URL . $obj->Valor($_page, "url") . "?LoginMessage=" . urlencode("Login realizado com sucesso"));
        }
    }
} else {
    header("Location:" . _URL . "/login/?cod_objeto=" . $cod_objeto . "&LoginMessage=" . urlencode("Informe os dados de usuário e senha"));
    exit();
}
