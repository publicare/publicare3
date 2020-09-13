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

$perfil_chefia = $this->container["usuario"]->pegarListaUsuarios();

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

        if ($this->container["usuario"]->existeOutroUsuario($login, $cod_usuario))
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
                    $this->container["usuario"]->atualizarUsuario($dados);

                    // apaga perfis selecionados
                    if (isset($_POST['checkadmperfil']) && is_array($_POST['checkadmperfil']))
                    {
                        foreach ($_POST['checkadmperfil'] as $tmpObjQuadro) 
                        {
                            $this->container["usuario"]->alterarPerfilUsuarioObjeto($dados["cod_usuario"], $tmpObjQuadro, _PERFIL_DEFAULT, false);
                        }
                    }
                    // grava perfil do usuario no objeto atual
                    if ($perfil > 0)
                    {
                        if ($perfil != _PERFIL_ADMINISTRADOR)
                        {
                            $this->container["usuario"]->alterarPerfilUsuarioObjeto($cod_usuario, $this->container["objeto"]->valor('cod_objeto'), $perfil);
                        }
                        // caso seja admin, atribui perfil no objeto root e apaga todas as outras entradas
                        else
                        {
                            $this->container["usuario"]->limparPerfisUsuario($cod_usuario);
                            $this->container["usuario"]->alterarPerfilUsuarioObjeto($_POST['cod_usuario'], $this->container["config"]->portal["objroot"], _PERFIL_ADMINISTRADOR);
                        }
                    }
                    $msg = "Usuário atualizado com êxito.";
                }
                else
                {
                    $dadosinsert = array();
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]] = $dados["nome"];
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]] = $dados["secao"];
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["login"]] = $dados["login"];
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["email"]] = $dados["email"];
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ramal"]] = $dados["ramal"];
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["senha"]] = md5($dados["senha"]);
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]] = $dados["chefia"];
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["altera_senha"]] = $dados["altera_senha"];
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["ldap"]] = $dados["ldap"];
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["data_atualizacao"]] = ConverteData($dados['data_atualizacao'], 16);
                    $dadosinsert[$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]] = 1;
//                    $dadosinsert["valido"] = 1;
//                    $dadosinsert["data_atualizacao"] = ConverteData($dadosinsert['data_atualizacao'], 16);
//                    $dadosinsert["senha"] = md5($dadosinsert["senha"]);
//                    unset($dadosinsert["cod_usuario"]);
//                    unset($dadosinsert["nomehidden"]);
//                    unset($dadosinsert["confsenha"]);
//                    unset($dadosinsert["perfil"]);

                    $cod_usuario = $page->db->Insert($this->container["config"]->bd["tabelas"]["usuario"]["nome"], $dadosinsert);

                    // Se não tiver nenhum perfil selecionado, coloca o perfil default
                    if (strlen($perfil) > 0) {
                        // DEFINE PERFIL SO_LOGADO PARA OBJETO ROOT NO SITE -- CASO N�O SEJA O OBJETO ROOT QUE ESTEJA SENDO DEFINIDO
                        if ($this->container["objeto"]->valor('cod_objeto') != $this->container["config"]->portal["objroot"]) {
                            $this->container["usuario"]->alterarPerfilUsuarioObjeto($cod_usuario, $this->container["config"]->portal["objroot"], _PERFIL_RESTRITO);
                            $this->container["usuario"]->alterarPerfilUsuarioObjeto($cod_usuario, $this->container["objeto"]->valor('cod_objeto'), $perfil);
                        } else {
                            $this->container["usuario"]->alterarPerfilUsuarioObjeto($cod_usuario, $this->container["config"]->portal["objroot"], $perfil);
                        }
                    } else {
                        $this->container["usuario"]->alterarPerfilUsuarioObjeto($cod_usuario, $this->container["config"]->portal["objroot"], _PERFIL_DEFAULT);
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
        $this->container["usuario"]->bloquearUsuario($cod_usuario);
        $this->container["usuario"]->alterarPerfilUsuarioObjeto($cod_usuario, $this->container["objeto"]->valor('cod_objeto'), _PERFIL_DEFAULT);
    }
} else {
    $msge = "Acesso negado a edição deste usuário.";
}

$url = "Location:".$this->container["config"]->portal["url"]."/do/gerusuario/" . $this->container["objeto"]->valor('cod_objeto') . ".html?acao=".$acao.$gets;

if ($msg!="") $url .= "&msg=" . urlencode($msg);
if ($msge!="") $url .= "&msge=" . urlencode($msge);


header($url);
