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

use Pbl\Core\Base;
use Pbl\Core\Objeto;



/**
 * Classe que contém métodos para manipulação de objetos
 */
class Administracao extends Base
{
    public $classesPrefixos;
    public $classesNomes;
    public $classesIndexaveis = array();
    public $_index;
    
    /**
     * Adiciona propriedade em classe
     * @param int $cod_classe - Codigo da classe
     * @param array $novo - Dados da propriedade
     */
    function acrescentarPropriedadeAClasse($cod_classe, $novo)
    {
        $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["propriedade"]["nome"]." ("
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_classe"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_tipodado"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_referencia_classe"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["campo_ref"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["nome"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["posicao"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["rotulo"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["descricao"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["obrigatorio"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["seguranca"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["valorpadrao"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["rot1booleano"].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["rot2booleano"].") "
                . " VALUES (".$cod_classe.", "
                . " ".$novo['tipodado'].", "
                . " ".($novo['codrefclasse']==0?"NULL":$novo['codrefclasse']).", "
                . " '".$novo['camporef']."', "
                . " '".$novo['nome']."', "
                . " ".$novo['posicao'].", "
                . " '".$novo['rotulo']."', "
                . " '".$novo['descricao']."', "
                . " ".$novo['obrigatorio'].", "
                . " ".$novo['seguranca'].", "
                . " '".$novo['valorpadrao']."', "
                . " '".$novo['rot1booleano']."', "
                . " '".$novo['rot2booleano']."')";
        $this->container["db"]->execSQL($sql);
    }
    
    
    
    /**
     * Altera objeto no banco de dados
     * @param array $dados - Dados do objeto
     * @param bool $log - Indica se deve gerar log ou não
     * @return int - Código do objeto alterado
     */
    function alterarObjeto($dados, $log = true)
    {	
        $fieldlist = array();
        $valorlist = array();
        $tagslist = array();
        $proplist = array();
        
        $cod_objeto = (int)htmlspecialchars($dados['cod_objeto'], ENT_QUOTES, "UTF-8");
        $cod_pele = (int)htmlspecialchars($dados['cod_pele'], ENT_QUOTES, "UTF-8");
        $cod_status = (int)htmlspecialchars($dados['cod_status'], ENT_QUOTES, "UTF-8");
        $url_amigavel = htmlspecialchars($dados['url_amigavel'], ENT_QUOTES, "UTF-8");
        $cod_pai = (int)htmlspecialchars($dados['cod_pai'], ENT_QUOTES, "UTF-8");
        $script_exibir = htmlspecialchars($dados['script_exibir'], ENT_QUOTES, "UTF-8");
        $cod_classe = (int)htmlspecialchars($dados['cod_classe'], ENT_QUOTES, "UTF-8");
        $cod_usuario = (int)htmlspecialchars($dados['cod_usuario'], ENT_QUOTES, "UTF-8");
        $titulo = htmlspecialchars($dados['titulo'], ENT_QUOTES, "UTF-8");
        $descricao = htmlspecialchars($dados['descricao'], ENT_QUOTES, "UTF-8");
        $data_publicacao = htmlspecialchars($dados['data_publicacao'], ENT_QUOTES, "UTF-8");
        $data_validade = htmlspecialchars($dados['data_validade'], ENT_QUOTES, "UTF-8");
        $peso = (int)htmlspecialchars($dados['peso'], ENT_QUOTES, "UTF-8");
        
        // rodando o formulário
        foreach ($dados as $key=>$valor)
        {
            if ($key != "submit")
            {
                if ($key=="tags")
                {
                    $tagslist = preg_split("[,]",$valor);
                }
                if (strpos($key,"___"))
                {
                    $proplist[$key] = $valor;
                }
            }
        }

        // Verifica se teve mudança na pele e caso positivo altera a pele de todos os filhos
        $sql_pele = "SELECT ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pele"]." AS cod_pele "
                . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $row_pele = $this->container["db"]->execSQL($sql_pele);
        $row_pele = $row_pele->GetRows();
        $row_pele = $row_pele[0];
        if (is_array($row_pele) && $row_pele['cod_pele'] != $cod_pele)
        {
            $this->trocarPeleFilhos($cod_objeto, $cod_pele);
        }

        // Objeto root deverá ser sempre publicado
        if ($cod_objeto == 1 || $cod_objeto == $this->container["config"]->portal["objroot"])
        {
            $cod_status = _STATUS_PUBLICADO;
        }
        
        // verifica se já existe objeto com a URL amigável
        $url_amigavel = $this->verificarExistenciaUrlAmigavel($url_amigavel, $cod_objeto);
			
        $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                . " SET ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pai"]." = ".$cod_pai.", "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["script_exibir"]." = '".$script_exibir."', "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." = ".$cod_classe.", "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_usuario"]." = ".$cod_usuario.", ";
        if ($cod_pele > 0) 
        {
            $sql .= " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pele"]." = ".$cod_pele.", ";
        }
        else 
        {
            $sql .= " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pele"]." = null, ";
        }
        $sql .= " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_status"]." = ".$cod_status.", "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." = '".$titulo."', "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["descricao"]." = '".$descricao."', "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_publicacao"]." = '".ConverteData($data_publicacao, 27)."', "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_validade"]." = '".ConverteData($data_validade, 27)."', "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["peso"]." = ".$peso.", "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["url_amigavel"]." = '".$url_amigavel."', "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["versao"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["versao"]." + 1 "
                . "WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $this->container["db"]->execSQL($sql);

        $this->apagarPropriedades($cod_objeto, false);
        $this->gravarPropriedades($cod_objeto, $cod_classe, $proplist);
        $this->gravarTags($cod_objeto, $tagslist);
			
        if ($log)
        {
            $this->container["log"]->incluirLogObjeto($cod_objeto, _OPERACAO_OBJETO_EDITAR);
        }
        
        return $cod_objeto;
    }

    /**
     * Busca lista de classes no banco de dados e popula propriedades de classes
     */
    function carregarClasses()
    {
        $this->container["adminobjeto"]->carregarClasses();
        
        if (is_null($this->classesPrefixos) || !is_array($this->classesPrefixos))
        {
            $this->classesPrefixos = $_SESSION['classesPrefixos'];
            $this->classesNomes = $_SESSION['classesNomes'];
            $this->classes = $_SESSION['classes'];
            $this->classesIndexaveis = $_SESSION['classesIndexaveis'];
        }
    }

    /**
     * Retorna o código de uma classe com base em seu prefixo
     * @param string $prefixo - Prefixo da classe
     * @return int - Código da classe
     */
    function codigoClasse($prefixo)
    {
        $this->container["adminobjeto"]->carregarClasses();
        // xd($prefixo);
        return $_SESSION["classesPrefixos"][$prefixo];
    }

    /**
     * Busca lista de peles no banco de dados. Caso esteja logado com usuário
     * admin ve todas as peles, caso contrario somente peles publicas
     * @param int $rcvPele - Código da pele
     * @return array
     */
    function pegarListaPeles($rcvPele=NULL)
    {
        $result=array();
        $sqladd = "";
        
        if ($rcvPele && $rcvPele!=NULL && $rcvPele!=0) $sqladd = " AND ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["cod_pele"]." = ".$rcvPele;
        
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["cod_pele"]." AS codigo, "
                . " ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["nome"]." AS texto, "
                . " ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["prefixo"]." AS prefixo, "
                . " ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["publica"]." AS publica "
                . " FROM ".$this->container["config"]->bd["tabelas"]["pele"]["nome"]." "
                . " WHERE 1=1 ";
        if ($_SESSION['usuario']['perfil'] != _PERFIL_ADMINISTRADOR) {
            $sql .= " AND ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["publica"]." = '1'";
        }
        $sql .= $sqladd;
        $sql .= " ORDER BY ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["nome"];
        
        $res = $this->container["db"]->execSQL($sql);
        return $res->GetRows();
    }

    /**
     * Busca lista de usuários dependentes, caso usuario logado 
     * seja administrador traz todos usuários
     * @param int $cod_usuario - código do usuário chefe
     * @return array
     */
    function pegarListaDependentes($cod_usuario)
    {
        $result=array();
        
        if($_SESSION['usuario']['perfil'] == _PERFIL_ADMINISTRADOR)
        {
            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS codigo, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS texto, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." AS secao "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 "
                    . " OR ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = ".$cod_usuario." "
                    . " ORDER BY ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"].", ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"];
        }
        else
        {
            if ($_SESSION['usuario']['cod_usuario'] == $cod_usuario)
            {
                $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." AS codigo, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS texto, "
                    . " ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"]." AS secao "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["valido"]." = 1 "
                    . " AND (".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["chefia"]." = ".$cod_usuario." OR ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = ".$cod_usuario.") "
                    . " ORDER BY ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["secao"].", ".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"];
//                $sql = "SELECT usuario.cod_usuario AS codigo, "
//                        . "usuario.nome AS texto, "
//                        . "usuario.secao AS secao "
//                        . "FROM usuario "
//                        . "WHERE valido = 1 "
//                        . "AND (chefia = ".$cod_usuario." OR cod_usuario = ".$cod_usuario.") "
//                        . "ORDER BY secao, texto";
            }
            else 
            {
                return false;
            }
        }
        
        $rs = $this->container["db"]->execSQL($sql);
        $result = $rs->GetRows();

        return $result;
    }

    /**
     * Recebe array e monta string com <options> para o select do dropdown
     * @param type $lista
     * @param type $selecionado
     * @param type $branco
     * @param type $nummaxletras
     * @return string
     */
    function criarDropDown($lista, $selecionado, $branco=true, $nummaxletras=0)
    {
        $result = "";
        if ($branco)
        {
            $result = '<option value="" selected>&nbsp;Selecione&nbsp;</option>';
        }

        foreach($lista as $item)
        {
            $result.='<option value="'.$item['codigo'].'"';
            if (($selecionado===$item['codigo']) || ($selecionado===$item['texto']))
            {
                $result .=' selected ';
            }
            $result .= '>';
            if ($nummaxletras)
            {
                $result .= substr($item['texto'],0,$nummaxletras);
            }
            else 
            {
                $result .= $item['texto'];
            }
            $result .= '</option>';
        }

        return $result;
    }

    /**
     * Busca propriedades da classe no banco de dados e retorna array com informações
     * @param int $cod_classe
     * @return array
     */
    function pegarPropriedadesClasse($cod_classe)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("administracao::pegarPropriedadesClasse cod_classe=".$cod_classe);
        }

        $propriedades = array();
        if (isset($_SESSION["classes"][$cod_classe]["propriedades"])
            && is_array($_SESSION["classes"][$cod_classe]["propriedades"])
            && count($_SESSION["classes"][$cod_classe]["propriedades"]))
        {
            $propriedades = $_SESSION["classes"][$cod_classe]["propriedades"];
        }
        else
        {
            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_tipodado"]." AS cod_tipodado, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_propriedade"]." AS cod_propriedade, "
                . " ".$this->container["config"]->bd["tabelas"]["tipodado"]["nick"].".".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["nome"]." AS tipodado, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["campo_ref"]." AS campo_ref, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["nome"]." AS nome, "
                . " ".$this->container["config"]->bd["tabelas"]["tipodado"]["nick"].".".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["tabela"]." AS tabela, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_referencia_classe"]." AS cod_referencia_classe, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["posicao"]." AS posicao, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["descricao"]." AS descricao, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["rotulo"]." AS rotulo, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["obrigatorio"]." AS obrigatorio, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["seguranca"]." AS seguranca, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["valorpadrao"]." AS valorpadrao, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["rot1booleano"]." AS rot1booleano, "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["rot2booleano"]." AS rot2booleano "
                . " FROM ".$this->container["config"]->bd["tabelas"]["propriedade"]["nome"]." ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["tipodado"]["nome"]." ".$this->container["config"]->bd["tabelas"]["tipodado"]["nick"]." "
                . " ON ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_tipodado"]." = ".$this->container["config"]->bd["tabelas"]["tipodado"]["nick"].".".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["cod_tipodado"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_classe"]." = ".$cod_classe." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["posicao"];
            $rs = $this->container["db"]->execSQL($sql);
            $propriedades = $rs->GetRows();
            $_SESSION["classes"][$cod_classe]["propriedades"] = $propriedades;
        }
        return $propriedades;
    }

    /**
     * Busca lista de objetos, com codigo do objeto e propriedade informada, 
     * de determinada classe no banco de dados e retorna array com informações.
     * @param int $cod_classe - Código da classe
     * @param string $propriedade - Propriedade que deseja valor
     * @return array
     */
    function pegarListaObjetos($cod_classe, $propriedade)
    {
        $result=array();
        if (in_array($propriedade, $this->container["db"]->getMetadados()))
        {
            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." as codigo, "
                    . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"][$propriedade]." as texto "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." = ".$cod_classe." "
                    . " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["apagado"]." <> 1 "
                    . " ORDER BY ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"][$propriedade];
//                    . " ".$propriedade." as texto "
//                    . "from objeto "
//                    . "where cod_classe=".$cod_classe." "
//                    . "and apagado <> 1 "
//                    . "order by ".$propriedade;
        }
        else
        {
            $info = $this->container["adminobjeto"]->criarSQLPropriedade($propriedade, ' asc', $cod_classe);
            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." AS codigo, "
                    . " ".$info['field']." as texto "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
                    . " ".$info['from']." "
                    . " WHERE ".$info['where']." "
                    . " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["apagado"]." <> 1 "
                    . " ORDER BY ".$info['field'];
        }
        $res=$this->container["db"]->execSQL($sql);
        
        return $res->GetRows();
    }

    /**
     * Troca pele de filhos recursivamente de determinado objeto
     * @param int $cod_objeto - Codigo do objeto pai
     * @param int $cod_pele - Código da pele
     */
    function trocarPeleFilhos($cod_objeto, $cod_pele)
    {
        $filhos = $this->container["adminobjeto"]->pegarListaFilhosCod($cod_objeto);

        if (is_array($filhos) && count($filhos) > 0)
        {
            $sql_pele_filhos = "UPDATE ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ";
            if ($cod_pele==0) $sql_pele_filhos .= " SET ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pele"]." = null ";
            else $sql_pele_filhos .= " SET ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pele"]." = ".$cod_pele." ";
            $sql_pele_filhos .= "WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." IN (".join(',',$filhos).")";
            $this->container["db"]->execSQL($sql_pele_filhos);
            
            foreach ($filhos as $filho)
            {
                $this->trocarPeleFilhos($filho, $cod_pele);
            }
        }
    }

    /**
     * Verifica se já existe outro objeto utilizando a url amigável
     * se tiver adiciona número no final e verifica novamente
     * @param string $url - Url amigável para verificar
     * @param int $cod_objeto - Código do objeto
     * @param int $nivel - número a ser adicionado no final
     * @param int $tamanho - tamanho máximo da url amigável
     * @return string - url amigável a ser gravada
     */
    function verificarExistenciaUrlAmigavel($url, $cod_objeto=0, $nivel=0, $tamanho=0)
    {
        $urls_proibidas = array("login", "blob", "include", "html", "content", "manage", "do");
        
        $url = strtolower(limpaString($url));
        if (strlen($url)>249) { $url = substr($url, 0, 245); }
        
        if (in_array($url, $urls_proibidas))
        {
            if ($tamanho==0) $tamanho = strlen($url);
            $nivel++;
            $url = substr($url, 0, $tamanho).$nivel;
        }
        
        $sql = "SELECT "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto "
                . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["url_amigavel"]." = '".$url."' ";
        if ($cod_objeto>0) $sql .= " AND NOT ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $rs = $this->container["db"]->execSQL($sql);
        if ($tamanho==0) $tamanho = strlen($url);
        if ($rs->_numOfRows > 0)
        {
            $nivel++;
            $url = substr($url, 0, $tamanho).$nivel;
            $url = $this->verificarExistenciaUrlAmigavel($url, $cod_objeto, $nivel, $tamanho);
        }
        return $url;
    }

    /**
     * Apaga propriedades de determinado objeto
     * @param int $cod_objeto - Codigo do objeto a remover as propriedades
     * @param bool $tudo - Indica se deve apagar blobs também
     */
    function apagarPropriedades($cod_objeto, $tudo = true)
    {
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["tipodado"]["nick"].".".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["tabela"]." AS tabela "
                . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]."  "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["propriedade"]["nome"]." ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." = ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_classe"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["tipodado"]["nome"]." ".$this->container["config"]->bd["tabelas"]["tipodado"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["propriedade"]["nick"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_tipodado"]." = ".$this->container["config"]->bd["tabelas"]["tipodado"]["nick"].".".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["cod_tipodado"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;

        if (!$tudo)
        {
            $sql .= " AND ".$this->container["config"]->bd["tabelas"]["tipodado"]["nick"].".".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["tabela"]." <> 'tbl_blob'";   
        }

        $res = $this->container["db"]->execSQL($sql);
        $row = $res->GetRows();

        for ($i=0; $i<sizeof($row); $i++)
        {
            if ($row[$i]['tabela']=='tbl_blob')
            {
                if (isset($this->container["config"]->portal["uploadpath"]) && $this->container["config"]->portal["uploadpath"]!="")
                {
                    $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_blob"]." AS cod_blob, "
                            . " ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_blob"]." AS arquivo "
                            . " FROM ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["nome"]." "
                            . " WHERE ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
                    $res_blob = $this->container["db"]->execSQL($sql);
                    $row_blob = $res_blob->GetRows();

                    for ($j=0; $j<sizeof($row_blob); $j++)
                    {
                        $file_ext = Blob::PegaExtensaoArquivo($row_blob[$j]['arquivo']);
                        if (file_exists($this->container["config"]->portal["uploadpath"]."/".Blob::identificaPasta($this->container, $row_blob[$j]['cod_blob'])."/".$row_blob[$j]['cod_blob'].'.'.$file_ext))
                        {
                            $checkDelete = unlink($this->container["config"]->portal["uploadpath"]."/".Blob::identificaPasta($this->container, $row_blob[$j]['cod_blob'])."/".$row_blob[$j]['cod_blob'].'.'.$file_ext);
                        }
                        if (defined ("_THUMBDIR"))
                        {
                            if (file_exists(_THUMBDIR.$row_blob[$j]['cod_blob'].'.'.$file_ext))
                            {
                                unlink(_THUMBDIR.$row_blob[$j]['cod_blob'].'.'.$file_ext);
                            }
                        }
                    }
                }
            }
            
            $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"][$row[$i]['tabela']]["nome"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"][$row[$i]['tabela']]["colunas"]["cod_objeto"]." = ".$cod_objeto;
            $this->container["db"]->execSQL($sql);
        }
    }

    /**
     * Grava propriedades do objeto
     * @global array $_FILES - Array com inputs file do PHP
     * @param int $cod_objeto - Codigo do objeto
     * @param int $cod_classe - Codigo da classe
     * @param array $proplist - Lista de propriedades
     * @param array $array_files - Array com arquivos de upload
     */
    function gravarPropriedades($cod_objeto, $cod_classe, $proplist, $array_files=array())
    {
        if (isset($_FILES) && is_array($_FILES) && count($_FILES)>0)
        {
            $array_files = $_FILES;
            $source = 'post';
        }
        else
        {
            $source = 'string';
        }
        
        // Se tiver array de propriedades
        if (is_array($proplist))
        {
            // percorre o array
            foreach ($proplist as $key => $valor)
            {
                // quebra o nome do campo para saber a propriedade
                $ar_fld = preg_split("[___]", $key);
                if (strpos($ar_fld[1], "^") === false)
                {
//                    x($ar_fld[1]);
                    // se tiver sido inserido valor na propriedade começa os tratamentos e gravação dos dados
                    if ($valor != "")
                    {
                        // pega as informações da propriedade
                        $info = $this->container["adminobjeto"]->pegarInfoPropriedade($cod_classe, $ar_fld[1]);
                        // se for tipo texto avançado, transforma quebras de linhas \n em <br>
                        if ($info['tabela'] == 'tbl_text')
                        {
                            if (!preg_match('%(\<p|\<BR)%is', $valor))
                            {
                                $valor = nl2br($valor);
                            }
                        }
                        // se for tipo data, transforma para o formato que é gravado no banco
                        if ($info['tabela'] == 'tbl_date')
                        {
                            $valor = ConverteData($valor,16);
                        }
                        // se for numero preciso, remove os pontos e transforma virgulas em pontos
                        // formato numérico americano
                        if ($info['tabela'] == 'tbl_float')
                        {
                            $valor = preg_replace("[\.]", "", $valor);
                            $valor = preg_replace("[,]", ".", $valor);
                        }
                        
                        $valor = stripslashes($valor);
                        if ($info['tabela'] != 'tbl_blob')
                        {
                            $sql = "";
                            $bind = array();
                            
                            if ($this->container["config"]->bd["tipo"] == "oracle11")
                            {
                                $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"][$info['tabela']]["nome"]." ("
                                        . " ".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_propriedade"].", "
                                        . " ".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_objeto"].", "
                                        . " ".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["valor"]." "
                                        . " ) VALUES ( "
                                        . " ".$info['cod_propriedade'].", "
                                        . " ".$cod_objeto.", "
                                        . " :valor "
                                        . ")";
                                $bind = array("valor" => $this->container["db"]->slashes($valor));
                            }
                            else
                            {
                                $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"][$info['tabela']]["nome"]." ("
                                        . "".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_propriedade"].", "
                                        . "".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_objeto"].", "
                                        . "".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["valor"].""
                                        . ") VALUES ("
                                        . "".$info['cod_propriedade'].", "
                                        . "".$cod_objeto.", "
                                        . " ? "
//                                        . "".$info['delimitador'].$this->container["db"]->slashes($valor).$info['delimitador'].""
                                        . ")";
                                $bind = array(1 => $this->container["db"]->slashes($valor));
                            }
                            $sql = $this->container["db_con"]->getCon()->prepare($sql);
                            $rs = $this->container["db"]->execSQL(array($sql, $bind));
                        }
                    }
                }
                else
                {
                    $ar_fld = explode("^", $ar_fld[1]);

                    $info = $this->container["adminobjeto"]->pegarInfoPropriedade($cod_classe, $ar_fld[0]);

                    if ($info['tabela'] == "tbl_blob")
                    {
                        $sql = "SELECT "
                                . " ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_blob"]." AS cod_blob, "
                                . " ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                                . " ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_propriedade"]." AS cod_propriedade, "
                                . " ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["arquivo"]." AS arquivo, "
                                . " ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["tamanho"]." AS tamanho "
                                . " FROM ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["nome"]." "
                                . " WHERE ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_propriedade"]." = ".$info["cod_propriedade"]." "
                                . " AND ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
                        $rs = $this->container["db"]->execSQL($sql);
                        while ($row = $rs->FetchRow())
                        {
                            $this->container["blob"]->apagaBlob($row['cod_blob'], $row['arquivo']);
                        }
                    }

                    $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"][$info['tabela']]["nome"]." "
                            . " WHERE ".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_propriedade"]." = ".$info['cod_propriedade']." "
                            . " AND ".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_objeto"]." = ".$cod_objeto;
                    $this->container["db"]->execSQL($sql);  
                }
            }
        }

        // Gravando propriedades blob
        if (is_array($array_files))
        {
            foreach ($array_files as $key => $valor)
            {
                if (isset($valor['size']) && $valor['size'] > 0)
                {
//            xd($array_files);
                    $ar_fld = preg_split("[___]", $key);
                    if (count($ar_fld)>1) { $prop = $ar_fld[1]; }
                    else { $prop = $key; }
                    $info = $this->container["adminobjeto"]->pegarInfoPropriedade($cod_classe, $prop);
                    
                    // Apaga registro, caso já exista
                    $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"][$info['tabela']]["nome"]." "
                            . " WHERE ".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_propriedade"]." = ".$info['cod_propriedade']." "
                            . " AND ".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_objeto"]." = ".$cod_objeto;
                    $this->container["db"]->execSQL($sql);
                    
                    if ($source=='post') $data = fread(fopen($valor['tmp_name'], "rb"), filesize($valor['tmp_name']));
                    else {
                        if (isset($valor['data'])) $data = stripslashes($valor['data']);
                        else $data = fread(fopen($valor['tmp_name'], "rb"), filesize($valor['tmp_name']));
                    }

                    // caso seja gravação do blob no banco
                    if (!isset($this->container["config"]->portal["uploadpath"]) || $this->container["config"]->portal["uploadpath"]=="")
                    {
                        $campo = gzcompress($data);
                        $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"][$info['tabela']]["nome"]." ("
                                . "".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_propriedade"].", "
                                . "".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_objeto"].", "
                                . "".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["valor"].", "
                                . "".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["arquivo"].", "
                                . "".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["tamanho"].""
                                . ") values ("
                                . "".$info['cod_propriedade'].", "
                                . "".$cod_objeto.", "
                                . "".$info['delimitador'].$this->container["db"]->BlobSlashes($data).$info['delimitador'].", "
                                . "'".$valor['name']."', "
                                . "".filesize($valor['tmp_name']).")";
                        $this->container["db"]->execSQL($sql);
                    }
                    // gravação do arquivo em disco
                    else
                    {
                        $campos = array();
                        $campos[$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_propriedade"]] = (int)$info['cod_propriedade'];
                        $campos[$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_objeto"]] = (int)$cod_objeto;
                        $campos[$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["arquivo"]] = strtolower($valor['name']);
                        $campos[$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["tamanho"]] = filesize($valor['tmp_name']);
                        $cod_blob = $this->container["db"]->insert($this->container["config"]->bd["tabelas"][$info['tabela']]["nome"], $campos);
                        
                        // Chama o método de gravação de blob no disco
                        $this->container["blob"]->gravarBlob($valor, $cod_blob);
                    }
                }
            }
        }
    }

    /**
     * Cria objeto
     * @param array $dados - Dados do objeto a ser criado
     * @param bool $log - Indica se deve gerar log
     * @param array $array_files - Lista de arquivos
     * @return int - Codigo do objeto criado
     */
    function criarObjeto($dados, $log = true, $array_files = array())
    {
        $fieldlist = array();
        $valuelist = array();
        $tagslist = array();
        $proplist = array();
        
//        xd($dados);
        
        foreach ($dados as $key=>$value)
        {
            if ($key!="submit")
            {
                if ($key=="tags") $tagslist = preg_split("[,]",$value);
//                if (strpos($key,"___")) $proplist[$key] = htmlspecialchars($value, ENT_QUOTES, "UTF-8");
                if (strpos($key,"___")) $proplist[$key] = $value;
            }
        }
        
        if (strlen($dados['data_publicacao'])<9)
        {
            if (preg_match('|[\.-]|',$dados['data_publicacao']))
            {
                $dados['data_publicacao'].= ' 00:00:00';
            }
            else
            {
                $dados['data_publicacao'].= '000000';
            }
        }
        
        if (strlen($dados['data_validade'])<9)
        {
            if (preg_match('|[\.-]|',$dados['data_validade']))
            {
                $dados['data_validade'].= ' 00:00:00';
            }
            else
            {
                $dados['data_validade'].= '000000';
            }
        }
        $noname = date("Ymd-His"); 
        if ($dados['titulo']=="") $dados['titulo'] = $noname;

        // prepara dados para gravação, prevenção de sql injection
        $campos = array();
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["script_exibir"]] = isset($dados['script_exibir'])?htmlspecialchars($dados['script_exibir'], ENT_QUOTES, "UTF-8"):"";
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pai"]] = (int)htmlspecialchars($dados['cod_pai'], ENT_QUOTES, "UTF-8");
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]] = (int)htmlspecialchars($dados['cod_classe'], ENT_QUOTES, "UTF-8");
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_usuario"]] = isset($dados['cod_usuario'])?(int)htmlspecialchars($dados['cod_usuario'], ENT_QUOTES, "UTF-8"):$_SESSION['usuario']['cod_usuario'];
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pele"]] = isset($dados['cod_pele'])?(int)htmlspecialchars($dados['cod_pele'], ENT_QUOTES, "UTF-8"):"";
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_status"]] = (int)htmlspecialchars($dados['cod_status'], ENT_QUOTES, "UTF-8");
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]] = htmlspecialchars($dados['titulo'], ENT_QUOTES, "UTF-8");
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["descricao"]] = htmlspecialchars($dados['descricao'], ENT_QUOTES, "UTF-8");
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_publicacao"]] = ConverteData(htmlspecialchars($dados['data_publicacao'], ENT_QUOTES, "UTF-8"), 27);
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_validade"]] = ConverteData(htmlspecialchars($dados['data_validade'], ENT_QUOTES, "UTF-8"), 27);
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["peso"]] = (int)htmlspecialchars($dados['peso'], ENT_QUOTES, "UTF-8");
//        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["script_exibir"]'script_exibir'] = htmlspecialchars($dados['script_exibir'], ENT_QUOTES, "UTF-8");
        if ($dados['url_amigavel']=="") $dados['url_amigavel'] = limpaString($campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]]);
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["url_amigavel"]] = $this->verificarExistenciaUrlAmigavel($dados['url_amigavel']);
        
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["apagado"]] = 0;
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["objetosistema"]] = 0;
        $campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["versao"]] = 1;
        
        if ($campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pele"]]==0) { unset($campos[$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pele"]]); }
        
// xd($this->container["config"]->bd);

        $cod_objeto = $this->container["db"]->insert($this->container["config"]->bd["tabelas"]["objeto"]["nome"], $campos);
        
        
        // grava as propriedades do objeto
        $this->gravarPropriedades($cod_objeto, $dados['cod_classe'], $proplist, $array_files);
//        xd($proplist);
        // grava as relações de parentesco do objeto
        $this->criarParentesco($cod_objeto, $dados['cod_pai']);
        // grava as tags
        $this->gravarTags($cod_objeto, $tagslist);
        
        // grava o log
        if ($log) $this->container["log"]->incluirLogObjeto($cod_objeto, _OPERACAO_OBJETO_CRIAR);
        
        return $cod_objeto;
    }
    
    public function pegarIp() {
        // Check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->validarIp($_SERVER['HTTP_CLIENT_IP']))
        {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // Check for IPs passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {
        // Check if multiple IP addresses exist in var
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) 
            {
                if ($this->validarIp($ip))
                {
                    return $ip;
                }
            }
        }
        
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->validarIp($_SERVER['HTTP_X_FORWARDED']))
        {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->validarIp($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->validarIp($_SERVER['HTTP_FORWARDED_FOR']))
        {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED']) && $this->validarIp($_SERVER['HTTP_FORWARDED']))
        {
            return $_SERVER['HTTP_FORWARDED'];
        }

        // Return unreliable IP address since all else failed
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Ensures an IP address is both a valid IP address and does not fall within
     * a private network range.
     *
     * @access public
     * @param string $ip
     */
    public function validarIp($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, 
                            FILTER_FLAG_IPV4 | 
                            FILTER_FLAG_IPV6 |
                            FILTER_FLAG_NO_PRIV_RANGE | 
                            FILTER_FLAG_NO_RES_RANGE) === false)
        {
            return false;
        }
        self::$ip = $ip;
        return true;
    }
    
    function gravarVersao($cod_objeto)
    {
        $obj = new Objeto($this->container, $cod_objeto);
        $obj->pegarListaPropriedades();
        $classe = $this->pegarInfoDaClasse($obj->valor("cod_classe"));
        $versao = $obj->valor("versao");
        if (isset($classe["todas"])) unset($classe["todas"]);
        if (isset($classe["obj_conta"])) unset($classe["obj_conta"]);
        if (isset($classe["objetos"])) unset($classe["objetos"]);
        if (isset($obj->page)) unset($obj->page);
        if (isset($obj->ponteiro)) unset($obj->ponteiro);
        if (isset($obj->quantidade)) unset($obj->quantidade);
        //        unset($obj->ArrayMetadados);
        $obj->classe = $classe;
        $arr_obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_QUOT);
        $ip = $this->pegarIp();
        
        $sql = "";
        $bind = array();

        
        
        if ($this->container["config"]->bd["tipo"] == "oracle11")
        {
            $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["nome"]." ("
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_objeto"].", "
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["versao"].", "
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["conteudo"].", "
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["data_criacao"].", "
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_usuario"].", "
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["ip"].""
                . ") VALUES ("
                . " :cod_objeto, "
                . " :versao, "
                . " :conteudo, "
                . " TO_TIMESTAMP(:data, 'YYYY-MM-DD HH24:MI:SS'), "
                . " :cod_usuario, "
                . " :ip )";
            $bind = array("cod_objeto" => $cod_objeto,
                "versao" => $versao,
                "conteudo" => $arr_obj, 
                "data" => date("Y-m-d H:i:s"),
                "cod_usuario" => $_SESSION["usuario"]["cod_usuario"],
                "ip" => $ip);
        }
        else
        {
            $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["nome"]." ("
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_objeto"].", "
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["versao"].", "
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["conteudo"].", "
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["data_criacao"].", "
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_usuario"].", "
                . "".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["ip"].""
                . ") VALUES ("
                . " ?, "
                . " ?, "
                . " ?, "
                . " ?, "
                . " ?, "
                . " ? )";
            $bind = array(1 => $cod_objeto,
                2 => $versao,
                3 => $arr_obj, 
                4 => date("Y-m-d H:i:s"),
                5 => $_SESSION["usuario"]["cod_usuario"],
                6 => $ip);
        }
        
        $sql = $this->container["db_con"]->getCon()->prepare($sql);
        $rs = $this->container["db"]->execSQL(array($sql, $bind));
        // xd("aqui");

        $this->container["log"]->registrarLogWorkflow("Criada versão ".$versao, $cod_objeto, 1);
    }
    
    function cacheFlush()
    {
        GLOBAL $ADODB_CACHE_DIR;
        
        if ($this->container["config"]->bd["cache"] === true) 
        {
            if (defined("_DBCACHEPATH")) $ADODB_CACHE_DIR = _DBCACHEPATH;
            $this->container["db_con"]->getCon()->CacheFlush();
        }
    }

    /**
     * Grava tags do objeto no banco de dados
     * @param int $cod_objeto - Codigo do objeto
     * @param array $tagslist - Lista de tags
     */
    function gravarTags($cod_objeto, $tagslist)
    {
        if (is_array($tagslist) && count($tagslist)>=1)
        {
            $this->apagarTags($cod_objeto);

            foreach ($tagslist as $tag)
            {
                $tag = trim($tag);
                $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["tag"]["colunas"]["cod_tag"]." AS cod_tag "
                        . " FROM ".$this->container["config"]->bd["tabelas"]["tag"]["nome"]." "
                        . " WHERE ".$this->container["config"]->bd["tabelas"]["tag"]["colunas"]["nome_tag"]." = '".$tag."'";
                $rs = $this->container["db"]->execSQL($sql);
                if ($rs->_numOfRows == 0)
                {
                    $cod_tag = $this->container["db"]->insert($this->container["config"]->bd["tabelas"]["tag"]["nome"], array($this->container["config"]->bd["tabelas"]["tag"]["colunas"]["nome_tag"] => $tag));
                }
                else
                {
                    $row = $rs->FetchRow();
                    $cod_tag = $row["cod_tag"];
                }

                $sql = "INSERT INTO "
                        . " ".$this->container["config"]->bd["tabelas"]["tagxobjeto"]["nome"]." ("
                        . "".$this->container["config"]->bd["tabelas"]["tagxobjeto"]["colunas"]["cod_tag"].", "
                        . "".$this->container["config"]->bd["tabelas"]["tagxobjeto"]["colunas"]["cod_objeto"].""
                        . ") VALUES ("
                        . "".$cod_tag.", "
                        . "".$cod_objeto.")";
                $rs = $this->container["db"]->execSQL($sql);
            }
        }
    }

    /**
     * Remove tags do objeto e do banco caso não tenha nenhum 
     * outro objeto utilizando
     * @param int $cod_objeto - Codigo do objeto
     */
    function apagarTags($cod_objeto)
    {
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["tagxobjeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["tagxobjeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $rs = $this->container["db"]->execSQL($sql);

        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["tag"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["tag"]["colunas"]["cod_tag"]." NOT IN (SELECT ".$this->container["config"]->bd["tabelas"]["tagxobjeto"]["colunas"]["cod_tag"]." FROM ".$this->container["config"]->bd["tabelas"]["tagxobjeto"]["nome"].")";
        $rs = $this->container["db"]->execSQL($sql);
    }
    
    /**
     * Remove pele do banco de dados e desativa de objetos
     * @param int $cod_pele - Codigo da pele
     */
    function apagarPele($cod_pele)
    {
        $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                . "SET ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pele"]." = null "
                . "WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pele"]." = ".$cod_pele;
	$this->container["db"]->execSQL($sql);
	
	$sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["pele"]["nome"]." "
                . "WHERE ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["cod_pele"]." = ".$cod_pele;
	$this->container["db"]->execSQL($sql);
	
	return 0;
    }
    
    /**
     * Atualiza informações da pele
     * @param int $cod_pele
     * @param string $nome
     * @param string $prefixo
     * @param int $publica
     */
    function atualizarPele($cod_pele, $nome, $prefixo, $publica)
    {
        $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["pele"]["nome"]." "
                . " SET ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["nome"]." = '".$nome."', "
                . " ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["prefixo"]." = '".$prefixo."', "
                . " ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["publica"]." = ".$publica." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["cod_pele"]." = ".$cod_pele;
	$this->container["db"]->execSQL($sql);
    }
    
     /**
     * Cria pele
     * @param string $nome
     * @param string $prefixo
     * @param int $publica
     */
    function criarPele($nome, $prefixo, $publica)
    {
        $campos=array();
        $campos[$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["nome"]] = $nome;
        $campos[$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["prefixo"]] = $prefixo;
        $campos[$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["publica"]] = $publica;
                
        return($this->container["db"]->insert($this->container["config"]->bd["tabelas"]["pele"]["nome"], $campos));
    }

    /**
     * Apaga lista de relação de parentesco de objeto
     * @param int $cod_objeto - Codigo do objeto
     */
    function apagarParentesco($cod_objeto)
    {
        $this->container["db"]->execSQL("DELETE FROM ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." = ".$cod_objeto);
    }

    /**
     * Cria relação de parentesco entre objetos
     * @param int $cod_objeto - Codigo do objeto
     * @param int $cod_pai - Codigo do objeto pai
     */
    function criarParentesco($cod_objeto, $cod_pai)
    {
        // duplica parentesco do objeto pai, incrementando o nível
        $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." ("
                . "".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"].", "
                . "".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"].", "
                . "".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["ordem"].") "
                . " SELECT ".$cod_objeto.", "
                . "".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"].", "
                . "".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["ordem"]."+1 "
                . "FROM ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." "
                . "WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." = ".$cod_pai;
        $this->container["db"]->execSQL($sql);
        
        // cria parentesco entre objeto e o pai
        $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." ("
                . " ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"].", "
                . " ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"].", "
                . " ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["ordem"].""
                . ") "
                . "values (".$cod_objeto.", ".$cod_pai.", 1)";
        $this->container["db"]->execSQL($sql);
    }

    /**
     * Apaga objeto, fisicamente ou logicamente
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     * @param bool $definitivo - indica se deve apagar realmente, ou mandar para lixeira
     */
    function apagarObjeto($cod_objeto, $definitivo = false)
    {
        $tblObjeto = $this->container["config"]->bd["tabelas"]["objeto"];
        $tblParentesco = $this->container["config"]->bd["tabelas"]["parentesco"];

        if (!$definitivo)
        {
            $sql = "SELECT distinct ".$tblParentesco["nick"].".".$tblParentesco["colunas"]["cod_objeto"]." AS cod_objeto, "
                    . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_status"]." AS cod_status "
                    . " FROM ".$tblParentesco["nome"]." ".$tblParentesco["nick"]." "
                    . " INNER JOIN ".$tblObjeto["nome"]." ".$tblObjeto["nick"]." "
                        . " ON ".$tblParentesco["nick"].".".$tblParentesco["colunas"]["cod_objeto"]." = ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_objeto"]." "
                    . " WHERE (".$tblParentesco["nick"].".".$tblParentesco["colunas"]["cod_pai"]." = ".$cod_objeto." "
                    . " OR ".$tblParentesco["nick"].".".$tblParentesco["colunas"]["cod_objeto"]." = ".$cod_objeto.") "
                    . " AND (".$tblObjeto["nick"].".".$tblObjeto["colunas"]["apagado"]." = 0"
                        . " OR ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["apagado"]." is null) ";
            $res = $this->container["db"]->execSQL($sql);

            while ($row = $res->FetchRow())
            {
                if ($row['cod_status'] == _STATUS_SUBMETIDO)
                {
                    $this->removerPendencia("Removida pendência de publicação por remoção do objeto", $row['cod_objeto']);
                }
                
                $sql = "UPDATE ".$tblObjeto["nome"]." "
                        . " SET ".$tblObjeto["colunas"]["apagado"]." = 1, "
                        . " ".$tblObjeto["colunas"]["data_exclusao"]." = ".date("YmdHis")." "
                        . " WHERE ".$tblObjeto["colunas"]["cod_objeto"]." = ".$row['cod_objeto'];
                $this->container["db"]->execSQL($sql);
                
                $this->container["log"]->incluirLogObjeto($cod_objeto, _OPERACAO_OBJETO_REMOVER);
            
                if ($row["cod_objeto"] != $cod_objeto)
                {
                    $this->apagarObjeto($row["cod_objeto"], false);
                }
            }
        }
        else
        {
            $this->apagarEmDefinitivo($cod_objeto);
        }

        $this->cacheFlush();
    }
    

    /**
     * Verifica se usuário é dono do objeto
     * @param int $cod_usuario - Codigo do usuario
     * @param int $cod_objeto - Codigo do objeto
     * @return boolean
     */
    function usuarioEDono($cod_usuario, $cod_objeto)
    {
        $sql = "select cod_objeto from objeto where cod_objeto=$cod_objeto and cod_usuario=$cod_usuario";
        $rs = $this->container["db"]->execSQL($sql);
        if ($rs->_numOfRows > 0) return true;
        else return false;
    }

    /**
     * Rejeita publicação do objeto
     * @param string $mensagem - Mensagem de rejeição
     * @param int $cod_objeto - Codigo do objeto
     */
    function rejeitarObjeto($mensagem, $cod_objeto)
    {
        if (($_SESSION['usuario']['perfil']==_PERFIL_ADMINISTRADOR) || ($_SESSION['usuario']['perfil']==_PERFIL_EDITOR)
                || (($_SESSION['usuario']['perfil']==_PERFIL_AUTOR) && $this->usuarioEdono($_SESSION['usuario']['cod_usuario'], $cod_objeto)))
        {
            $this->trocarStatusObjeto($mensagem, $cod_objeto, _STATUS_REJEITADO);
            $this->container["db"]->execSQL("DELETE FROM ".$this->container["config"]->bd["tabelas"]["pendencia"]["nome"]." "
                            . " WHERE ".$this->container["config"]->bd["tabelas"]["pendencia"]["colunas"]["cod_objeto"]." = ".$cod_objeto);
        }
    }

    /**
     * Publica objeto
     * @param string $mensagem - mensagem de publicação
     * @param int $cod_objeto - Codigo do objeto
     */
    function publicarObjeto($mensagem, $cod_objeto)
    {			
        if ($_SESSION['usuario']['perfil'] <= _PERFIL_EDITOR)
        {
//            xd($mensagem);
            $this->trocarStatusObjeto($mensagem, $cod_objeto, _STATUS_PUBLICADO);
            $this->container["db"]->execSQL("DELETE FROM ".$this->container["config"]->bd["tabelas"]["pendencia"]["nome"]." "
                            . " WHERE ".$this->container["config"]->bd["tabelas"]["pendencia"]["colunas"]["cod_objeto"]." = ".$cod_objeto);

            if (defined("_avisoPublicacao") && _avisoPublicacao==true)
            {
                $objetoPublicado = new Objeto($this->container, $cod_objeto);
                $array_objeto = null;
                $array_objeto[] = array($objetoPublicado->metadados["cod_objeto"], $objetoPublicado->metadados["titulo"]);
                $caminhoObjeto = $this->container["adminobjeto"]->pegarParentescoCompleto($cod_objeto, 100, array(0), array(), false);
                foreach ($caminhoObjeto as $codigo=>$titulo) 
                {
                    $array_objeto[] = array($codigo, $titulo[0]);
                }

                $mensagemEmail = "<html><head><title>Objeto Publicado</title></head>
                <body>
                Objeto publicado no site: <b>".$this->container["config"]->portal["nome"]."</b><br>
                Data: ".date("d/m/Y H:i:s")."<br>
                Objeto: <a href=\"".$this->container["config"]->portal["url"]."/index.php/content/view/".$array_objeto[0][0].".html\" target=\"_blank\">".$array_objeto[0][1]."</a><br><br>
                Caminho do objeto: <br>";

                for ($i=1; $i<sizeof($array_objeto); $i++) {
                    $mensagemEmail .= $i." - <a href=\"".$this->container["config"]->portal["url"]."/index.php/content/view/".$array_objeto[$i][0].".html\" target=\"_blank\">".$array_objeto[$i][1]."</a><br>";
                }

                $mensagemEmail .= "<br><small>Mensagem gerada automaticamente. Nao responda.</small>
                </body></html>";

                $destinatario = _emailAvisoPublicacao;
                $remetente =  _remetenteAvisoPublicacao;
                $assunto = "Objeto publicado no site: ".$this->container["config"]->portal["nome"];
                $wassent = EnviarEmail($remetente, $destinatario, $assunto, $mensagemEmail); 
            }
        }
    }

    /**
     * Despublica objeto
     * @param string $mensagem - Mensagem de despublicação
     * @param int $cod_objeto - Codigo do objeto
     */
    function despublicarObjeto($mensagem, $cod_objeto)
    {			
        if (($_SESSION['usuario']['perfil']==_PERFIL_ADMINISTRADOR) || ($_SESSION['usuario']['perfil']==_PERFIL_EDITOR))
        {
            $this->trocarStatusObjeto($mensagem, $cod_objeto, _STATUS_PRIVADO);
        }
    }

    /**
     * Envia objeto para publicação, solicita publicação do objeto
     * @param string $mensagem - mensagem de solicitação de publicação
     * @param int $cod_objeto - Codigo do objeto
     */
    function submeterObjeto($mensagem, $cod_objeto)
    {
        $dadosObjeto = $this->container["adminobjeto"]->pegarDadosObjetoId($cod_objeto);

        if ((($_SESSION['usuario']['perfil']==_PERFIL_AUTOR) || ($this->usuarioEdono($_SESSION['usuario']['cod_usuario'],$cod_objeto))) && ($dadosObjeto['cod_status'] == _STATUS_PRIVADO))
        {
            $this->trocarStatusObjeto($mensagem, $cod_objeto, _STATUS_SUBMETIDO);

            $sql = "select ".$_SESSION["usuario"]["chefia"]." as cod_usuario,".$cod_objeto." as cod_objeto from usuarioxobjetoxperfil inner join parentesco on (usuarioxobjetoxperfil.cod_objeto=parentesco.cod_pai or usuarioxobjetoxperfil.cod_objeto=parentesco.cod_objeto) where parentesco.cod_objeto=".$cod_objeto." group by cod_usuario, cod_usuario";
            $rs = $this->container["db"]->execSQL($sql, 1, 1);
            $campos = $rs->fields;

            $sql = "select * from pendencia where cod_usuario = ".$campos['cod_usuario']." and cod_objeto = ".$campos['cod_objeto'];
            $rs = $this->container["db"]->execSQL($sql);

            if (!$rs->GetRows())
            {
                $sql = "insert into pendencia(cod_usuario, cod_objeto) values (".$campos['cod_usuario'].", ".$campos['cod_objeto'].")";
                $this->container["db"]->execSQL($sql);
            }

            $enviarEmailSolicitacao = $this->container["adminobjeto"]->enviarEmailSolicitacao($_SESSION['usuario']['chefia'], $cod_objeto, $mensagem);
        }
    }

    /**
     * Remove solicitação de publicação
     * @param string $mensagem - mensagem de remoção da pendencia
     * @param int $cod_objeto - Codigo do objeto
     */
    function removerPendencia($mensagem, $cod_objeto)
    {
        $this->trocarStatusObjeto($mensagem, $cod_objeto, _STATUS_PRIVADO);
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["pendencia"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["pendencia"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $this->container["db"]->execSQL($sql);
    }

    /**
     * Troca status do objeto
     * @param string $mensagem - Mensagem da troca de status
     * @param int $cod_objeto - Codigo do objeto
     * @param int $cod_status - Codigo do novo status
     */
    function trocarStatusObjeto($mensagem, $cod_objeto, $cod_status)
    {
        if ($cod_objeto != $this->container["config"]->portal["objroot"])
        {
            $this->container["db"]->execSQL("UPDATE ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                    . " SET ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_status"]." = ".$cod_status." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto);
            $this->container["log"]->registrarLogWorkflow($mensagem, $cod_objeto, $cod_status);
            $this->cacheFlush();
        }
    }

    /**
     * Define status para criação do objeto conforme perfil do usuário
     * @return int - Codigo do status
     */
    function pegarStatusNovoObjeto()
    {
        if ($_SESSION['usuario']['perfil']==_PERFIL_AUTOR)
        {
            $status=_STATUS_PRIVADO;
        }
        else
        {
            $status = _STATUS_PUBLICADO;
        }

        return $status;
    }
    
    function pegarVersao($cod_versaoobjeto)
    {
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_versaoobjeto"]." AS cod_versaoobjeto, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["versao"]." AS versao, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["data_criacao"]." AS data_criacao, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["conteudo"]." AS conteudo, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["ip"]." AS ip "
                . " FROM ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_versaoobjeto"]." = ".$cod_versaoobjeto." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["versao"]." ";
        $res = $this->container["db"]->execSQL($sql);
        return $res->GetRows();
    }

    /**
     * Busca versões do objeto no banco
     * @param int $cod_objeto
     * @return array
     */
    function pegarVersoes($cod_objeto)
    {
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_versaoobjeto"]." AS cod_versaoobjeto, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["versao"]." AS versao, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["data_criacao"]." AS data_criacao, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                . " ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["ip"]." AS ip "
                . " FROM ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["versaoobjeto"]["colunas"]["versao"]." ";
        $res = $this->container["db"]->execSQL($sql);
        return $res->GetRows();
    }
    
    /**
     * Cria cópia de determinado objeto
     * @param int $cod_objeto - codigo do objeto a ser copiado
     * @param int $cod_pai - Codigo do objeto pai onde sera criado novo objeto
     */
    function copiarObjeto($cod_objeto, $cod_pai)
    {
        $this->duplicarObjeto($cod_objeto, $cod_pai);
        $this->removerObjetoPilha($cod_objeto);
    }

    /**
     * Move determinado objeto
     * @param int $cod_objeto - Codigo do objeto a ser movido
     * @param int $cod_pai - Codigo do objeto pai onde ficara objeto movido
     */
    function moverObjeto($cod_objeto, $cod_pai)
    {
        if ($cod_objeto == -1)
        {
                $cod_objeto = $this->pegarPrimeiroPilha();
        }
        $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                . " SET ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pai"]." = ".$cod_pai." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $this->container["db"]->execSQL($sql);

        $this->apagarParentesco($cod_objeto);
        $this->criarParentesco($cod_objeto, $cod_pai);
        
        $this->container["log"]->registrarLogWorkflow("Objeto movido para ".$cod_pai, $cod_objeto, _OPERACAO_OBJETO_MOVER);

        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pai"]." AS cod_pai, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto "
                . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." "
                . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto." "
                . " GROUP BY ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"].", "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pai"];
        $res = $this->container["db"]->execSQL($sql);
        $row = $res->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
                $this->apagarParentesco($row[$i]['cod_objeto']);
                $this->criarParentesco($row[$i]['cod_objeto'], $row[$i]['cod_pai']);
        }

        $this->removerObjetoPilha($cod_objeto);
    }

    /**
     * Cola objeto da pilha como link
     * @param int $cod_objeto - codigo do objeto a ser colado como link
     * @param int $cod_pai - codigo do objeto que será pai do link
     */
    function colarComoLink($cod_objeto, $cod_pai)
    {
        if ($cod_objeto == -1)
        {
            $cod_objeto = $this->pegarPrimeiroPilha();
        }

        $orig_obj = $this->container["adminobjeto"]->criarObjeto($cod_objeto);
        $dados = $orig_obj->metadados;

        $status = $this->pegarStatusNovoObjeto();
        
        $cod_classe_interlink = $this->codigoClasse("interlink");

        $campos=array();
        $campos['cod_pai'] = $cod_pai;
        $campos['cod_classe'] = $cod_classe_interlink;
        $campos['cod_usuario'] = $dados['cod_usuario'];
        $campos['cod_status'] = $dados['cod_status'];
        $campos['titulo'] = $this->container["db"]->slashes($dados['titulo']);
        $campos['descricao'] = $this->container["db"]->slashes($dados['descricao']);
        $campos['data_publicacao'] = ConverteData($dados['data_publicacao'],27);
        $campos['data_validade'] = ConverteData($dados['data_validade'],27);

        $novo_cod_objeto = $this->container["db"]->insert('objeto',$campos);		
//        xd($novo_cod_objeto);

        $this->gravarPropriedades($novo_cod_objeto, $cod_classe_interlink, array('property___link'=>$cod_objeto));
        $this->removerObjetoPilha($cod_objeto);
        $this->criarParentesco($novo_cod_objeto, $cod_pai);
    }

    /**
     * Pega primeiro objeto da pilha
     * @return int - codigo do primeiro objeto da pilha
     */
    function pegarPrimeiroPilha()
    {
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_objeto"]." AS cod_objeto "
                . " FROM ".$this->container["config"]->bd["tabelas"]["pilha"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_usuario"]." = ".$_SESSION['usuario']['cod_usuario'];
        $rs = $this->container["db"]->execSQL($sql);
        $row = $rs->fields;
        
        return $row['cod_objeto'];
    }

    /**
     * Duplica objeto e seus filhos
     * @param int $cod_objeto - Codigo do objeto a duplicar
     * @param int $cod_pai - Codigo do objeto pai, onde ficara novo objeto
     * @return int - Codigo do novo objeto
     */
    function duplicarObjeto($cod_objeto, $cod_pai=-1)
    {
        if ($cod_objeto == -1)
        {
            $cod_objeto = $this->pegarPrimeiroPilha();
        }

        $orig_obj = $this->container["adminobjeto"]->criarObjeto($cod_objeto);
        $dados = $orig_obj->metadados;
        
        if ($cod_pai==-1) $cod_pai = $dados['cod_pai'];

        $campos = array();
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["script_exibir"]] = $dados['script_exibir'];
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["cod_pai"]] = $cod_pai;
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["cod_classe"]] = $dados['cod_classe'];
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["cod_usuario"]] = $dados['cod_usuario'];
        if (!is_null($dados['cod_pele'])) $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["cod_pele"]] = $dados['cod_pele'];
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["cod_status"]] = $dados['cod_status'];
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["titulo"]] = $this->container["db"]->slashes($dados['titulo']);
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["descricao"]] = $this->container["db"]->slashes($dados['descricao']);
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["data_publicacao"]] = ConverteData($dados['data_publicacao'],27);
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["data_validade"]] = ConverteData($dados['data_validade'],27);
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["url_amigavel"]] = $this->verificarExistenciaUrlAmigavel($dados['url_amigavel']);
        $campos[$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["peso"]] = $dados['peso'];

        $cod_objeto = $this->container["db"]->insert($this->container["config"]->bd["tabelas"]['objeto']["nome"], $campos);	
        $this->duplicarPropriedades($cod_objeto, $orig_obj);
        $this->criarParentesco($cod_objeto, $cod_pai);

        if ($orig_obj->pegarListaFilhos())
        {
            while ($childobj = $orig_obj->pegarProximoFilho())
            {
                $this->duplicarObjeto($childobj->valor("cod_objeto"), $cod_objeto);
            }
        }

        $this->container["log"]->incluirLogObjeto($cod_objeto, _OPERACAO_OBJETO_CRIAR);
        
        $this->cacheFlush();
        
        return $cod_objeto;
    }

    /**
     * Duplica propriedades de determinado objeto em outro objeto
     * @param int $destino - codigo do objeto que recebera as propriedades
     * @param int $origem - codigo do objeto que tera proprieades duplicadas
     */
    function duplicarPropriedades($destino, $origem)
    {
        $propriedades = $origem->pegarListaPropriedades();
        $lista = array();
        foreach ($propriedades as $nome => $valor)
        {
            if ($valor["tipo"]=="tbl_objref" && isset($valor["referencia"]))
            {
                $lista['property___'.$nome] = $valor['referencia'];
            }
            else
            {
                // adicionado para duplicar os blobs no caso de copias
                if ($valor["tipo"] == "tbl_blob" && isset($valor["cod_blob"]))
                {
                    $this->codigo_temp_blob = $valor['cod_blob'];
                    $this->tipo_temp_blob = $valor['tipo_blob'];
                    $this->tamanho_temp_blob = $valor['tamanho_blob'];
                }
                $lista["property___".$nome] = $valor["valor"];
            }
        }
        $this->gravarPropriedades($destino, $origem->valor("cod_classe"), $lista);
    }

    /**
     * Busca lista de classes que podem ser criadas abaixo de determinada classe
     * @param int $cod_classe - Codigo da classe a ser verificada
     * @return array - Lista de classes que podem ser criadas
     */
    function listarClassesPermitidas($cod_classe)
    {
        $out=array();
        $sql = "SELECT "
                . " ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["nick"].".".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe_filho"]." AS cod_classe, "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"]." AS nome, "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["descricao"]." AS descricao, "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["prefixo"]." AS prefixo "
                . " FROM ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["nome"]." ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["nick"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." ".$this->container["config"]->bd["tabelas"]["classe"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." = ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["nick"].".".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe_filho"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["nick"].".".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe"]." = ".$cod_classe." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"];
        $res = $this->container["db"]->execSQL($sql);
        return $res->GetRows();
    }
    
    /**
     * Verifica quais classes podem ser criadas abaixo de determinado objeto
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Lista de classes que podem ser criadas
     */
    function listarClassesPermitidasObjeto($cod_objeto)
    {
        $out=array();
        $sql = "SELECT "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." AS cod_classe, "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"]." AS nome, "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["descricao"]." AS descricao, "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["prefixo"]." AS prefixo "
                . " FROM ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["nick"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." ".$this->container["config"]->bd["tabelas"]["classe"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." = ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["classexobjeto"]["colunas"]["cod_classe"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["classexobjeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"];
        $rs = $this->container["db"]->execSQL($sql);
        return $rs->GetRows();
    }

    /**
     * Envia objeto para pilha do usuario
     * @param int $cod_objeto - Codigo do objeto para ir para pilha
     */
    function copiarObjetoParaPilha($cod_objeto)
    {
        $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["pilha"]["nome"]." "
                . " (".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_objeto"].", ".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_usuario"].") "
                . " VALUES (".$cod_objeto.", ".$_SESSION['usuario']['cod_usuario'].")";
        $this->container["db"]->execSQL($sql);
    }

    /**
     * Remove objeto da pilha do usuario
     * @param int $cod_objeto - Codigo do objeto que deve sair da pilha
     */
    function removerObjetoPilha($cod_objeto, $user=1)
    {
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["pilha"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        if ($user == 1)
        {
            $sql .= " AND ".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_usuario"]." = ".$_SESSION['usuario']['cod_usuario'];
        }
        $this->container["db"]->execSQL($sql);
    }

    /**
     * Limpa pilha do usuário
     */
    function limparPilha()
    {
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["pilha"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_usuario"]." =" .$_SESSION['usuario']['cod_usuario'];
        $this->container["db"]->execSQL($sql);
    }

    /**
     * Pega pilha do usuario logado
     * @return array - lista de objetos na pilha
     */
    function pegarPilha()
    {
        $result=array();
        $this->ContadorPilha=0;
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["pilha"]["nome"].".".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_objeto"]." AS codigo, "
                . ""
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." AS texto "
                . " FROM ".$this->container["config"]->bd["tabelas"]["pilha"]["nome"]." "
                . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["pilha"]["nome"].".".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_objeto"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["pilha"]["nome"].".".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_usuario"]." = ".$_SESSION['usuario']['cod_usuario'];
        $rs = $this->container["db"]->execSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $this->ContadorPilha++;
            $result[]=$row[$i];
        }
        return $result;
    }

    /**
     * Verifica se usuario tem objetos na pilha
     * @return int - Numero de objetos na pilha
     */
    function temPilha()
    {
        if (!$this->ContadorPilha)
        {
            $sql = "SELECT COUNT(*) AS contador "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["pilha"]["nome"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["pilha"]["colunas"]["cod_usuario"]."=".$_SESSION['usuario']['cod_usuario'];
            $rs = $this->container["db"]->execSQL($sql);
            $this->ContadorPilha = $rs->fields["contador"];
        }
        return $this->ContadorPilha;
    }

    /**
     * Busca objetos da pilha e envia resultado para metodo que monta dropdown
     * @param int $selecionado - codigo do parametro que deve vir selecionado no <select>
     * @param bool $branco - indica se deve ter um <option> com value em branco
     * @return string - Lista de <option> para o <select>
     */
    function dropdownPilha($selecionado='', $branco=false)
    {
        $lista = $this->pegarPilha();
        return $this->criarDropDown($lista, $selecionado, $branco);
    }
    
    /**
     * Monta relacionamento entre classes e objetos, identificando os objetos onde a classe pode ser criada
     * @param int $cod_classe - Codigo da classe que será relacionada
     * @param array $relacao - Lista de códigos de objetos
     * @param array $relacaourl - Lista de urls amigáveis de objetos
     */
    function montarRelacionamentoClasseObjeto($cod_classe, $relacao, $relacaourl)
    {
        // Atualiza lista de objetos onde pode ser criada
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["colunas"]["cod_classe"]." = " . $cod_classe;
        $this->container["db"]->execSQL($sql);
        
        if (is_array($relacao) && count($relacao) > 0)
        {
            $objs = array();
            for ($i=0; $i<count($relacao); $i++)
            {
                $cod = (int)htmlspecialchars($relacao[$i], ENT_QUOTES, "UTF-8");
                $url = htmlspecialchars($relacaourl[$i], ENT_QUOTES, "UTF-8");
                if (substr($url, 0, 1) == "/") $url = substr($url, 1);
                $url = limpaString($url);
                
                if ($cod > 0)
                {
                    $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." as cod_objeto "
                            . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                            . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod;
                    $rs = $this->container["db"]->execSQL($sql);
                    if ($rs->_numOfRows > 0)
                    {
                        $objs[] = $cod;
                    }
                }
                else
                {
                    if ($url != "")
                    {
                        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." as cod_objeto "
                            . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                            . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["url_amigavel"]." = '".$url."'";
                        $rs = $this->container["db"]->execSQL($sql);
                        if ($rs->_numOfRows > 0)
                        {
                            while ($row = $rs->FetchRow())
                            {
                                $objs[] = (int)$row["cod_objeto"];
                            }
                        }
                    }
                }
            }
            foreach ($objs as $obj)
            {
                $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["nome"]." "
                        . " (".$this->container["config"]->bd["tabelas"]["classexobjeto"]["colunas"]["cod_classe"].", "
                        . " ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["colunas"]["cod_objeto"].") "
                        . " VALUES (".$cod_classe.", "
                        . " ".$obj.")";
                $this->container["db"]->execSQL($sql);
            }
        }
    }
    
    /**
     * Apaga e refaz relacionamento entre classes
     * @param int $cod_classe - Codigo da classe principal que esta tendo o relacionamento remontado
     * @param array $relacao - Codigos das classes que compoem o relacionamento
     * @param int $tipo - Informa tipo de relação: 1=contem, 2=está contido
     */
    function montarRelacionamentoClasses($cod_classe, $relacao, $tipo)
    {
        // Apagando relação existente
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["nome"]." "
                . " WHERE ";
        if ($tipo == "1") $sql .= " ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe"]." = " . $cod_classe;
        else $sql .= " ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe_filho"]." = " . $cod_classe;
        $this->container["db"]->execSQL($sql);
        
        if (is_array($relacao) && count($relacao) > 0)
        {
            foreach ($relacao as $rel)
            {
                $cod = (int)htmlspecialchars($rel, ENT_QUOTES, "UTF-8");

                $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["nome"]." "
                        . " (".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe"].", "
                        . " ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe_filho"].") ";
                if ($tipo=="1") $sql .= "VALUES (" . $cod_classe . ", " . $cod . ")";
                else $sql .= "VALUES (" . $cod . ", " . $cod_classe . ")";
                $this->container["db"]->execSQL($sql);
            }
        }
    }

    /**
     * Busca informações de determinada classe
     * @param int $cod_classe - Codigo da classe
     * @return array - lista com informações da classe
     */
    function pegarInfoDaClasse($cod_classe)
    {
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." AS cod_classe,  "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"]." AS nome,  "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["prefixo"]." AS prefixo,  "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["descricao"]." AS descricao,  "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["temfilhos"]." AS temfilhos,  "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["sistema"]." AS sistema,  "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["indexar"]." AS indexar "
                . " FROM ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." = ".$cod_classe." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"];
//        xd($sql);
        $rs = $this->container["db"]->execSQL($sql);
        $result['classe'] = $rs->fields;

        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." AS cod_classe, "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"]." AS nome "
                . " FROM ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"];
        $rs = $this->container["db"]->execSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result['todas'][$row[$i]['cod_classe']]=$row[$i];
        }

        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe_filho"]." AS cod_classe_filho "
                . " FROM ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["nome"]." "
                . "WHERE ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe"]." = ".$cod_classe;
        $rs = $this->container["db"]->execSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result['todas'][$row[$i]['cod_classe_filho']]['permitido']=true;
        }

        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe"]." AS cod_classe "
                . " FROM ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["classexfilhos"]["colunas"]["cod_classe_filho"]." = ".$cod_classe;
        $rs = $this->container["db"]->execSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result['todas'][$row[$i]['cod_classe']]['criadoem']=true;
        }

        $prop = $this->pegarPropriedadesClasse($cod_classe);
        $count=1;
        $result['prop']=array();
        if (is_array($prop))
        {
            foreach($prop as $value)
            {
                $result['prop'][$value['nome']]=$value;
            }
        }

        $sql = "SELECT COUNT(*) AS cnt "
                . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." = ".$cod_classe;
        $rs = $this->container["db"]->execSQL($sql);
        $result['obj_conta'] = $rs->fields["cnt"];

        $sql = "SELECT "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." AS titulo, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["url_amigavel"]." AS url_amigavel "
                . " FROM ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["nick"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
                . " ON ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["classexobjeto"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["classexobjeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["classexobjeto"]["colunas"]["cod_classe"]." = ".$cod_classe;
//                . " objeto.titulo, objeto.url_amigavel from classexobjeto "
//                . "inner join objeto on classexobjeto.cod_objeto=objeto.cod_objeto "
//                . "where classexobjeto.cod_classe=$cod_classe";
        $res = $this->container["db"]->execSQL($sql);
        $row = $res->GetRows();
        for ($k=0; $k<sizeof($row); $k++)
        {
            $result['objetos'][]=$row[$k];
        }
        return $result;
    }

    /**
     * Busca tipos de dado existentes e envia valores para metodo que monta dropdown
     * @param int $selecionado - Valor que devera vir selecionado no dropdown
     * @param bool $branco - indica se devera ter <option> com value em branco
     * @return string - Lista de <option> para o <select>
     */
    function dropdownTipoDado($selecionado, $branco=false)
    {
        $lista = $this->pegarListaTipoDado();
        return $this->criarDropDown($lista, $selecionado, $branco);
    }

    /**
     * Pega lista de tipos de dados no banco de dados
     * @return array - lista com tipos de dados
     */
    function pegarListaTipoDado()
    {
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["cod_tipodado"]." AS codigo, "
                . " ".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["nome"]." AS texto "
                . " FROM ".$this->container["config"]->bd["tabelas"]["tipodado"]["nome"]." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["nome"];
        $rs = $this->container["db"]->execSQL($sql);
        return $rs->GetRows();
    }

    /**
     * Busca classes e envia valores para metodo que monta dropdown
     * @param int $selecionado - Valor que devera vir selecionado no dropdown
     * @param bool $branco - indica se devera ter <option> com value em branco
     * @return string - Lista de <option> para o <select>
     */
    function dropdownClasses($selecionado, $branco=false)
    {
        $lista = $this->pegarListaClasses($page);
        return $this->criarDropDown($lista, $selecionado, $branco);
    }
    
    /**
     * Pega lista de views nas pastas do site
     * @param object $page - Referência de objeto da classe Pagina
     * @return array
     */
    function pegarListaViews()
    {
        $retorno = array();
        
        $pastaDefault = $_SERVER['DOCUMENT_ROOT']."/html/template/";
        $pastaPeles = $_SERVER['DOCUMENT_ROOT']."/html/skin/";
        $tipos = array('php');
        
        // buscando arquivos na pasta de views padrão
        $default = array();
        if ($dir = opendir($pastaDefault))
        {
            while($arquivo = readdir($dir))
            {
                if ($arquivo != "." && $arquivo != "..")
                {
                    $arquivo = mb_strtolower($arquivo, "UTF-8");
                    $ext = pathinfo($arquivo);
                    if((isset($ext["extension"]) && in_array($ext["extension"], $tipos)) 
                            && $arquivo != "view_protegido.php" 
                            && substr($arquivo, 0, 5) == "view_"
                            && substr($arquivo, 0, 6) != "view__") $default[] = $arquivo;
                }
            }
            closedir($dir);
            sort($default);
        }
        $retorno["default"] = $default;
        
        // buscando views dentro das peles
        $peles = $this->pegarListaPeles();
        foreach ($peles as $pele)
        {
            $pasta = $pastaPeles . $pele["prefixo"] . "/";
            $temp = array();
            if (is_dir($pasta) && $dir = opendir($pasta))
            {
                while($arquivo = readdir($dir))
                {
                    if ($arquivo != "." && $arquivo != "..")
                    {
                        $arquivo = mb_strtolower($arquivo, "UTF-8");
                        $ext = pathinfo($arquivo);
                        if(in_array($ext["extension"], $tipos) 
                            && $arquivo != "view_protegido.php" 
                            && substr($arquivo, 0, 5) == "view_") $temp[] = $arquivo;
                    }
                }
                closedir($dir);
                sort($temp);
            }
            $retorno[$pele["prefixo"]] = $temp;
        }
        
        return $retorno;
    }

    /**
     * Busca lista de classes no banco de dados
     * @param object $page - Referência de objeto da classe Pagina
     * @return array - lista de classes
     */
    function pegarListaClasses(&$page)
    {
        $this->carregarClasses();
        
        foreach ($this->classes as $cod => $dados)
                $saida[] = array ('codigo'=>$cod, 'texto'=> $dados["nome"]);

        return $saida;
    }

    /**
     * Atualiza informações da classe
     * @param integer $cod_classe - Código da classe
     * @param array $dados - Dados da classe
     */
    function atualizarClasse($cod_classe, $dados)
    {
         $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." "
                . " SET ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"]." = '" . $dados["nome"] . "', "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["prefixo"]." = '" . $dados["prefixo"] . "', "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["descricao"]." = '" . $dados["descricao"] . "', "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["temfilhos"]." = '" . $dados["temfilhos"] . "', "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["indexar"]." = '" . $dados["index"] . "' "
                . "WHERE ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." = " . $cod_classe;
        $this->container["db"]->execSQL($sql);
    }
    
    /**
     * Cria classe no banco de dados
     * @param array $dados - Dados da classe
     * @return integer
     */
    function criarClasse($dados)
    {
        $cod_classe = 0;
        
        $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." "
                . " (".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"].", "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["prefixo"].", "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["descricao"].", "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["temfilhos"].", "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["indexar"].") "
                . " VALUES ('" . $dados["nome"] . "', "
                . " '" . $dados["prefixo"] . "', "
                . " '" . $dados["descricao"] . "', "
                . " '" . $dados["temfilhos"] . "', "
                . " '" . $dados["index"] . "')";
        $this->container["db"]->execSQL($sql);
        
        $sql = "SELECT max(".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"].") AS cod FROM ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]."";
        $rs = $this->container["db"]->execSQL($sql);
        while ($row = $rs->FetchRow())
        {
            $cod_classe = $row["cod"];
        }
        
        return $cod_classe;
    }

    /**
     * Apaga propriedade
     * @param int $cod_classe - Codigo da classe
     * @param string $nome - Nome da propriedade a ser apagada
     */
    function apagarPropriedadeDaClasse($cod_propriedade)
    {
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["tipodado"]["nome"].".".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["tabela"]." AS tabela "
                . " FROM ".$this->container["config"]->bd["tabelas"]["propriedade"]["nome"]." "
                . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["tipodado"]["nome"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["tipodado"]["nome"].".".$this->container["config"]->bd["tabelas"]["tipodado"]["colunas"]["cod_tipodado"]." = ".$this->container["config"]->bd["tabelas"]["propriedade"]["nome"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_tipodado"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["propriedade"]["nome"].".".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_propriedade"]." = ".$cod_propriedade;
        $rs = $this->container["db"]->execSQL($sql);
        $row = $rs->fields;

        if (isset($row['tabela']) && $row['tabela']!="")
        {
            if ($row['tabela'] == "tbl_blob")
            {
                $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_blob"]." AS cod_blob, "
                        . " ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["arquivo"]." AS arquivo "
                        . " FROM ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["nome"]." "
                        . " WHERE ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_propriedade"]." = ".$cod_propriedade;
                $rs2 = $this->container["db"]->execSQL($sql);

                while ($row2 = $rs2->FetchRow())
                {
                    $file_ext = Blob::PegaExtensaoArquivo($row2['arquivo']);
                    if (file_exists($this->container["config"]->portal["uploadpath"]."/".Blob::identificaPasta($this->container, $row2['cod_blob'])."/".$row2['cod_blob'].'.'.$file_ext))
                    {
                        $checkDelete = unlink($this->container["config"]->portal["uploadpath"]."/".Blob::identificaPasta($this->container, $row2['cod_blob'])."/".$row2['cod_blob'].'.'.$file_ext);
                    }
                }
            }
            
            $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"][$row['tabela']]["nome"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"][$row['tabela']]["colunas"]["cod_propriedade"]." = ".$cod_propriedade;
            $this->container["db"]->execSQL($sql);
        }
        
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["propriedade"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_propriedade"]." = ".$cod_propriedade;
        $this->container["db"]->execSQL($sql);
    }

    /**
     * Atualiza dados de propriedade ao criar ou alterar classe
     * @param int $cod_propriedade - Codigo da propriedade
     * @param array $dados - dados da proprieadde
     */
    function atualizarDadosPropriedade($cod_propriedade, $dados)
    {
        $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["propriedade"]["nome"]." SET ";
//        if(isset($dados["codrefclasse"]) && $dados["codrefclasse"]>0) 
//        {
            $sql .= " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_referencia_classe"]." = ".(!isset($dados["codrefclasse"]) || $dados["codrefclasse"]==0?"NULL":$dados["codrefclasse"]).", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["campo_ref"]." = '".$dados["camporef"]."', ";
//        }
        $sql .= " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["nome"]." = '".$dados['nome']."', "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["posicao"]." = ".$dados['posicao'].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["descricao"]." = '".$dados['descricao']."', "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["rotulo"]." = '".$dados['rotulo']."', "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["rot1booleano"]." = '".$dados['rot1booleano']."', "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["rot2booleano"]." = '".$dados['rot2booleano']."', "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["obrigatorio"]." = ".$dados['obrigatorio'].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["seguranca"]." = ".$dados['seguranca'].", "
                . " ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["valorpadrao"]." = '".$dados['valorpadrao']."' "
                . "WHERE ".$this->container["config"]->bd["tabelas"]["propriedade"]["colunas"]["cod_propriedade"]." = ".$cod_propriedade;
//        xd($sql);
        $this->container["db"]->execSQL($sql);
    }

    /**
     * Apaga classe do banco de dados e objetos que pertencam a ela
     * @param int $cod_classe - Codigo da classe a ser apagada
     */
    function apagarClasse($cod_classe)
    {
        // apagando a classe
        $sql  = "DELETE "
                . " FROM ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." = ".$cod_classe;
        $this->container["db"]->execSQL($sql);
    }
    
    /**
     * Cria view automaticamente para a classe, caso não exista na pasta de template do portal
     * @param type $cod_classe
     */
    function criarTemplateClasse($cod_classe)
    {
        $dados = $this->pegarInfoDaClasse($cod_classe);
        $prefixo = $dados["classe"]["prefixo"];
        $nome = $dados["classe"]["nome"];
        $pasta = $_SERVER['DOCUMENT_ROOT']."/html/template/";
        $nome_arquivo = "view_".$prefixo.".php";
        
        $str = "<?php \r\n"
                . "/** \r\n"
                . "* Criação da view para a classe '".$nome."' \r\n"
                . "* \r\n"
                . "* @author ".$_SESSION["usuario"]["nome"]." \r\n"
                . "* @version 1.0 \r\n"
                . "* <pre> \r\n"
                . "*   <b>1.0</b> \r\n"
                . "*    Data: ".date("d/m/Y")." \r\n"
                . "*    Autor: ".$_SESSION["usuario"]["nome"]." \r\n"
                . "*    Descricao: Versao inicial \r\n"
                . "*    Propriedades da Classe: \r\n";
foreach ($dados["prop"] as $prop)
{
    $str .= "*      #".$prop["nome"]." (".$prop["cod_propriedade"].") - ".$prop["tipodado"]." - ".$prop["rotulo"]." \r\n";
}
$str .= "*  <hr /> \r\n"
        . "* </pre> \r\n"
        . "* @name view_".$prefixo.".php \r\n"
        . "* @package html/template \r\n"
        . "* @access public \r\n"
        . "*/ \r\n"
        . "?> \r\n"
        . "\r\n"
        . "<script>/*window.location='/content/view/<@= #cod_pai@>.html';*/</script>\r\n";

        if (!file_exists($pasta.$nome_arquivo))
        {
            $fp = fopen($pasta.$nome_arquivo, "w");
            fwrite($fp, $str);
            fclose($fp);
        }
    }

    /**
     * Busca perfil de usuário no objeto
     * @param int $cod_usuario - Codigo do usuario
     * @param int $cod_objeto - Codigo do objeto
     * @return boolean
     */
    function pegarPerfilUsuarioObjeto($cod_usuario, $cod_objeto)
    {
        if (empty($cod_usuario)) return false;
        $perfil = $this->container["usuario"]->pegarDireitosUsuario($cod_usuario);
        $caminho = $this->container["adminobjeto"]->pegarCaminhoObjeto($cod_objeto);
        $caminho2 = array();
        foreach ($caminho as $cam)
        {
            $caminho2[] = $cam["cod_objeto"];
        }
        foreach ($perfil as $objeto => $cod_perfil)
        {
            if ((in_array($objeto, $caminho2))) return $cod_perfil;
        }
        return false;
    }



    /**
     * Busca lista de objetos apagados logicamente
     * @param int $start - dados para paginação da busca
     * @param int $limit - dados para paginação da busca
     * @return array - Lista de objetos apagados
     */
    function pegarListaApagados($inicio=0, $start=-1, $limit=-1)
    {
        $out=array();
        $sql = "SELECT distinct ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_exclusao"]." AS data_exclusao, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." AS titulo, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"]." AS classe "
                . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
               . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"]." "
               . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." "
                . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." ".$this->container["config"]->bd["tabelas"]["classe"]["nick"]." "
                . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." = ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["apagado"]." = 1 ";
        if ($inicio > 0)
        {
            // $sql .= " AND ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$inicio." ";
        }
        $sql .= " ORDER BY ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_exclusao"]." DESC";
        
        $rs = $this->container["db"]->execSQL($sql, $start, $limit);
        $row = $rs->GetRows();
        for ($l=0; $l<sizeof($row); $l++)
        {
                $row[$l]['exibir']="content/view/".$row[$l]['cod_objeto'].".html";
                $out[]=$row[$l];
        }
        return $out;
    }
    
    /**
     * Busca lista de objetos vencidos
     * @param string $ord1 - Metadado para ordenação da lista
     * @param string $ord2 - asc ou desc
     * @param int $inicio - dados para paginação da busca
     * @param int $limite - dados para paginação da busca
     * @param int $cod_objeto - Objeto pai da busca
     * @return array - Lista de objetos vencidos
     */
    function pegarListaVencidos($ord1="titulo", $ord2="asc", $inicio=-1, $limite=-1, $cod_objeto=1)
    {
        $out=array();
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." AS titulo, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_validade"]." AS data_validade, "
                . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"]." AS classe "
                . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." ".$this->container["config"]->bd["tabelas"]["classe"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." = ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto." "
                . " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_validade"]." < ".date("Ymd")."000000 "
                . " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["apagado"]." = 0 ";
        $rs = $this->container["db"]->execSQL($sql, $inicio, $limite);
        $row = $rs->GetRows();

        for ($i=0; $i<sizeof($row); $i++)
        {
            $row[$i]['exibir']="content/view/".$row[$i]['cod_objeto'].".html";
            $out[]=$row[$i];
        }

        return $out;
    }
    
    /**
     * Apaga registros de log do objeto
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     */
    function apagarLogObjeto($cod_objeto)
    {
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["logobjeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $this->container["db"]->execSQL($sql);
    }
    
    /**
     * Apaga registros de log do objeto
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     */
    function apagarLogWorkflow($cod_objeto)
    {
        $sql = "DELETE FROM ".$this->container["config"]->bd["tabelas"]["logworkflow"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $this->container["db"]->execSQL($sql);
    }

    /**
     * Apaga objeto em definitivo - fisicamente
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     */
    function apagarEmDefinitivo($cod_objeto)
    {
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." AS cod_objeto "
                . " FROM ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto;
        $res=$this->container["db"]->execSQL($sql);
        $row = $res->GetRows();

        for ($c=0; $c<sizeof($row); $c++)
        {
            $this->apagarEmDefinitivo($row[$c]["cod_objeto"]);
        }

        $this->container["db"]->execSQL("DELETE FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto);
    }

    /**
     * Recupera objeto apagado logicamente
     * @param int $cod_objeto - Codigo do objeto a ser recuperado
     */
    function recuperarObjeto($cod_objeto)
    {
        $sql = "SELECT distinct ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." AS cod_objeto "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"]." "
                    . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
                        . " ON ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." "
                    . " WHERE (".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto." "
                    . " OR ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." = ".$cod_objeto.") "
                    . " AND (".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["apagado"]." = 1) ";
        $res = $this->container["db"]->execSQL($sql);

        while ($row = $res->FetchRow())
        {
            $sql = "UPDATE ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                    . " SET ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["apagado"]." = 0, "
                    . " ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_exclusao"]." = null "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$row['cod_objeto'];
            $this->container["db"]->execSQL($sql);

            $this->container["log"]->incluirLogObjeto($cod_objeto,_OPERACAO_OBJETO_RECUPERAR);

            if ($row["cod_objeto"] != $cod_objeto)
            {
                $this->recuperarObjeto($row["cod_objeto"]);
            }
        }
    }

    /**
     * Verifica se propriedade tem preenchimento obrigatorio
     * @param int $cod_classe - Codigo da classe que propriedade pertence
     * @param array $propriedades - Lista de propriedades
     * @return boolean
     */
    function validarPropriedades($cod_classe, $propriedades)
    {
        $lista = $this->pegarPropriedadesClasse($cod_classe);
        foreach ($lista as $prop)
        {
            if (($prop['obrigatorio']) && (!strlen($propriedades['prop:'.$prop['nome']]))) return false;
        }
        return true;	
    }
    
    /**
     * Grava objeto
     * @param array $post
     * @param string $acao
     * @param int $publicar
     * @param int $cod
     * @return \Objeto
     */
    function gravarObjeto($post, $acao, $publicar=0, &$cod)
    {
        $retorno = array();
        // executa scripts antes da gravacao do objeto
        
        $_POST = $post;
        $execAntes = $this->container["adminobjeto"]->executarScript($post['cod_classe'], $post['cod_pele'], 'antes');
        $post = $_POST;
        
        $executa = false;
        $cod = 0;
        //        xd($post);
        
        if ($acao=="create")
        {
            $cod = $this->criarObjeto($post);
            $executa = true;
        }
        elseif ($acao=="edit")
        {
            $cod = $this->alterarObjeto($post);
            $executa = true;
        }
        
        if ($executa === true)
        {
            
            $obj = new Objeto($this->container, $cod);
            $this->gravarVersao($cod);
            // xd("aha");
            $retorno["obj"] = $obj;
            
            if ($publicar==1)
            {
                $this->submeterObjeto('Solicitada publicação da versão '.$obj->valor("versao"), $cod);
            }
            elseif ($publicar==2)
            {
                $this->publicarObjeto('Publicada versão '.$obj->valor("versao"), $cod);
            }
        }
        
        // chama a execução de scripts depois de gravar o objeto
        $execDepois = $this->container["adminobjeto"]->executarScript($post['cod_classe'], $post['cod_pele'], 'depois');
        
        return $retorno;
    }

}
