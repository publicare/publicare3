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
use Pbl\Core\Blob;

/**
 * Classe adminobjeto, responsável por gerenciar parte usuários de objetos
 */
class AdminObjeto extends Base
{
    /**
     * Gera SQL para verificar se objeto está publicado
     * @return string
     */
    function criarCondicaoPublicado()
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::criarCondicaoPublicado");
        }

        return " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_status"]." = "._STATUS_PUBLICADO;
    }

    /**
     * Cria SQL para verificar se objeto está publicado, dentro de data válida,
     * ou usuário é dono
     * @return string
     */
    function criarCondicaoAutor()
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::criarCondicaoAutor");
        }

        return " AND ((".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_status"]." = "._STATUS_PUBLICADO.$this->criarCondicaoData($page).") "
                . " OR ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_usuario"]." = ".$_SESSION['usuario']['cod_usuario'].') ';
    }

    public function buscaObjetoJsonDatatable($data, $apagados=false)
    {
        $busca = isset($data["search"]["value"])&&$data["search"]["value"]!=""?htmlspecialchars($data["search"]["value"], ENT_QUOTES, "UTF-8"):"";
        $draw = isset($data["draw"])&&$data["draw"]!=""?htmlspecialchars($data["draw"], ENT_QUOTES, "UTF-8"):"1";
        $qry = "";
        if($apagados === true)
        {
            // $qry = "data_exclusao !=null".$qry;
        }
        $array = array();
        if ($busca != "")
        {
            $qry .= /*($apagados === true?"&&":"").*/"titulo like %".$busca."%";
            $qry .= "||classe like %".$busca."%";
            if(is_numeric($busca))
            {
                $qry .= "||cod_objeto like %".$busca."%";
            }
        }
        //$qry = "titulo like %a%";
        $ordem = isset($data["order"][0]["column"])?$data["columns"][(int)$data["order"][0]["column"]]["data"]:"";
        if (isset($data["order"][0]["dir"]) && $data["order"][0]["dir"]=="desc")
        {
            $ordem = "-".$ordem;
        }
        $inicio = isset($data["start"])&&$data["start"]?(int)htmlspecialchars($data["start"], ENT_QUOTES, "UTF-8"):-1;
        $limite = isset($data["length"])&&$data["length"]?(int)htmlspecialchars($data["length"], ENT_QUOTES, "UTF-8"):-1;
        $pai = $this->container["objeto"]->valor("cod_objeto");
        $niveis = 0;


        // xd($qry);
    
        $objetos = $this->localizarObjetos('*', $qry, $ordem, $inicio, $limite, $pai, $niveis, $apagados);
        $objetos2 = $this->localizarObjetos('*', $qry, $ordem, -1, -1, $pai, $niveis, $apagados);
        $objetostotal = $this->localizarObjetos('*', "", "", -1, -1, $pai, $niveis, $apagados);
        $array = array(
            "draw" => $draw,
            "recordsTotal" => count($objetostotal),
            "recordsFiltered" => count($objetos2),
            "data" => array()
        );
        foreach ($objetos as $obj)
        {
            $checkbox = "";
            $titulo = $obj->metadados["titulo"];
            $acoes = "";
    
            if ($obj->metadados["cod_status"] == _STATUS_PRIVADO || $obj->metadados["cod_status"] == _STATUS_REJEITADO)
            {
                $titulo = "<font color='red'>".$titulo."</font>";
            }
            elseif ($obj->metadados["cod_status"] == _STATUS_SUBMETIDO)
            {
                $titulo = "<font color='blue'>".$titulo."</font>";
            }
    
            if ($_SESSION['usuario']['perfil'] < _PERFIL_AUTOR 
                    || ($_SESSION['usuario']['perfil']==_PERFIL_AUTOR && $obj->valor("cod_usuario")==$_SESSION['usuario']['cod_usuario']))
            {
                $checkbox = '<input type="checkbox" '
                        . 'id="objlist_'.$obj->valor("cod_objeto").'" '
                        . 'name="objlist[]" '
                        . 'value="'.$obj->valor("cod_objeto").'" class="chkObj">';
            }
            
            if ($_SESSION['usuario']['perfil'] < _PERFIL_AUTOR 
                    || ($_SESSION['usuario']['perfil']==_PERFIL_AUTOR && $obj->valor("cod_usuario")==$_SESSION['usuario']['cod_usuario']))
            {
                $acoes .= '<a href="'.$this->container["config"]->portal["url"].'/do/edit/'.$obj->valor("cod_objeto").'.html" '
                        . 'title="Editar Objeto" '
                        . 'class="ml-1" '
                        . 'rel="tooltip" '
                        . 'data-animate="animated fadeIn" '
                        . 'data-toggle="tooltip" '
                        . 'data-original-title="Editar Objeto" '
                        . 'data-placement="left" '
                        . 'title="Editar este objeto"><i class="fapbl fapbl-pencil-alt"></i></a>';
            }
    
            $acoes .= "<a href='".$this->container["config"]->portal["url"].$obj->valor("url")."' "
                    . "title='Exibir Objeto' "
                    . "rel='tooltip' "
                    . "data-animate='animated fadeIn' "
                    . "data-toggle='tooltip' "
                    . "data-original-title='Visualizar objeto' "
                    . "data-placement='left' "
                    . "title='Visualizar objeto' "
                    . "class='ml-1'><i class='fapbl fapbl-eye'></i></a>";
    
            if ($obj->podeTerFilhos())
            {
                $acoes .= "<a href='".$this->container["config"]->portal["url"]."/do/list_content/".$obj->valor("cod_objeto").".html' "
                        . "title='Listar conteúdo' "
                        . "rel='tooltip' "
                        . "data-animate='animated fadeIn' "
                        . "data-toggle='tooltip' "
                        . "data-original-title='Listar conteúdo' "
                        . "data-placement='left' "
                        . "title='Listar conteúdo' "
                        . "class='ml-1'><i class='fapbl fapbl-folder-open'></i></a>";
            }
    
            $dados = $obj->metadados;
            $dados["acoes"] = $acoes;
            $dados["checkbox"] = $checkbox;
            $dados["titulo"] = $titulo;
    
            $array["data"][] = $dados;
            
        }
        return \json_encode($array);
    }

    /**
     * Monta SQL para verificar se objeto está dentro de data válida
     * @return string
     */
    function criarCondicaoData()
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::criarCondicaoData");
        }
        return " AND (".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_publicacao"]." <= ".date("YmdH")."0000 "
                . " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_validade"]." >= ".date("YmdH")."5959) ";
    }

    /**
     * Realiza busca de objetos no banco do publicare
     * @param string $query - Texto a buscar
     * @param string $excecoes - Lista de classes que não deve buscar, codigos separados por virgula
     * @param string $parentesco_excecoes - Lista de objetos que não deve buscar filhos
     * @param int $pagina - Pagina atual de resultados
     * @param int $paginacao - Numero de registros por página
     * @return array - Lista com resultado da busca
     */
    function search($query, $excecoes="", $parentesco_excecoes="", $pagina=1, $paginacao=20, $classe=0)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::search");
        }
        $retorno = array("total"=>0,
                        "paginas"=>0,
                        "pagina"=>$pagina,
                        "paginacao"=>$paginacao,
                        "inicio"=>0,
                        "fim"=>0,
                        "query"=>$query,
                        "resultados"=>array());
        
        $PERFIL = $this->container["usuario"]->cod_perfil;
        
        if ((isset($query) && strlen($query)>1))
        {
            $query = addslashes($query);
            
            $sql = "SELECT DISTINCT(".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]."), "
                        ." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pai"]." AS cod_pai, "
                        ." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." AS cod_classe, "
                        ." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." AS titulo, "
                        ." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["descricao"]." AS descricao, "
                        ." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["url_amigavel"]." AS url_amigavel, "
                        ." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["peso"]." AS peso, "
                        ." ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"]." AS nome_classe "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
                    . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." ".$this->container["config"]->bd["tabelas"]["classe"]["nick"]." "
                        . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." = ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." "
                    . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["tbl_text"]["nome"]." ".$this->container["config"]->bd["tabelas"]["tbl_text"]["nick"]." "
                        . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["tbl_text"]["nick"].".".$this->container["config"]->bd["tabelas"]["tbl_text"]["colunas"]["cod_objeto"]." "
                    . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["tbl_string"]["nome"]." ".$this->container["config"]->bd["tabelas"]["tbl_string"]["nick"]." "
                        . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["tbl_string"]["nick"].".".$this->container["config"]->bd["tabelas"]["tbl_string"]["colunas"]["cod_objeto"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["apagado"]." = 0 ";

            if ($PERFIL > _PERFIL_AUTOR) { 
                $sql .= " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_status"]." = 2 "
                        . " AND (".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_publicacao"]." <= ".date("YmdHi")."00 "
                        . " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["data_validade"]." >= ".date("YmdHi")."00) "; 
            }
            
            if ($excecoes != "") { 
                $sql .= " AND ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." NOT IN (".$excecoes.") "; 
            }
            
            if ($parentesco_excecoes!="") { 
                $sql .= " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." "
                        . " NOT IN (SELECT DISTINCT(pa2.".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"].") "
                        . " FROM ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." pa2 "
                        . " WHERE pa2.".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." in (".$parentesco_excecoes.")) "; 
            }
            
            $sql .= " AND (UPPER(".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"].") LIKE ('%".strtoupper($query)."%') "
                    . " OR UPPER(".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["descricao"].") LIKE ('%".strtoupper($query)."%') "
                    . " OR UPPER(".$this->container["config"]->bd["tabelas"]["tbl_text"]["nick"].".".$this->container["config"]->bd["tabelas"]["tbl_text"]["colunas"]["valor"].") LIKE ('%".strtoupper($query)."%') "
                    . " OR UPPER(".$this->container["config"]->bd["tabelas"]["tbl_string"]["nick"].".".$this->container["config"]->bd["tabelas"]["tbl_string"]["colunas"]["valor"].") LIKE ('%".strtoupper($query)."%')) "
                    . " AND ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["indexar"]." = 1 "
                    . " ORDER BY ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"];
            
            $sqlCont = "SELECT COUNT(*) AS total FROM (" . $sql . ") sqlbusca";
            
            $rs = $this->container["db"]->execSQL($sqlCont);
            if ($rs->_numOfRows>0)
            {
                while ($row = $rs->FetchRow())
                {
                    $retorno["total"] = (int)$row["total"];
                }
            }
            
           if ($retorno["total"] > 0)
           {
               $retorno["inicio"] = ($retorno["pagina"] > 1) ? (($retorno["paginacao"] * ($retorno["pagina"] - 1)) + 1) : 1;
               $retorno["fim"] = ($retorno["inicio"] + $retorno["paginacao"]) - 1;
               if ($retorno["fim"] > $retorno["total"]) { $retorno["fim"] = $retorno["total"]; }
               $retorno["paginas"] = intval($retorno["total"] / $retorno["paginacao"]);
               if ($retorno["total"] % $retorno["paginacao"] > 0) { $retorno["paginas"]++; }
               
               $rs = $this->container["db"]->execSQL($sql, $retorno["inicio"]-1, $retorno["paginacao"]);
               if ($rs->_numOfRows > 0)
               {
                   $retorno["resultados"] = $rs->GetRows();
               }
           }
            
        } 
        return $retorno;
    }

    /**
     * Recupera as tags de determinado objeto no banco de dados
     * @param int $cod_objeto - Codigo do objeto que deseja recupear as tags
     * @return string - tags separadas por virgula
     */
    function pegarTags($cod_objeto)
    {
        $tblTag = $this->container["config"]->bd["tabelas"]["tag"];
        $tblTagx = $this->container["config"]->bd["tabelas"]["tagxobjeto"];

        $tags = "";
        $sql = "SELECT ".$tblTag["nick"].".".$tblTag["colunas"]["nome_tag"]." AS nome_tag "
                . "FROM ".$tblTag["nome"]." ".$tblTag["nick"]." "
                . "INNER JOIN ".$tblTagx["nome"]." ".$tblTagx["nick"]." "
                    . "ON ".$tblTag["nick"].".".$tblTag["colunas"]["cod_tag"]." = ".$tblTagx["nick"].".".$tblTagx["colunas"]["cod_tag"]." "
                . "WHERE ".$tblTagx["nick"].".".$tblTagx["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $rs = $this->container["db"]->execSQL($sql);
        if ($rs->_numOfRows>0)
        {
            while ($row = $rs->FetchRow())
            {
                if (strlen($row["nome_tag"])>=3)
                {
                  $tags .= ", ".$row["nome_tag"];
                }
            }
        }
        if (strlen($tags)>=3) $tags = trim(substr($tags, 1));
        return $tags;
    }
	
    /**
     * Recupera dados do objeto pelo título
     * @param string $titulo - Titulo do objeto
     * @return array - array com metadados do objeto
     */
    function pegarDadosObjetoTitulo($titulo)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarDadosObjetoTitulo titulo=".$titulo);
        }

        $sql = $this->container["db"]->sqlobj." WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." = '".$titulo."' ";
        $rs = $this->container["db"]->execSQL($sql);
        $dados = $rs->fields;
        return $dados;
    }

    /**
     * Recupera dados do objeto pelo código
     * @param int $cod_objeto - Código do objeto
     * @return array - array com metadados do objeto
     */
    function pegarDadosObjetoId($cod_objeto)
    {
        if (is_numeric($cod_objeto))
        {
            $tblObjeto = $this->container["config"]->bd["tabelas"]["objeto"];
            $sql = $this->container["db"]->getSqlObj()." "
                ." WHERE ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_objeto"]." = ".$cod_objeto;
            $rs = $this->container["db"]->execSQL($sql);
            $dados = $rs->fields;
            return $dados;
        }
        return false;
    }
    
    /**
     * Instancia objeto da classe Objeto e já popula o mesmo
     * @param int $cod_objeto - Codigo do objeto
     * @return \Objeto
     */
    function criarObjeto($cod_objeto)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::criarObjeto cod_objeto=".$cod_objeto);
        }
        
        $objeto = new Objeto($this->container, $cod_objeto);
        return $objeto;
    }

    /**
     * Pega o caminho do objeto e retorna string com caminho recursivo do objeto
     * @param int $cod_objeto - Codigo do objeto
     * @return string - Caminho até o objeto, separado por virgulas
     */
    function pegarCaminhoObjeto($cod_objeto)
    {
        return $this->recursivaCaminhoObjeto($cod_objeto);
    }

    /**
     * Busca o caminho do objeto recursivamente, utilizando tabela parentesco
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Caminho do objeto
     */
    function recursivaCaminhoObjeto($cod_objeto)
    {
        $result = array();

        if ($cod_objeto != $this->container["config"]->portal["objroot"])
        {
            $tblObjeto = $this->container["config"]->bd["tabelas"]["objeto"];
            $tblParentesco = $this->container["config"]->bd["tabelas"]["parentesco"];

            $sql = "SELECT ".$tblParentesco["nick"].".".$tblParentesco["colunas"]["cod_pai"]." AS cod_pai, "
                    ." ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["titulo"]." AS titulo, "
                    ." ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["script_exibir"]." AS script_exibir, "
                    ." ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["url_amigavel"]." AS url_amigavel "
                    ." FROM ".$tblParentesco["nome"]." ".$tblParentesco["nick"]." "
                    ." LEFT JOIN ".$tblObjeto["nome"]." ".$tblObjeto["nick"]." "
                        ." ON ".$tblParentesco["nick"].".".$tblParentesco["colunas"]["cod_pai"]." = ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_objeto"]." "
                    ." WHERE ".$tblParentesco["nick"].".".$tblParentesco["colunas"]["cod_objeto"]." = ".$cod_objeto." "
                    . " ORDER BY ".$tblParentesco["nick"].".".$tblParentesco["colunas"]["ordem"]." DESC";
            $rs = $this->container["db"]->execSQL($sql);
    
            if ($rs->_numOfRows>0)
            {
                while (!$rs->EOF)
                {
                    $result[$rs->fields['cod_pai']] = array(
                        "cod_objeto" => $rs->fields['cod_pai'],
                        "titulo" => $rs->fields['titulo'],
                        "script_exibir" => $rs->fields['script_exibir'],
                        "url_amigavel" => $rs->fields['url_amigavel']
                    );
                    $rs->MoveNext();
                }
            } 
        }
        return $result;
    }

    /**
     * Pega o caminho do objeto e retorna array com codigo e titulo de todo o parentesco
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Caminho do objeto em array com dados [cod_objeto], [titulo]
     */
    function pegarCaminhoObjetoComTitulo($cod_objeto)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarCaminhoObjetoComTitulo cod_objeto=".$cod_objeto);
        }

        $result = array();
        
        

        $sql = "SELECT "
                . " ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["ordem"]." AS ordem, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." AS titulo "
                . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." = ".$cod_objeto." "
                . " GROUP BY ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["ordem"].", "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"].", "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["ordem"]." DESC";

        $res = $this->container["db"]->execSQL($sql);
        $row = $res->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result[]= array('cod_objeto'=>$row[$i]['cod_objeto'],'titulo'=>$row[$i]['titulo']);
        }

        return $result;
    }

    function pegarPropriedadesClasseObjeto($cod_objeto)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarPropriedades cod_objeto=".$cod_objeto);
        }

        if ($cod_objeto == $this->container["objeto"]->valor("cod_objeto"))
        {
            return $this->container["administracao"]->pegarPropriedadesClasse($this->container["objeto"]->valor("cod_classe"));
        }
        else
        {
            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." AS cod_classe "
                . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
            $rs = $this->container["db"]->execSQL($sql);
            $row = $rs->GetRows();
            return $this->container["administracao"]->pegarPropriedadesClasse($row[0]["cod_classe"]);
        }
    }

    /**
     * Busca todas as propriedades de determinado objeto
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Array com propriedades
     */
    function pegarPropriedades($cod_objeto)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarPropriedades cod_objeto=".$cod_objeto);
        }

        $result = array();

        $props = $this->pegarPropriedadesClasseObjeto($cod_objeto);
        
        $join = array();
        $campos = array();
        $tipo = array();

        // popula array de propriedades e busca informações
        // caso seja objeto-ref e a referencia não seja metadado
        // $props = $rs->GetRows();
//        while ($row = $rs->FetchRow())
//        {
//            if ($row["tabela"] == "tbl_objref" && !$this->ehMetadado($row["campo_ref"]))
//            {
//                $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto "
//                        . " FROM ".$this->container["config"]->bd["tabelas"]["tbl_objref"]["nome"]." ".$this->container["config"]->bd["tabelas"]["tbl_objref"]["nick"]." "
//                        . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
//                            . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["tbl_objref"]["nick"].".".$this->container["config"]->bd["tabelas"]["tbl_objref"]["colunas"]["valor"]." "
//                        . " WHERE ".$this->container["config"]->bd["tabelas"]["tbl_objref"]["nick"].".".$this->container["config"]->bd["tabelas"]["tbl_objref"]["colunas"]["cod_propriedade"]." = ".$row["cod_propriedade"]." "
//                        . " AND ".$this->container["config"]->bd["tabelas"]["tbl_objref"]["nick"].".".$this->container["config"]->bd["tabelas"]["tbl_objref"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
//                $rs2 = $this->container["db"]->execSQL($sql);
//                $propriedade = $rs2->fields;
//                if ($propriedade["cod_objeto"]) $dados = $this->pegarPropriedades($propriedade["cod_objeto"]);
//                $row[$i]["valor_saida"] = $dados[strtolower($row[$i]["campo_ref"])];
//            }
//            $props[] = $row;
//        }
//        exit();
//
        // Monta SQLs para busca dos valores das propriedades em suas respectivas tabelas
        if (isset($props) && is_array($props) && count($props)>0)
        {
            foreach ($props as $row)
            {
                $result[$row['nome']]['tipo'] = $row['tabela'];
                $tabela = 'tbl_'.$row['nome'];
                $array_nomes[] = $row['nome'];
				
                switch ($row['tabela'])
                {
                    case 'tbl_objref':
                        if ($this->ehMetadado($row['campo_ref']))
                        {
                            $tipo[] = 'ref';
                            $join[] = " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["tbl_objref"]["nome"]." ".$tabela." "
                                    . " ON (".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_objref"]["colunas"]["cod_propriedade"]." = ". $row['cod_propriedade']." "
                                    . " AND ".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_objref"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"].") ";
                            $join[] = " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$tabela."_objeto "
                                    . " ON (".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_objref"]["colunas"]["valor"]." = ".$tabela."_objeto.".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"].") ";
                            $campos[] = " ".$tabela."_objeto.".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"][$row['campo_ref']]." AS ".$row['nome']." ";
                            $campos[] = " ".$tabela."_objeto.".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." AS ".$row['nome']."_referencia"." ";
                            $campos[] = " '".$row["campo_ref"]."' AS ".$row['nome']."_campo ";
                        }
                        else
                        {
//                            
                            $tipo[] = 'ref_prop';
                            $join[] = " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["tbl_objref"]["nome"]." ".$tabela." "
                                    . " ON (".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_objref"]["colunas"]["cod_propriedade"]." = ". $row['cod_propriedade']." "
                                    . " AND ".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_objref"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"].") \r\n";
                            $join[] = " LEFT JOIN ".$this->container["config"]->bd["tabelas"][$row["valor_saida"]["tipo"]]["nome"]." ".$tabela."_prop "
                                    . " ON (".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_objref"]["colunas"]["valor"]." = ".$tabela."_prop.".$this->container["config"]->bd["tabelas"][$row["valor_saida"]["tipo"]]["colunas"]["cod_objeto"].") ";
                            $campos[] = $tabela."_prop.".$this->container["config"]->bd["tabelas"][$row["valor_saida"]["tipo"]]["colunas"]["valor"]." AS ".$row['nome'];
                            $campos[] = $tabela.".".$this->container["config"]->bd["tabelas"]["tbl_objref"]["colunas"]["cod_objeto"]." AS ".$row['nome']."_referencia";
                            $campos[] = "'".$row["campo_ref"]."' AS ".$row['nome']."_campo";
                        }
                        break;
                    case 'tbl_blob':
                        $tipo[] = 'blob';
                        $join[] = " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["tbl_blob"]["nome"]." ".$tabela." "
                                . " ON (".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_propriedade"]." = ". $row['cod_propriedade']." "
                                . " AND ".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"].") ";
                        $campos[] = " ".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["cod_blob"]." AS ".$row['nome']."_cod_blob";
                        $campos[] = " ".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["arquivo"]." AS ".$row['nome']."_arquivo";
                        $campos[] = " ".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_blob"]["colunas"]["tamanho"]." AS ".$row['nome']."_tamanho";
                        break;
                    case 'tbl_date':
                        $tipo[] = 'date';
                        $join[] = " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["tbl_date"]["nome"]." ".$tabela." "
                                . " ON (".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_date"]["colunas"]["cod_propriedade"]." = ".$row['cod_propriedade']." "
                                . " AND ".$tabela.".".$this->container["config"]->bd["tabelas"]["tbl_date"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"].") ";
                        $campos[] = $tabela.".".$this->container["config"]->bd["tabelas"]["tbl_date"]["colunas"]["valor"]." AS ".$row['nome'];
                        break;
                    default:
                        $tipo[] = 'default';
                        $join[] = " LEFT JOIN ".$this->container["config"]->bd["tabelas"][$row['tabela']]["nome"]." ".$tabela." "
                                . " ON (".$tabela.".".$this->container["config"]->bd["tabelas"][$row['tabela']]["colunas"]["cod_propriedade"]." = ".$row['cod_propriedade']." "
                                . " AND ".$tabela.".".$this->container["config"]->bd["tabelas"][$row['tabela']]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"].") ";
//                        $join[] = " left join ".$row['tabela']." as ".$tabela." on (".$tabela.".cod_propriedade=".$row['cod_propriedade']." and ".$tabela.".cod_objeto=".$this->container["db"]->nomes_tabelas["objeto"].".cod_objeto )";
                        $campos[] = " ".$tabela.".".$this->container["config"]->bd["tabelas"][$row['tabela']]["colunas"]["valor"]." AS ".$row['nome'];
                        break;
                }
            }
		
            // Monta SQL com dados dos arrays de montagem
            $sql = "SELECT ".implode(',',$campos)." "
                    . " FROM ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
                    . " ".implode(' ',$join)." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
            $res = $this->container["db"]->execSQL($sql);
            
        //    xd($sql);
            
            if($dados = $res->fields)
            {
                foreach ($tipo as $key => $value)
                {
                    switch ($value)
                    {
                        case 'ref':
                        case 'ref_prop':
                            $result[$array_nomes[$key]]['valor'] = $dados[$array_nomes[$key]];
                            $result[$array_nomes[$key]]['referencia'] = $dados[$array_nomes[$key].'_referencia'];
                            $result[$array_nomes[$key]]['campo'] = $dados[$array_nomes[$key].'_campo'];
                            break;
                        case 'blob':
                            $result[$array_nomes[$key]]['valor'] = $dados[$array_nomes[$key].'_arquivo'];
                            $result[$array_nomes[$key]]['cod_blob'] = $dados[$array_nomes[$key].'_cod_blob'];
                            $result[$array_nomes[$key]]['tamanho_blob'] = $dados[$array_nomes[$key].'_tamanho'];
                            $result[$array_nomes[$key]]['tipo_blob'] = Blob::PegaExtensaoArquivo($dados[$array_nomes[$key].'_arquivo']);
                            break;
                        case 'date':
                            $result[$array_nomes[$key]]['valor'] = ConverteData($dados[$array_nomes[$key]],5);
                            break;
                        default:
                            $result[$array_nomes[$key]]['valor'] = isset($dados[$array_nomes[$key]])?$dados[$array_nomes[$key]]:"";
                    }
                }
            }
        }
        return $result;
    }
    
    /**
     * Lista objetos filhos de determinado objeto
     * @param int $cod_objeto - Codigo do objeto pai
     * @param string $classe - Classes para buscar filhos
     * @param string $ordem - Ordem do resultado
     * @param int $inicio - Registro inicial para paginacao
     * @param int $limite - Numero de registros para trazer na paginação
     * @return array - Array de objetos
     */
    function pegarListaFilhos($cod_objeto, $classe='*', $ordem='', $inicio=-1, $limite=-1)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarListaFilhos cod_objeto=".$cod_objeto);
        }
            return $this->localizarObjetos($classe, '', $ordem, $inicio, $limite, $cod_objeto, 0);
    }

    /**
     * Usa tabela parentesco para buscar os codigos dos objetos filho
     * @param int $cod_objeto - Codigo do objeto a buscar os filhos
     * @return array - array com codigo dos objetos
     */
    function pegarListaFilhosCod($cod_objeto)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarListaFilhosCod cod_objeto=".$cod_objeto);
        }

        $sql = "SELECT cod_objeto "
                . "FROM parentesco "
                . "WHERE cod_pai = ".$cod_objeto." "
                . "AND ordem = 1";
        $res = $this->container["db"]->execSQL($sql);
        while ($row = $res->FetchRow())
        {
            $result[] = $row['cod_objeto'];
        }
        return $result;
    }

    /**
     * Cria informações a serem testadas pelo SQL
     * @param string $str - String para criar as informações
     * @return string|array - Array com dados a serem testados
     */
    function criarInfoTeste($str)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::criarInfoTeste");
        }

        $result = array();
        if ($str == '') return $result;
        
        while (preg_match ("%(.*?)(&&|\|\|)(.*)%", $str, $passo_um))
        {
            $str = $passo_um[3];
            $array_exp[] = $passo_um[1];
            $array_exp[] = $passo_um[2];
        }
        
        $array_exp[] = $str;
        foreach ($array_exp as $exp)
        {

            if (preg_match("%(.+?)(>=|<=|<>|=|<|>|LIKE|ILIKE|\%)(.+)%is", $exp, $passo_dois))
            {
                $passo_dois[1] = trim ($passo_dois[1]);
                $passo_dois[2] = trim ($passo_dois[2]);
                $passo_dois[3] = trim ($passo_dois[3]);
                if ($this->ehMetadado($passo_dois[1]))
                {
                    // TODO: CORRIGIR CAONSULTA COM FILTRO DE DATA
                    if ($passo_dois[1] == 'data_publicacao' || $passo_dois[1] == 'data_validade' || $passo_dois[1] == 'data_exclusao')
                    {
//                        $passo_dois[1] = $this->container["db"]->Day($this->container["config"]->bd["tabelas"]["objeto"]["nick"].'.'.$passo_dois[1]);
                        $passo_dois[3] = str_pad(ConverteData($passo_dois[3],16), 14, "0", STR_PAD_RIGHT);
                    }
                    $passo_dois[1] = $this->container["config"]->bd["tabelas"]["objeto"]["nick"].'.'.$passo_dois[1];
                }
                if (preg_match("/\d{1,2}\/\d{1,2}\/\d{2,4}/", $passo_dois[3]))
                {
                    $passo_dois[3] = ConverteData($passo_dois[3],16);
                }

                $result[] = array($passo_dois[1], $passo_dois[2], $passo_dois[3]);
            }
            else
            {
                switch ($exp)
                {
                    case "&&":
                        $result[] = "AND";
                        break;
                    case "||":
                        $result[] = "OR";
                        break;
                    default:
                        $this->container["page"]->adicionarAviso("Operador ".$exp." desconhecido.",true);
                }
            }
        }
        
        return $result;
    }

    /**
     * Verifica se propriedade é metadado
     * @param string $teste - Nome da propriedade
     * @return boolean
     */
    function ehMetadado($teste)
    {
        // if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        // {
        //     x("adminobjeto::ehMetadado teste=".$teste);
        // }
//        xd($this->container["db"]->metadados);
        if (strpos($teste, '.'))
        {
            $teste = substr($teste, strpos($teste, '.') + 1);
        }
        // xd($this->container["db"]->getMetadados());
        if (in_array($teste, $this->container["db"]->getMetadados())) return true;

        if (strpos($teste,'objeto.') || strpos($teste, $this->container["config"]->bd["tabelas"]['objeto']["nick"].".")) return true;
        return false;
    }

    /**
     * Localiza objetos no banco de dados
     * @param string $classe - Classes, separado por virgula
     * @param string $qry - Query para filtro, condição
     * @param string $ordem - Campos para ordenar, separados por virgula
     * @param int $inicio - Registro inicial para paginação
     * @param int $limite - Total de registros para paginação
     * @param int $pai - codigo do objeto pai
     * @param int $niveis - Nivel de objetos para trazer
     * @param bool $apagados - Trazer objetos apagados também
     * @param string $likeas - Condição like
     * @param string $likenocase - Condição ilike
     * @param string $tags - TAGS dos objetos a buscar
     * @return boolean|\Objeto
     */
    function localizarObjetos($classe, $qry, $ordem='', $inicio=-1, $limite=-1, $pai=-1, $niveis=-1, $apagados=false, $likeas='', $likenocase='', $tags='')
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::localizarObjetos classe=".$classe);
        }
        // x("adminobjeto::localizarObjetos classe=".$classe);

        if (!isset($classe) || $classe==null || $classe=='') return false;
		
        $array_qry = $this->criarInfoTeste($qry);
        $pai_join = $this->criarSQLParentesco($pai, $niveis);
        $usuario_where = $this->criarCondicaoUsuario();
        $tags_join = "";
        $tags_where = "";
        $tags_temp = "";
        $apagado_where = "";
        
        if ($tags != "")
        {
            $array_tags = preg_split("[,]", $tags);
            $tags_join .= " INNER JOIN ".$this->container["config"]->bd["tabelas"]['tagxobjeto']["nome"]." ".$this->container["config"]->bd["tabelas"]['tagxobjeto']['nick']." "
            . " ON ".$this->container["config"]->bd["tabelas"]['objeto']["nick"].".".$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]['tagxobjeto']["nick"].".".$this->container["config"]->bd["tabelas"]['tagxobjeto']["colunas"]["cod_objeto"]." "
            . " INNER JOIN ".$this->container["config"]->bd["tabelas"]['tag']["nome"]." ".$this->container["config"]->bd["tabelas"]['tag']["nick"]." "
            . " ON ".$this->container["config"]->bd["tabelas"]['tagxobjeto']['nick'].".".$this->container["config"]->bd["tabelas"]['tagxobjeto']['colunas']["cod_tag"]." = ".$this->container["config"]->bd["tabelas"]['tag']["nick"].".".$this->container["config"]->bd["tabelas"]['tag']['colunas']["cod_tag"]." ";
            $tags_where .= " AND ( ";
            foreach ($array_tags as $tag)
            {
                $tags_temp .= " OR ".$this->container["config"]->bd["tabelas"]['tag']["nick"].".".$this->container["config"]->bd["tabelas"]['tag']["colunas"]["nome_tag"]." = '".trim($tag)."' ";
            }
            $tags_where .= substr($tags_temp, 3);
            $tags_where .= ") ";
        }
        
        // Deve buscar objetos apagados?
        if (!$apagados) $apagado_where = " AND (".$this->container["config"]->bd["tabelas"]['objeto']["nick"].".".$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["apagado"]." <> 1) ";
        
        $cod_classe_array = array();
        
        // Se ordem não tiver sido informada, ordena por peso
        if ($ordem=='') $ordem = array('peso');
        else
        {
            if (!is_array($ordem)) $ordem = explode (",", $ordem);
        }
        
        if(!$likeas=='')
        {
            $like_as = " AND ".$this->container["config"]->bd["tabelas"]['objeto']["nick"].".".$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["titulo"]." LIKE '".$likeas."' ";
        }
        // Além de perguntar sobre 'ilike', também garante que só um LIKE será usado na Query (caso programador tente usar LIKE e iLIKE na mesma chamada)
        if((!$likenocase=='') || ((!$likeas=='') && (!$likenocase=='')))
        {
            $like_as = " AND ".$this->container["config"]->bd["tabelas"]['objeto']["nick"].".".$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["titulo"]." ILIKE '".strtolower($likenocase)."' ";
        }
        
        // Verifica se tem propriedade na ordem
        $tem_propriedade_na_ordem = false;
        foreach ($ordem as $key=>$item)
        {
            if ($item[0]=='-')
            {
                $array_ordem[$key]['orientacao'] = ' desc ';
                $array_ordem[$key]['campo'] = substr($item,1);
            }
            elseif ($item[0]=='+')
            {
                $array_ordem[$key]['campo'] = substr($item,1);
                $array_ordem[$key]['orientacao'] = ' asc ';
            }
            else $array_ordem[$key]['campo'] = $item;
            if (!$this->ehMetadado($array_ordem[$key]['campo'])) $tem_propriedade_na_ordem = true;
        }
        
        
        // Verifica se tem propriedade na query
        $tem_propriedade_na_qry = false;
        //x($array_qry);
        foreach ($array_qry as $condicao)
        {
            if (!is_array($condicao))
            {
                continue;
            }
            //x($condicao);
            //x($this->ehMetadado($condicao[0]));
            if (!$this->ehMetadado($condicao[0])) $tem_propriedade_na_qry = true;
        }
        
        // Prepara SQL para as classes
        $multiclasse = false; //Classe única. Nesse caso NÃO é preciso criar a temp table
        $todas_as_classes = false;
        if ($classe=='*')
        {
            $todas_as_classes = true;
            $multiclasse = true; // usa temp table
        }
        else
        {
            if (!is_array($classe)) $classe = explode (",",strtolower($classe));
            if (count($classe)>1) $multiclasse = true; // usa temp table
        }
        // x("localizarOb0jetos 800");
        // x($classe);
        $classes = $this->codigosClasses($classe);
        // x($classes);
        
        if ($tem_propriedade_na_ordem || ($multiclasse && $tem_propriedade_na_qry))
        {
            //xd($tem_propriedade_na_qry);
            if (!isset($classes_where)) $classes_where = "";
            $sql_out = $this->localizarObjetosTabelaTemporaria ($classes, $array_qry, $array_ordem, $apagado_where.$tags_where.$usuario_where.$classes_where, $pai_join.$tags_join);
            $sqlfinal = $sql_out['tbl'].$sql_out['ordem'];
        }
        else
        {
            $sql_out = $this->localizarObjetosTabela ($classes, $array_qry, $array_ordem);
            $classes_where = "";
            if (isset($sql_out['classes']) && is_array($sql_out['classes']))
            {
                $classes_where = ' and '.$this->container["db"]->criarTeste($this->container["config"]->bd["tabelas"]['objeto']["nick"].'.'.$this->container["config"]->bd["tabelas"]['objeto']["colunas"]["cod_classe"], $sql_out['classes']);
            }
            $sqlfinal = "select ".$this->container["db"]->getSqlObjSel();
            if (isset($sql_out['campos'])) $sqlfinal .= $sql_out['campos'];
            $sqlfinal .= $this->container["db"]->getSqlObjFrom().$pai_join.$tags_join;
            if (isset($sql_out['from'])) $sqlfinal .= $sql_out['from'];
            $sqlfinal .= ' where (1=1)'.$apagado_where.$tags_where;
            if (isset($sql_out['where'])) $sqlfinal .= $sql_out['where'];
            $sqlfinal .= $usuario_where.$classes_where;
            if (isset($like_as)) $sqlfinal .= $like_as;
            if (isset($sql_out['ordem'])) $sqlfinal .= $sql_out['ordem'];
        }
		
        $res = $this->container["db"]->execSQL($sqlfinal, $inicio, $limite);
        $row = $res->GetRows();
		
        $objetos = array();

        // Vai criando objetos Objeto e populando array
        for ($i=0; $i<sizeof($row); $i++)
        {
//            xd($_SESSION['usuario']);
            if ($_SESSION['usuario']['perfil'] < _PERFIL_MILITARIZADO || ($_SESSION['usuario']['perfil']==_PERFIL_DEFAULT || $_SESSION['usuario']['perfil']==_PERFIL_MILITARIZADO) && ($row[$i]["data_publicacao"]<=date("YmdHi")."00" && $row[$i]["data_validade"]>=date("YmdHi")."00"))
            {
                $obj = new Objeto($this->container);
                $obj->povoar($row[$i]);
                if (!in_array($obj, $objetos)) $objetos[] = $obj;
            }
        }
		
        // Apaga tabela temporária caso tenha sido utilizada
//        if (isset($sql_out['tbl']) && $sql_out['tbl'] != '')
//        {
//            $this->container["db"]->DropTempTable($sql_out['tbl']);
//        }

        return $objetos;
    }

    /**
     * Busca códigos das classes e retorna em array
     * @param array $classes - Array com prefixos das classes
     * @return array - Array com codigos das classes
     */
    function codigosClasses($classes)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::codigosClasses");
        }

        $this->carregarClasses();
        $saida=array();

        
        // xd($_SESSION['classes']);
        if ($classes=='*')
        {
            return $_SESSION['classesNomes'];
        }
        else
        {
            foreach ($classes as $nome)
            {
                
                if (isset($_SESSION['classesNomes'][strtolower(trim($nome))])) $saida[] = $_SESSION['classesNomes'][strtolower($nome)];
                else
                {
                    if (isset($_SESSION['classesPrefixos'][strtolower(trim($nome))])) $saida[] = $_SESSION['classesPrefixos'][strtolower($nome)];
                }
            }
        }
        return $saida;
    }

    /**
     * Carrega as classes do portal e guarda em session
     */
    function carregarClasses()
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::carregarClasses");
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
     * Localiza objetos usando tabela temporária
     * @param array $classes - Array com codigos das classes
     * @param array $array_qry - Array com query
     * @param array $array_ordem - Array com propriedades para ordenar
     * @param string $default_where - Where default
     * @param string $pai_join - Sql com join para pai já montado
     * @return array
     */
    function localizarObjetosTabelaTemporaria ($classes, $array_qry, $array_ordem, $default_where, $pai_join)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::localizarObjetosTabelaTemporaria");
        }
        // Variavel para controlar a criacao dos campos na tabela temporaria //
        $campo_incluido = array();
        $campo_incluido_natabela = array();
        $ordem_temporaria = array();
		
        $sqls_insert = array();
		
        foreach ($classes as $cod_classe)
        {
            
            $temp_campos = array();
            $temp_from = array();
            $temp_where = array();
            $campo_incluido = array();

            // xd($array_ordem);
			
            //Constroi SQL para casos em que existem propriedades na ordem
            foreach ($array_ordem as $item)
            {
                if (!isset($item['orientacao'])) $item['orientacao'] = "ASC";
                $string_temp = "";
                
                if (!$this->ehMetadado($item['campo']))
                {
                    $info = $this->criarSQLPropriedade($item['campo'], $item['orientacao'], $cod_classe);
                    
                    $temp_campos[] = $info['field'];
                    $temp_campos[] = $info['fieldordem'];
                    $temp_from[] = $info['from'];
                    $temp_where[] = $info['where'];
                    $campo_incluido[] = $info['field'];
                    
                    if ($info["tabela"] == "tbl_objref") $item['campo'] .= "_ref___2";
                    else $item['campo'] .= "___2";

                    $string_temp = $item['campo'].' '.$item['orientacao'];
                }
                else
                {
                    if ($item['campo']=="classe")
                    {
                        $string_temp = $this->container["config"]->bd["tabelas"]["classe"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"];
                    }
                    elseif ($item['campo']=="pele")
                    {
                        $string_temp = $this->container["config"]->bd["tabelas"]["pele"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["nome"];
                    }
                    elseif ($item['campo']=="prefixopele")
                    {
                        $string_temp = $this->container["config"]->bd["tabelas"]["pele"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["prefixo"];
                    }
                    elseif ($item['campo']=="status")
                    {
                        $string_temp = $this->container["config"]->bd["tabelas"]["status"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["status"]["colunas"]["nome"];
                    }
                    else
                    {
                        $string_temp = $item['campo'];
                    }
                    if (isset($item['orientacao'])) $string_temp .= " ".$item['orientacao'];
                    
                }
                if ($string_temp != "" && !in_array($string_temp, $ordem_temporaria)) $ordem_temporaria[] = $string_temp;

                //xd($this->container["config"]->bd["tabelas"]);
                /*
                
            $result['ordem'][]= $temp_array;
                */
                
                
            }

            //Constroi SQL para casos em que existem propriedades na condicao
            foreach ($array_qry as $condicao)
            {
                if (!is_array($condicao))
                {
                    $out['where'] .= ' '.$condicao;
                } 
                else 
                {
                    if ($this->ehMetadado($condicao[0]))
                    {
                        if (preg_match('/floor/',$condicao[0])) {
                            $condicao[0]=str_replace('objeto.','',$condicao[0]);
                        }
                        $temp_where[]=' ('.$condicao[0]." ".$condicao[1]." '".$condicao[2]."')";
                    }
                    else
                    {
                        $info = $this->criarSQLPropriedade($condicao[0],"", $cod_classe);
                        $temp_campos[]=$info['fieldordem'];
                        $temp_from[]=$info['from'];
                        $temp_where[]=$info['where'];
                        $campo_incluido[]=$info['field'];
			
                        $temp_where[]= ' ('.$info['field']." ".$condicao[1]." ".$info['delimitador'].$condicao[2].$info['delimitador'].')';                   
                    }
                }
            }
			//fim
            $campos=','.implode(',', $temp_campos);
            $from = implode(' ', $temp_from);
            $where = implode(' AND ', $temp_where);
			
            $sqls_insert[] = " SELECT ".$this->container["db"]->getSqlObjSel().$campos.$this->container["db"]->getSqlObjFrom().$pai_join.$from.' WHERE (1=1) AND '.$where.$default_where;
			//$this->container["db"]->execSQL($sql);

        }
		
//        $sqlCreate = $this->container["db"]->tipodados["temp"]." ".$this->container["db"]->tipodados["temp2"].$tbl["nome"]." (".implode(", ", $tbl["colunas"]).")";
//        $this->container["db"]->execSQL($sqlCreate);
	
        $sqlFinal = "SELECT * FROM ( "
                . join(PHP_EOL." UNION ALL ".PHP_EOL, $sqls_insert)
                . " ) tabletemp ";
        
//        xd($sqlFinal);
        
//        foreach($sqls_insert as $sqls)
//        {
//            $this->container["db"]->execSQL($sqls);
//        }

        $result['tbl'] = $sqlFinal;
        $result['ordem']=' ORDER BY '.implode(',', $ordem_temporaria);
			
        return $result;
    }

    
    /**
     * Localizar objetos sem utilização de tabela temporária
     * @param array $classes - Array com codigos das classes
     * @param array $array_qry - Array com condicoes da query
     * @param array $array_ordem - Array com propriedades para ordenação
     * @return array - Dados da consulta
     */
    function localizarObjetosTabela($classes, $array_qry, $array_ordem)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::localizarObjetosTabela");
        }
        //x($this->container["config"]->bd["tabelas"]["objeto"]["colunas"]);
        
        foreach ($array_ordem as $item)
        {
            if ($item['campo']=="classe")
            {
                $temp_array = $this->container["config"]->bd["tabelas"]["classe"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"];
            }
            elseif ($item['campo']=="pele")
            {
                $temp_array = $this->container["config"]->bd["tabelas"]["pele"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["nome"];
            }
            elseif ($item['campo']=="prefixopele")
            {
                $temp_array = $this->container["config"]->bd["tabelas"]["pele"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["pele"]["colunas"]["prefixo"];
            }
            elseif ($item['campo']=="status")
            {
                $temp_array = $this->container["config"]->bd["tabelas"]["status"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["status"]["colunas"]["nome"];
            }
            else
            {
                $temp_array = $this->container["config"]->bd["tabelas"]["objeto"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["objeto"]["colunas"][$item['campo']];
            }
            if (isset($item['orientacao'])) $temp_array .= $item['orientacao'];
            $result['ordem'][]= $temp_array;
            if (!$this->ehMetadado($item['campo']))
            {
                $result['campos'][]=$item['campo'];
            }
        }
		
        foreach ($classes as $cod_classe)
        {
            $input = array();
            $input = $this->criarSQLCondicao($array_qry, $cod_classe);
            if (isset($input) && is_array($input) && count($input)>0 && ($input['where']!="" || $input['from']!=""))
            {
                $result['where'][] = $input['where'];
                $result['from'][] = $input['from'];
            }
            $result['classes'][] = $cod_classe;
        }

        if (isset($result['where']) && is_array($result['where']))
        {
            $result['where']=' AND '.implode(' AND ', $result['where']);
        }

        if (isset($result['campos']) && is_array($result['campos'])) $result['campos']=implode(',',$result['campos']);

        if (isset($result['from']) && is_array($result['from']))
        {
            $sep_temp='';
            $saida_from='';
            foreach ($result['from'] as $cada_from)
            {
                if ($cada_from)
                {
                    $saida_from=$sep_temp.$cada_from;
                    $sep_temp=',';
                }
            }
            $result['from']=$saida_from;
        }
        if (is_array($result['ordem'])) $result['ordem']=' order by '.implode(',', $result['ordem']);
        return $result;
    }

    /**
     * Cria condições SQL com base no array query recebido
     * @param array $array_qry - Array com condições query
     * @param int $cod_classe - Código da classe
     * @return string
     */
    function criarSQLCondicao($array_qry, $cod_classe)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::criarSQLCondicao");
        }

        $out = array("where"=>"", "from"=>"", "condicao"=>array());
        foreach ($array_qry as $condicao)
        {
            if (!is_array($condicao))
            {
                $out['where'] .= ' '.$condicao;
            }
            else
            {
                if ($this->ehMetadado($condicao[0]))
                {
                    $condicao[0] = str_replace($this->container["config"]->bd["tabelas"]["objeto"]["nick"].'.(','(',$condicao[0]);
                    $condicao[0] = str_replace($this->container["config"]->bd["tabelas"]["objeto"]["nick"].'.','',$condicao[0]);
                    if ($condicao[0] == "classe")
                    {
                        $out['where'] .= ' '.$this->container["config"]->bd["tabelas"]["classe"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"].' '.$condicao[1]." '".$condicao[2]."'";
                    }
                    else
                    {
                        $out['where'] .= ' '.$this->container["config"]->bd["tabelas"]["objeto"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["objeto"]["colunas"][$condicao[0]].' '.$condicao[1]." '".$condicao[2]."'";
                    }
                }
                else
                {
                    $temp = $this->criarSQLPropriedade($condicao[0], "", $cod_classe);
                    if (!strpos($out['from'], $temp['from'])) $out['from'] .= ' '.$temp['from'];
                    $out['condicao'][] = $condicao[0];
                    $out['where'] .= ' ('.$temp['where'].' AND '.$temp['field']." ".$condicao[1]." ".$temp['delimitador'].$condicao[2].$temp['delimitador'].')';
                }
            }
        }
        return $out;
    }

    /**
     * Localiza objetos com publicação pendente
     * @param int $cod_pai - Codigo do objeto pai
     * @param int $cod_usuario - Codigo do usuario
     * @param string $ord1 - Coluna para ordenação
     * @param string $ord2 - tipo de ordenação, asc ou desc
     * @param int $inicio - Primeiro registro a ser retornado para paginação
     * @param int $limite - Numero de registros para paginação
     * @return Array Objetos
     */
    function localizarPendentes($cod_pai, $cod_usuario, $ord1, $ord2, $inicio=-1, $limite=-1)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::localizarPendentes");
        }

        $sql_pendentes = "SELECT "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." AS titulo "
                . " FROM ".$this->container["config"]->bd["tabelas"]["pendencia"]["nome"]." ".$this->container["config"]->bd["tabelas"]["pendencia"]["nick"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["pendencia"]["nick"].".".$this->container["config"]->bd["tabelas"]["pendencia"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["pendencia"]["nick"].".".$this->container["config"]->bd["tabelas"]["pendencia"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$cod_pai." "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["apagado"]." = 0 "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"][$ord1]." ".$ord2;
        $rs = $this->container["db"]->execSQL($sql_pendentes, $inicio, $limite);
        return $rs->GetRows(); 
    }
	
    /**
     * Cria SQL de condição de acordo com nível do usuário
     * @return string - SQL com condições
     */
    function criarCondicaoUsuario()
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::criarCondicaoUsuario");
        }

        $sql_condicao = "";
//        xd($_SESSION['usuario']);
        switch ($_SESSION['usuario']['perfil'])
        {
            case _PERFIL_DEFAULT:
                $sql_condicao = $this->criarCondicaoPublicado().$this->criarCondicaoData();
                break;
            case _PERFIL_AUTOR:
                //$sql_condicao=$this->criarCondicaoAutor();
                //$sql_condicao=$this->criarCondicaoData($page);
                break;
            case _PERFIL_RESTRITO:
                $sql_condicao=$this->criarCondicaoPublicado().$this->criarCondicaoData();
                break;
            case _PERFIL_MILITARIZADO:
                $sql_condicao=$this->criarCondicaoData();
                break;
            case _PERFIL_ADMINISTRADOR:
                //$sql_condicao=$this->criarCondicaoData($page);
                break;
            default:
                //$sql_condicao = $this->criarCondicaoPublicado($page).$this->criarCondicaoData($page);
                //$sql_condicao = $this->criarCondicaoData($page);
                break;
        }

        return $sql_condicao;
    }
    
    /**
     * Retorna array com codigos, titulos e url_amigavel dos objetos até o root
     * @param int $cod_objeto - Codigo do objeto 
     * @param int $nivel
     * @param array $excecoes - codigos dos objetos que não devem vir no array
     * @param array $excecoes_classes - codigos das classes que não devem vir no array
     * @param boolean $desc - informa se o array deve ser ordenado de forma descendente
     * @return array
     */
    function pegarParentescoCompleto($cod_objeto, $nivel, $excecoes=array(), $excecoes_classes=array(), $desc=false)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarParentescoCompleto");
        }

        $rtnLista = array();
        $contador = 0;
        
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." AS cod_pai, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["titulo"]." AS titulo, "
                . " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["url_amigavel"]." AS url_amigavel "
                . " FROM ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"]." "
                . " INNER JOIN ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        if (count($excecoes_classes)>0) { 
            $sql .= " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"]." NOT IN (". implode(",", $excecoes_classes).") "; 
        }
        $sql .= " ORDER BY ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["ordem"]." ";
        if ($desc) { $sql .= " DESC"; }
        
        $res = $this->container["db"]->execSQL($sql);
        while ($row = $res->FetchRow())
        {
            $arrCodeTitulo = array($row['cod_pai'] => array($row['titulo'], $row['url_amigavel']));
            if (($contador < $nivel) && !(in_array($row['cod_pai'], $excecoes)))
            {
                array_push_associative($rtnLista, $arrCodeTitulo);
                $contador = $contador + 1;
            }
        }

        return $rtnLista;
    }



    /**
     * Pega nome da classe com base no código
     * @param type $cod_classe
     * @return type
     */
    function pegarNomeClasse($cod_classe)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarNomeClasse cod_classe=".$cod_classe);
        }

        $this->carregarClasses();
        if (isset($_SESSION["classes"][$cod_classe]))
        {
            return array(
                "nome" => $_SESSION["classes"][$cod_classe]["nome"],
                "prefixo" => $_SESSION["classes"][$cod_classe]["prefixo"]
            );
        }

        // $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["nome"]." as nome, "
        //         . " ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["prefixo"]." as prefixo "
        //         . " FROM ".$this->container["config"]->bd["tabelas"]["classe"]["nome"]." ".$this->container["config"]->bd["tabelas"]["classe"]["nick"]." "
        //         . " WHERE ".$this->container["config"]->bd["tabelas"]["classe"]["nick"].".".$this->container["config"]->bd["tabelas"]["classe"]["colunas"]["cod_classe"]." = ".$cod_classe; 
        // $rs = $this->container["db"]->execSQL($sql);
        // return $rs->fields;
        return false;
    }

    /**
     * Cria SQL para busca de propriedades
     * @param type $campo
     * @param type $direcao
     * @param type $cod_classe
     * @return string
     */
    function criarSQLPropriedade($campo, $direcao, $cod_classe)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::criarSQLPropriedade campo=".$campo);
        }

        $info = $this->pegarInfoPropriedade($cod_classe, $campo);
        $montagem = array();
		
        if ($info!=null && $info!='')
        {
            $montagem["tabela"] = $this->container["config"]->bd["tabelas"][$info['tabela']]["nome"];
            $montagem['from'] = " LEFT JOIN ".$this->container["config"]->bd["tabelas"][$info['tabela']]["nome"]." ".$campo." ON ";
            $on = " ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." = ".$campo.".".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_objeto"]." ";
            $montagem['type'] = $info['nome'];
            $montagem['field'] = "";
            $montagem['fieldordem'] = "";
            if ($info['tabela']=='tbl_objref')
            {
                $montagem['from'] .= ' (('.$on.') AND ('.$campo.'.'.$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_propriedade"].' = '.$info['cod_propriedade'].')) ';
                $montagem['where'] = ' (1 = 1) AND '.$this->container["config"]->bd["tabelas"]["objeto"]["nick"].'.'.$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_classe"].' = '.$cod_classe;
                $montagem['from'] .= " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$campo."_ref ON ".$campo.".".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["valor"]." = ".$campo."_ref.".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." ";
                if (!$this->ehMetadado($info['campo_ref']))
                {
                    $propriedade = $this->pegarInfoPropriedade($info['cod_referencia_classe'], $info['campo_ref']);
                    //$montagem['from'] .= '(('.$on.') and ('.$campo."_property.cod_propriedade=".$propriedade['cod_propriedade'].'))';
                    $montagem['from'] .= " LEFT JOIN ".$propriedade['tabela']." as ".$campo."_campo_ref on ".$campo.'_ref.cod_objeto='.$campo.'_property.cod_objeto';
                    $montagem['field'] .= $campo."_property.valor";
                    $montagem['fieldordem'] .= $campo."_property.valor AS ".$campo."_property___2";
                    $montagem['delimitador']=$propriedade['delimitador'];
                    //$montagem['where'] .= $campo."_property.cod_propriedade=".$propriedade['cod_propriedade'];
                }
                else
                {
//                    xd($info['tabela']);
                    //$montagem['from'] .= '(('.$on.') and ('.$campo.'.cod_propriedade='.$info['cod_propriedade'].'))';
                    $montagem['field'] .=  $campo."_ref.".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"][$info['campo_ref']];
                    $montagem['fieldordem'] .=  $campo."_ref.".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"][$info['campo_ref']]." AS ".$campo."_ref___2";
                    $montagem['delimitador']="'";
                }
                // x($montagem);
            }
            else
            {
                $montagem['from'] .= $on;
                $montagem['where'] = $campo.".".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["cod_propriedade"]." = ".$info['cod_propriedade'];
                $montagem['field'] .= $campo.".".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["valor"];
                $montagem['fieldordem'] .= $campo.".".$this->container["config"]->bd["tabelas"][$info['tabela']]["colunas"]["valor"]." AS ".$campo."___2";
                $montagem['delimitador']="'";
            }
        }
        else
        {
            $ClasseNome = $this->pegarNomeClasse($cod_classe);
            $this->container["page"]->adicionarAviso("Classe ".$ClasseNome['nome']." n&atilde;o tem propriedade $campo.",true);
        }
        return $montagem;
    }

    /**
     * Busca no banco de dados informações dobre propriedade
     * @param type $cod_classe
     * @param type $prop
     * @return type
     */
    function pegarInfoPropriedade($cod_classe, $prop)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarInfoPropriedade cod_classe=".$cod_classe." prop=".$prop);
        }

        $tabelas = $this->container["config"]->bd["tabelas"];

        // Removendo parenteses para verificacao da propriedade
//                $prop = preg_replace("[\(|\)]", "", $prop);

        $sql = "SELECT ".$tabelas["propriedade"]["nick"].".".$tabelas["propriedade"]["colunas"]["cod_propriedade"]." AS cod_propriedade, "
                . " ".$tabelas["tipodado"]["nick"].".".$tabelas["tipodado"]["colunas"]["nome"]." AS nome, "
                . " ".$tabelas["tipodado"]["nick"].".".$tabelas["tipodado"]["colunas"]["tabela"]." AS tabela, "
                . " ".$tabelas["propriedade"]["nick"].".".$tabelas["propriedade"]["colunas"]["cod_referencia_classe"]." AS cod_referencia_classe, "
                . " ".$tabelas["propriedade"]["nick"].".".$tabelas["propriedade"]["colunas"]["campo_ref"]." AS campo_ref, "
                . " ".$tabelas["tipodado"]["nick"].".".$tabelas["tipodado"]["colunas"]["delimitador"]." AS delimitador "
                . " FROM ".$tabelas["propriedade"]["nome"]." ".$tabelas["propriedade"]["nick"]." "
                . " INNER JOIN ".$tabelas["tipodado"]["nome"]." ".$tabelas["tipodado"]["nick"]." "
                    . " ON ".$tabelas["propriedade"]["nick"].".".$tabelas["propriedade"]["colunas"]["cod_tipodado"]." = ".$tabelas["tipodado"]["nick"].".".$tabelas["tipodado"]["colunas"]["cod_tipodado"]." "
                . " WHERE ".$tabelas["propriedade"]["nick"].".".$tabelas["propriedade"]["colunas"]["cod_classe"]." = ".$cod_classe." ";

        if (!intval($prop))
        {
            $sql .=" AND ".$tabelas["propriedade"]["nick"].".".$tabelas["propriedade"]["colunas"]["nome"]." = '".$prop."' ";
        }
        else
        {
            $sql .=" AND ".$tabelas["propriedade"]["nick"].".".$tabelas["propriedade"]["colunas"]["cod_propriedade"]." = ".$prop." ";
        }

        $rs = $this->container["db"]->execSQL($sql);
        $return = $rs->fields;
        return $return;
    }

//	function Limites($inicio,$limite)
//	{
//		if ($limite!="")
//		{
//			$result=" limit ".intval($inicio).",$limite";
//		}
//		else
//		{
//			if ($inicio)
//			$result=" limit $inicio";
//		}
//		return $result;
//	}

    /**
     * Cria SQL de associação com objetos pai
     * @param type $pai
     * @param type $niveis
     * @param string $campo
     * @return string
     */
    function criarSQLParentesco($pai, $niveis, $campo="objeto.cod_pai")
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::criarSQLParentesco pai=".$pai." niveis=".$niveis);
        }

        $return = "";
        if ($campo === "objeto.cod_pai")
        {
            $campo = $this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_pai"];
        }
        if ($pai!=-1)
        {
            $return = " INNER JOIN ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"]." "
                        . " ON (".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." "
                            . " AND ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$pai." ";
//				on (".$this->container["db"]->nomes_tabelas["parentesco"].".cod_objeto = ".$this->container["db"]->nomes_tabelas["objeto"].".cod_objeto 
//				and ".$this->container["db"]->nomes_tabelas["parentesco"].".cod_pai=".$pai;
            if ($niveis>=0)
            {
                $return.= " AND ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["ordem"]." <= ".($niveis+1).')';
            }
            else
            {
                $return .=')';
            }
        }
        return $return;
    }

//        /**
//         * Retorna lista de códigos dos objetos filhos
//         * @param type $page
//         * @param type $pai
//         * @param type $niveis
//         * @return type
//         */
//	function PegaIDFilhos(&$page, $pai, $niveis)
//	{
//            $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." AS cod_objeto "
//                    . " FROM ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." "
//                    . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$pai;
//            $res = $this->container["db"]->execSQL($sql);
//            while ($row = $res->FetchRow())
//            {
//                $list[]=$row['cod_objeto'];
//            }
//            return $list;
//	}
	
    
    /**
     * Retorna número de filhos de determinado objeto
     * @param type $pai
     * @return type
     */
    function pegarNumFilhos($pai)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::pegarNumFilhos pai=".$pai);
        }
        $sql = "SELECT COUNT(*) AS total "
                . " FROM ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"]." "
                . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["objeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." = ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["cod_objeto"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["nick"].".".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$pai." "
                . " AND ".$this->container["config"]->bd["tabelas"]["objeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["objeto"]["colunas"]["apagado"]." <> 1";
        $res = $this->container["db"]->execSQL($sql);
        return $res->fields["total"];
    }

    /**
     * Identifica de determinado objeto é filho de outro
     * @param type $cod_objeto
     * @param type $cod_pai
     * @return type
     */
    function ehFilho($cod_objeto, $cod_pai)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::ehFilho cod_objeto=".$cod_objeto." pai=".$pai);
        }

        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." AS cod_objeto "
                . " FROM ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$cod_pai." "
                . " AND ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $res = $this->container["db"]->execSQL($sql);
        return !$res->EOF;
    }

//	function ShowObjectResume(&$page, $cod_objeto)
//	{
//		$obj = $this->criarObjeto($page, $cod_objeto);
//		foreach ($obj as $key => $quadro)
//		{
//			if ($key == 'CaminhoObjeto')
//				$arrCaminho = $quadro;
//		}
//		//echo $obj['CaminhoObjeto']."<br>";
//		//var_dump_pre($cod_objeto);
//		return array('url'=>$obj->valor('url'), 'titulo'=>$obj->valor('titulo'), 'descricao'=>$obj->valor('descricao'), 'codigo'=>$obj->valor('cod_objeto'),'caminho'=>$arrCaminho);
//	}

    function enviarEmailSolicitacao($cod_chefia, $cod_objeto,$mensagemsubmetida)
    {
        global $PORTAL_NAME;
        include('email.class.php');
        $arrInfoUsuario = $this->container["usuario"]->pegarInformacoesUsuario($cod_chefia);
        $arrInfoDadosObjeto = $this->container["adminobjeto"]->pegarDadosObjetoId($cod_objeto);

        $texConteudo = "<font align=\"left\">Esta mensagem &eacute; para informar a solicita&ccedil;&atilde;o de publica&ccedil;&atilde;o de objetos por parte do usu&aacute;rio <b>".$_SESSION['usuario']['nome']."</b> dentro do ".$PORTAL_NAME.".
        <br>
        Voc&ecirc; deve efetuar login no sistema, utilizando seu usu&aacute;rio e senha, <a href=\"".$this->container["config"]->portal["url"]."/login\">clicando aqui</a>. Dentro das <b>Op&ccedil;&otilde;es de Menu</b>, localize o bot&atilde;o de <i>objetos aguardando aprova&ccedil;&atilde;o</i>.
        <br><br>
        Os dados do objeto seguem:<br>
        <br>
        <br>
        <b>Mensagem de solicita&ccedil;&atilde;o:</b> ".$mensagemsubmetida."<br><b>T&iacute;tulo do Objeto:</b> ".$arrInfoDadosObjeto['titulo']." <i>[".$arrInfoDadosObjeto['cod_objeto']."]</i> <br>
        <b>Data de Validade:</b> ".ConverteData($arrInfoDadosObjeto['data_validade'],1)."<br>
        <b>Data de Publica&ccedil;&atilde;o:</b> ".ConverteData($arrInfoDadosObjeto['data_publicacao'],1)."<br>
        <b>Classe utilizada:</b> ".$arrInfoDadosObjeto['classe']."<br>
        <br><br>
        Caso seja necess&aacute;rio, os dados do solicitante s&atilde;o descritos abaixo:
        <br><br>
        <b>Nome do Solicitante:</b> ".$_SESSION['usuario']['nome']."<br>
        <b>E-mail do Solicitante:</b> ".$_SESSION['usuario']['email']."<br>
        <b>Telefone de contato:</b> - n&atilde;o cadastrado -<br>
        <br><br><br><br>
        <b>Esta mensagem &eacute; autom&aacute;tica e n&atilde;o deve ser respondida.</b>
        <br><br>
        <center>".$this->container["config"]->portal["nome"]."</center></font>";

        $destinatario = $arrInfoUsuario['nome']." <".$arrInfoUsuario['email'].">";
        $remetente =  _PORTAL_EMAIL;

        $conteudoCompleto = "<html><body style='margin:0; padding:0;'>".
                                              "<center>".
                                              "<BR>Caro Sr(a), ".$arrInfoUsuario['nome']."<BR><BR>".
                                              "$texConteudo".
                                              "<BR></body></html>";

        $email = new Email($remetente ,$destinatario, "Solicitacao de Publicacao" , $conteudoCompleto);

    //** send a copy of this file in the email. (nï¿½o nescessï¿½rio)
      //$email->Attach(__FILE__, "text/plain");


    //** attach this included image file.

      //$email->Attach("informect/images/logo_ct2.gif", "image/gif");
      //$email->Attach("informect/images/brasil2.gif", "image/gif");
      //$email->Attach("informect/images/moldura_dest_dir.gif", "image/gif");
      //$email->Attach("informect/images/bottom_informe.gif", "image/gif");
      //$email->Attach("informect/images/headre_informe.jpg", "image/gif");

        $wassent = $email->Send();
        return $wassent;
    }

    /**
     * Executa scripts antes de depois de gravar objetos
     * @param integer $codClasse - Código da classe
     * @param integer $codPele - Código da pele
     * @param string $codTexto - Antes ou depois de gravar o objeto
     */
    function executarScript($codClasse=0, $codPele=0, $codTexto="antes")
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::executarScript");
        }

        $cod_classe = (int)htmlspecialchars($codClasse, ENT_QUOTES, "UTF-8");
        $cod_pele = (int)htmlspecialchars($codPele, ENT_QUOTES, "UTF-8");
        
        $ClasseUtilizada = $this->pegarNomeClasse($cod_classe);
        $PeleUtilizada = $this->container["administracao"]->pegarListaPeles($cod_pele);
        
        if (count($PeleUtilizada) == 1 && file_exists($_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$PeleUtilizada[0]['prefixo']."_".$ClasseUtilizada['prefixo']."_".$codTexto.".php"))	
        {
                include($_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$PeleUtilizada['prefixo']."_".$ClasseUtilizada['prefixo']."_".$codTexto.".php");
        }
        elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$ClasseUtilizada['prefixo']."_".$codTexto.".php"))
        {
                include($_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$ClasseUtilizada['prefixo']."_".$codTexto.".php");
        }
    }



    /**
     * Identifica se determinado objeto está sob área protegida
     * @param type $cod_objeto
     * @return boolean
     */
    function estaSobAreaProtegida($cod_objeto=-1)
    {
        if (isset($this->container["config"]->portal["debug"]) && $this->container["config"]->portal["debug"] === true)
        {
            x("adminobjeto::estaSobAreaProtegida cod_objeto=".$cod_objeto);
        }

        $protegido = false;
        $caminho2 = $this->container["objeto"]->caminhoObjeto;
        $objBlob = clone $this->container["objeto"];
        $caminho = is_array($caminho2)?$caminho2:array();

        if ($cod_objeto != -1)
        {
            $caminho = $this->recursivaCaminhoObjeto($cod_objeto);
            $objBlob = new Objeto($this->container, $cod_objeto);
        }
        
        
        // pegando permissao do usuario no objeto
        $permissao = false;
        if (isset($_SESSION['usuario']["cod_usuario"]))
        {
            $permissao = $this->container["administracao"]->pegarPerfilUsuarioObjeto($_SESSION['usuario']["cod_usuario"], $cod_objeto);
        }
        
        // verificando se o objeto está publicado
        if ($objBlob->valor("cod_status") != "2" && !$permissao)
        {
            return false;
        }
        
        // verifica se o objeto é protegido
        if (preg_match("/_protegido.*/", $objBlob->valor("script_exibir")))
        {
            $protegido = true;
        }
        else
        {
            if ($cod_objeto != $this->container["config"]->portal["objroot"])
            {
                if (count($caminho) > 0)
                {
                    // verificando se tem objeto protegido no parentesco
                    foreach ($caminho as $cam)
                    {
                        if (preg_match("/_protegido.*/", $cam["script_exibir"]))
                        {
                            $protegido = true;
                            break;
                        }
                    }
                }
            }
        }
        

        if ($protegido && (!$permissao || $permissao>_PERFIL_MILITARIZADO))
        {
            return false;
        }

        return true;
    }

}
