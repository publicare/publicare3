<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo gerusuario_post.php é receber os dados de usuarios e gravar e/ou apagar
 * @copyright GPL © 2007
 * @package publicare/manage
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

// recupera dados do form
$cod_usuario = isset($_POST['cod_usuario'])?(int)htmlspecialchars($_POST["cod_usuario"], ENT_QUOTES, "UTF-8"):0;
$nomehidden = isset($_POST['nomehidden'])?htmlspecialchars($_POST["nomehidden"], ENT_QUOTES, "UTF-8"):"";
$nome = isset($_POST['nome'])?htmlspecialchars($_POST["nome"], ENT_QUOTES, "UTF-8"):"";
$secao = isset($_POST['secao'])?htmlspecialchars($_POST["secao"], ENT_QUOTES, "UTF-8"):"";
$login = isset($_POST['login'])?htmlspecialchars($_POST["login"], ENT_QUOTES, "UTF-8"):"";
$email = isset($_POST['email'])?htmlspecialchars($_POST["email"], ENT_QUOTES, "UTF-8"):"";
$ramal = isset($_POST['ramal'])?htmlspecialchars($_POST["ramal"], ENT_QUOTES, "UTF-8"):"";
$senha = isset($_POST['senha'])?htmlspecialchars($_POST["senha"], ENT_QUOTES, "UTF-8"):"";
$confsenha = isset($_POST['confsenha'])?htmlspecialchars($_POST["confsenha"], ENT_QUOTES, "UTF-8"):"";
$chefia = isset($_POST['chefia'])?(int)htmlspecialchars($_POST["chefia"], ENT_QUOTES, "UTF-8"):0;
$altera_senha = isset($_POST['altera_senha'])?(int)htmlspecialchars($_POST["altera_senha"], ENT_QUOTES, "UTF-8"):0;
$ldap = isset($_POST['ldap'])?(int)htmlspecialchars($_POST["ldap"], ENT_QUOTES, "UTF-8"):0;
$data_atualizacao = isset($_POST['data_atualizacao'])?htmlspecialchars($_POST["data_atualizacao"], ENT_QUOTES, "UTF-8"):"";
$perfil = isset($_POST['perfil'])?(int)htmlspecialchars($_POST["perfil"], ENT_QUOTES, "UTF-8"):0;

//xd($_POST);

$perfil_chefia = $_page->_usuario->PegaListaDeUsuarios($_page);

$msg = "";
$msge = "";
$gets = "";
$acao = "";

$tmpCheckProcedencia = false;
foreach ($perfil_chefia as $sub) 
{
    // Verifica se o usuario atual e chefe do usuario que esta sendo modificado
    if ($_POST['cod_usuario'] == $sub['codigo']) 
    {
        $tmpCheckProcedencia = true;
        break;
    }
}

// se for administrador
if ($_SESSION['usuario']['perfil'] == 1)
{
    // verifica se existe psot enviado e se é novo usuário
    if ($_POST && isset($_POST["btnGravar"]) && $_POST["btnGravar"] == "Gravar") 
    {

        if ($_page->_usuario->ExisteOutroUsuario($_page, $login, $cod_usuario))
        {
            $msge = "Login '".$login."' já existe. Por favor escolha outro.";
            $gets = "&nome=".urlencode($nome)."&secao=".urlencode($secao)."&"
                            ."login=".urlencode($login)."&email=".urlencode($email)."&ramal=".urlencode($ramal)."&"
                            ."chefia=".urlencode($chefia)."&ldap=".urlencode($ldap)."&"
                            ."data_atualizacao=".urlencode($data_atualizacao)."&perfil=".urlencode($perfil);
            $acao = "novo";
        }
        else
        {
            if ($senha == $confsenha)
            {
                // monta array com dados do usuário
                $dados = array("cod_usuario"=>$cod_usuario,
                    "nome"=>$nome,
                    "secao"=>$secao,
                    "login"=>$login,
                    "email"=>$email,
                    "ramal"=>$ramal,
                    "senha"=>$senha,
                    "chefia"=>$chefia,
                    "altera_senha"=>$altera_senha,
                    "ldap"=>$ldap,
                    "data_atualizacao"=>$data_atualizacao);
                
                // se tiver codigo de usuario, significa que é update
                if ($dados["cod_usuario"] > 0)
                {
                    // atualiza dados do usuario no banco
                    $_page->_usuario->atualizaUsuario($_page, $dados);

                    // apaga perfis selecionados
                    if (isset($_POST['checkadmperfil']) && is_array($_POST['checkadmperfil']))
                    {
                        foreach ($_POST['checkadmperfil'] as $tmpObjQuadro) 
                        {
                            $_page->_usuario->AlterarPerfilDoUsuarioNoObjeto($_page, $dados["cod_usuario"], $tmpObjQuadro, _PERFIL_DEFAULT, false);
                        }
                    }
                    // grava perfil do usuario no objeto atual
                    if ($perfil > 0)
                    {
                        if ($perfil != _PERFIL_ADMINISTRADOR)
                        {
                            $_page->_usuario->AlterarPerfilDoUsuarioNoObjeto($_page, $cod_usuario, $_page->_objeto->Valor($_page, 'cod_objeto'), $perfil);
                        }
                        // caso seja admin, atribui perfil no objeto root e apaga todas as outras entradas
                        else
                        {
                            $_page->_usuario->limpaPerfisUsuario($_page, $cod_usuario);
                            $_page->_usuario->AlterarPerfilDoUsuarioNoObjeto($_page, $_POST['cod_usuario'], _ROOT, _PERFIL_ADMINISTRADOR);
                        }
                    }
                    $msg = "Usuário atualizado com êxito.";
                }
                else
                {
                    $dadosinsert = $dados;
                    $dadosinsert["valido"] = 1;
                    $dadosinsert["data_atualizacao"] = ConverteData($dadosinsert['data_atualizacao'], 16);
                    $dadosinsert["senha"] = md5($dadosinsert["senha"]);
                    unset($dadosinsert["cod_usuario"]);
                    unset($dadosinsert["nomehidden"]);
                    unset($dadosinsert["confsenha"]);
                    unset($dadosinsert["perfil"]);

                    $cod_usuario = $_page->_db->Insert('usuario', $dadosinsert);

                    // Se não tiver nenhum perfil selecionado, coloca o perfil default
                    if (strlen($perfil) > 0) {
                        // DEFINE PERFIL SO_LOGADO PARA OBJETO ROOT NO SITE -- CASO N�O SEJA O OBJETO ROOT QUE ESTEJA SENDO DEFINIDO
                        if ($_page->_objeto->Valor($_page, 'cod_objeto') != _ROOT) {
                            $_page->_usuario->AlterarPerfilDoUsuarioNoObjeto($_page, $cod_usuario, _ROOT, _PERFIL_RESTRITO);
                            $_page->_usuario->AlterarPerfilDoUsuarioNoObjeto($_page, $cod_usuario, $_page->_objeto->Valor($_page, 'cod_objeto'), $perfil);
                        } else {
                            $_page->_usuario->AlterarPerfilDoUsuarioNoObjeto($_page, $cod_usuario, _ROOT, $perfil);
                        }
                    } else {
                        $_page->_usuario->AlterarPerfilDoUsuarioNoObjeto($_page, $cod_usuario, _ROOT, _PERFIL_DEFAULT);
                    }
                    $msg = "Usuário criado com êxito.";
                }
                
                
                    
            } else {
                $msge = "Senha diferente da confirmação. Digite novamente.";
            }
        }
    } 
    elseif ($_POST && isset($_POST["btnApagar"]) && $_POST["btnApagar"]=="Apagar" && $cod_usuario > 0) 
    {
        $_page->_usuario->bloquearUsuario($_page, $cod_usuario);
        $_page->_usuario->AlterarPerfilDoUsuarioNoObjeto($_page, $cod_usuario, $_page->_objeto->Valor($_page, 'cod_objeto'), _PERFIL_DEFAULT);
    }
} else {
    $msge = "Acesso negado a edição deste usuário.";
}

$url = "Location:/do/gerusuario/" . $_page->_objeto->Valor($_page, 'cod_objeto') . ".html?acao=".$acao.$gets;

if ($msg!="") $url .= "&msg=" . urlencode($msg);
if ($msge!="") $url .= "&msge=" . urlencode($msge);


header($url);
