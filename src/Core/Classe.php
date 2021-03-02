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
class Classe extends Base
{
    private $classes;
    private $classesPrefixos;
    private $classesNomes;
    private $classesIndexaveis = array();
    
    /**
     * Adiciona propriedade em classe
     * @param int $cod_classe - Codigo da classe
     * @param array $novo - Dados da propriedade
     */
    function acrescentarPropriedade($cod_classe, $novo)
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
    
    // /**
    //  * Busca lista de classes no banco de dados e popula propriedades de classes
    //  */
    // function carregarClasses()
    // {
    //     $this->container["adminobjeto"]->carregarClasses();
        
    //     if (is_null($this->classesPrefixos) || !is_array($this->classesPrefixos))
    //     {
    //         $this->classesPrefixos = $_SESSION['classesPrefixos'];
    //         $this->classesNomes = $_SESSION['classesNomes'];
    //         $this->classes = $_SESSION['classes'];
    //         $this->classesIndexaveis = $_SESSION['classesIndexaveis'];
    //     }
    // }

    /**
     * Carrega as classes do portal e guarda em session
     */
    function carregar()
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("classe::carregar");
        }

        if ((!isset($_SESSION['classesPrefixos'])) || (!is_array($_SESSION['classesPrefixos'])) || count($_SESSION['classesPrefixos']) == 0)
        {
            if (!isset($_SESSION['classes'])) $_SESSION['classes'] = array();
            $_SESSION['classesPrefixos'] = array();
            $_SESSION['classesNomes'] = array();
            $_SESSION['classesIndexaveis'] = array();

            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." AS cod_classe, "
                    . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["prefixo"]." AS prefixo, "
                    . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"]." AS nome, "
                    . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["indexar"]." AS indexar, "
                    . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["descricao"]." AS descricao, "
                    . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["sistema"]." AS sistema "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." ".$this->container["config"]->bd["tabelas"]["classe"]["nick"]." "
                    . " ORDER BY ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"];
            $rs = $this->container["db"]->execSQL($sql);

            if ($rs->_numOfRows > 0)
            {
                while ($row = $rs->FetchRow())
                {
                    $row["propriedades"] = isset($_SESSION['classes'][$row['cod_classe']]["propriedades"])?$_SESSION['classes'][$row['cod_classe']]["propriedades"]:array();
                    $_SESSION['classes'][$row['cod_classe']] = $row;

                    $_SESSION['classesPrefixos'][$row['prefixo']] = $row['cod_classe'];
                    $_SESSION['classesNomes'][strtolower($row['nome'])] = $row['cod_classe'];
                    if ($row['indexar']) {
                        if (!in_array($row['cod_classe'], $_SESSION['classesIndexaveis'])) $_SESSION['classesIndexaveis'][] = $row['cod_classe'];
                    }
                }
            }
        }
    }

    /**
     * Retorna o código de uma classe com base em seu prefixo
     * @param string $prefixo - Prefixo da classe
     * @return int - Código da classe
     */
    function codigo($prefixo)
    {
        $this->carregar();
        // xd($prefixo);
        return $_SESSION["classesPrefixos"][$prefixo];
    }

    /**
     * Busca propriedades da classe no banco de dados e retorna array com informações
     * @param int $cod_classe
     * @return array
     */
    function pegarPropriedades($cod_classe)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("classe::pegarPropriedades cod_classe=".$cod_classe);
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

    function pegarPropriedadesObjeto($cod_objeto)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarPropriedades cod_objeto=".$cod_objeto);
        }

        if ($cod_objeto == $this->container["objeto"]->valor("cod_objeto"))
        {
            return $this->pegarPropriedades($this->container["objeto"]->valor("cod_classe"));
        }
        else
        {
            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." AS cod_classe "
                . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
            $rs = $this->container["db"]->execSQL($sql);
            $row = $rs->GetRows();
            return $this->pegarPropriedades($row[0]["cod_classe"]);
        }
    }

    /**
     * Apaga e refaz relacionamento entre classes
     * @param int $cod_classe - Codigo da classe principal que esta tendo o relacionamento remontado
     * @param array $relacao - Codigos das classes que compoem o relacionamento
     * @param int $tipo - Informa tipo de relação: 1=contem, 2=está contido
     */
    function montarRelacionamento($cod_classe, $relacao, $tipo)
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
    function pegarInfo($cod_classe)
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

        $prop = $this->pegarPropriedades($cod_classe);
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
     * Busca classes e envia valores para metodo que monta dropdown
     * @param int $selecionado - Valor que devera vir selecionado no dropdown
     * @param bool $branco - indica se devera ter <option> com value em branco
     * @return string - Lista de <option> para o <select>
     */
    function dropdown($selecionado, $branco=false)
    {
        $lista = $this->pegarLista();
        return $this->criarDropDown($lista, $selecionado, $branco);
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
     * Busca lista de classes no banco de dados
     * @param object $page - Referência de objeto da classe Pagina
     * @return array - lista de classes
     */
    function pegarLista()
    {
        $this->carregar();
        
        foreach ($_SESSION['classes'] as $cod => $dados)
                $saida[] = array ('codigo'=>$cod, 'texto'=> $dados["nome"]);

        return $saida;
    }

    /**
     * Atualiza informações da classe
     * @param integer $cod_classe - Código da classe
     * @param array $dados - Dados da classe
     */
    function atualizar($cod_classe, $dados)
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
    function criar($dados)
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
    function apagarPropriedade($cod_propriedade)
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
        $this->container["db"]->execSQL($sql);
    }

    /**
     * Apaga classe do banco de dados e objetos que pertencam a ela
     * @param int $cod_classe - Codigo da classe a ser apagada
     */
    function apagar($cod_classe)
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
    function criarTemplate($cod_classe)
    {
        $dados = $this->pegarInfo($cod_classe);
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

    

}
