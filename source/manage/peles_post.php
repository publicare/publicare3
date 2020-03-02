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

function ChecaValidade(&$_page, $nome, $prefixo, $cod_pele_atual)
{
    if ($nome == '')
    {
        return 'Informe um nome para a pele.';
    }
    elseif ($prefixo == '')
    {
        return 'Informe um prefixo para a pele.';
    }
    else
    {
        if (preg_match('&\W&is', $prefixo))
        {
            return 'Prefixo cont&eacute;m caracteres inv&aacute;lidos.';
        }
        else
        {
            $sql = "SELECT ".$_page->_db->tabelas["pele"]["colunas"]["cod_pele"]." AS cod_pele "
                    . " FROM ".$_page->_db->tabelas["pele"]["nome"]." "
                    . " WHERE ".$_page->_db->tabelas["pele"]["colunas"]["nome"]." = '".$nome."'";
            if ($cod_pele_atual) $sql .= " AND ".$_page->_db->tabelas["pele"]["colunas"]["cod_pele"]." <> ".$cod_pele_atual;
            $rs = $_page->_db->ExecSQL($sql);
            if (!$rs->EOF)
            {
                return 'Nome de pele j&aacute; existente. Escolha outro nome.';
            }
            
            $sql = "SELECT ".$_page->_db->tabelas["pele"]["colunas"]["cod_pele"]." AS cod_pele "
                    . " FROM ".$_page->_db->tabelas["pele"]["nome"]." "
                    . " WHERE ".$_page->_db->tabelas["pele"]["colunas"]["prefixo"]." = '".$prefixo."'";
            if ($cod_pele_atual) $sql .= " AND ".$_page->_db->tabelas["pele"]["colunas"]["cod_pele"]." <> ".$cod_pele_atual;
            $rs = $_page->_db->ExecSQL($sql);
            if (!$rs->EOF)
            {
                return 'Prefixo j&aacute; existente. Escolha outro prefixo.';
            }

        }
    }
    return '';
}

$nome = isset($_POST['nome'])?htmlspecialchars($_POST['nome'], ENT_QUOTES, "UTF-8"):"";
$prefixo = isset($_POST['prefixo'])?htmlspecialchars($_POST['prefixo'], ENT_QUOTES, "UTF-8"):"";
$cod_pele = isset($_POST['cod_pele'])?(int)htmlspecialchars($_POST['cod_pele'], ENT_QUOTES, "UTF-8"):0;
$publica = isset($_POST['publica'])?(int)htmlspecialchars($_POST['publica'], ENT_QUOTES, "UTF-8"):0;

//Checa se os dados enviados são válidos
$msg = ChecaValidade($_page, $nome, $prefixo, $cod_pele);

// se tiver codigo da pele e for clicado o botao de excluir
if ($cod_pele > 0 && isset($_POST['delete']))
{
    $cod_pele = $_page->_administracao->ApagarPele($cod_pele);
}
else
{
    // se nao tiver problema nos campos	
    if ($msg=='')
    {
        // Atualiza
        if ($cod_pele > 0 && isset($_POST['update']))
        {
            $_page->_administracao->AtualizarPele($cod_pele, $nome, $prefixo, $publica);
        }
        // cria
        elseif ($_POST['new'])
        {
            $cod_pele = $_page->_administracao->CriaPele($nome, $prefixo, $publica);
        }
    }
    else
    {
        header("Location:".$_page->config["portal"]["url"]."/do/peles/".$_page->_objeto->Valor("cod_objeto").".html?erro=".urlencode($msg)."&cod_pele=".$cod_pele."&nome=".urlencode($nome)."&prefixo=".urlencode($prefixo)."&publica=".$publica);
        exit();
    }
}

header("Location:".$_page->config["portal"]["url"]."/do/peles/".$_page->_objeto->Valor("cod_objeto").".html?cod_pele=".$cod_pele);
exit();