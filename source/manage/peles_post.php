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
            $sql = "select cod_pele from pele where nome = '".$nome."'";
            if ($cod_pele_atual) $sql .= " and cod_pele <> ".$cod_pele_atual;
			 		
            $rs = $_page->_db->ExecSQL($sql);
            if (!$rs->EOF)
            {
                return 'Nome de pele j&aacute; existente. Escolha outro nome.';
            }
            $sql = "select cod_pele from pele where prefixo = '".$prefixo."'";
            if ($cod_pele_atual) $sql .= " and cod_pele<>".$cod_pele_atual;
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
	$sql = "UPDATE objeto "
                    . "SET cod_pele=null "
                    . "WHERE cod_pele=".$cod_pele;
	$_page->_db->ExecSQL($sql);
	
	$sql = "DELETE "
			. "FROM pele "
			. "WHERE cod_pele=".$cod_pele;
	$_page->_db->ExecSQL($sql);
	
	$cod_pele=0;
}
else
{
	// se nao tiver problema nos campos	
	if ($msg=='')
	{
		if ($cod_pele > 0 && isset($_POST['update']))
		{
			$skinPublica = " publica = ".$publica;
			
			$sql = "UPDATE pele "
					. "SET nome='".$nome."', "
					. "prefixo='".$prefixo."', "
					. "".$skinPublica." "
					. "WHERE cod_pele=".$cod_pele;
			$_page->_db->ExecSQL($sql);
		}
		elseif ($_POST['new'])
		{
			$skinPublica = $publica;
			
			$campos=array();
			$campos['nome'] = $nome;
			$campos['prefixo'] = $prefixo;
			$campos['publica'] = $skinPublica;
			
			$cod_pele = $_page->_db->Insert('pele', $campos);
		}
	}
	else
	{
		header("Location:"._URL."/do/peles/".$_page->_objeto->Valor($_page, "cod_objeto").".html?erro=".urlencode($msg)."&cod_pele=".$cod_pele."&nome=".urlencode($nome)."&prefixo=".urlencode($prefixo)."&publica=".$publica);
		exit();
	}
}

header("Location:"._URL."/do/peles/".$_page->_objeto->Valor($_page, "cod_objeto").".html?cod_pele=".$cod_pele);
exit();