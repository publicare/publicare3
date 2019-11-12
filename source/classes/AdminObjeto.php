<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Classe AdminObjeto é responsável pela manipulação dos objetos por parte dos internautas
* @copyright GPL © 2007
* @package publicare
*
* MCTI - Ministério da Ciência, Tecnologia e Inovação - www.mcti.gov.br
* ANTT - Agência Nacional de Transportes Terrestres - www.antt.gov.br
* EPL - Empresa de Planejamento e Logística - www.epl.gov.br
* *
*
* Este arquivo é parte do programa Publicare
* Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
* como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
* Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
* de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
* Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
*/

/**
 * Classe adminobjeto, responsável por gerenciar parte usuários de objetos
 */
class AdminObjeto
{
    public $index;
	
    /**
     * Gera SQL para verificar se objeto está publicado
     * @param object $_page - Referência de objeto da classe Pagina
     * @return string
     */
    function CondicaoPublicado(&$_page)
    {
        return " and ".$_page->_db->nomes_tabelas["objeto"].".cod_status="._STATUS_PUBLICADO;
    }

    /**
     * Cria SQL para verificar se objeto está publicado, dentro de data válida,
     * ou usuário é dono
     * @param object $_page - Referência de objeto da classe Pagina
     * @return string
     */
    function CondicaoAutor(&$_page)
    {
        return " and ((".$_page->_db->nomes_tabelas["objeto"].".cod_status="._STATUS_PUBLICADO.$this->CondicaoData($_page).") "
                . "or ".$_page->_db->nomes_tabelas["objeto"].".cod_usuario=".$_SESSION['usuario']['cod_usuario'].')';
    }

    /**
     * Monta SQL para verificar se objeto está dentro de data válida
     * @param object $_page - Referência de objeto da classe Pagina
     * @return string
     */
    function CondicaoData(&$_page)
    {
        return " and (".$_page->_db->nomes_tabelas["objeto"].".data_publicacao<=".date("YmdHi")."00 and ".$_page->_db->nomes_tabelas["objeto"].".data_validade>=".date("YmdHi")."00)";
//        return "";
    }

    /**
     * Realiza busca de objetos no banco do publicare
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $query - Texto a buscar
     * @param string $excecoes - Lista de classes que não deve buscar, codigos separados por virgula
     * @param string $parentesco_excecoes - Lista de objetos que não deve buscar filhos
     * @param int $pagina - Pagina atual de resultados
     * @param int $paginacao - Numero de registros por página
     * @return array - Lista com resultado da busca
     */
    function Search(&$_page, $query, $excecoes="", $parentesco_excecoes="", $pagina=1, $paginacao=20)
    {
        $retorno = array("total"=>0,
                        "paginas"=>0,
                        "pagina"=>$pagina,
                        "paginacao"=>$paginacao,
                        "inicio"=>0,
                        "fim"=>0,
                        "query"=>$query,
                        "resultados"=>array());

        if ((isset($query) && strlen($query)>1))
        {
            $query = addslashes($query);
            
            $sql = "select distinct(o.cod_objeto), 
                                o.cod_pai,
                                o.cod_classe,
                                o.titulo,
                                o.descricao,
                                o.url_amigavel,
                                o.peso,
                                c.nome as nome_classe
                    from objeto o 
                                inner join classe c on o.cod_classe = c.cod_classe
                                left join tbl_text txt on o.cod_objeto = txt.cod_objeto
                                left join tbl_string str on o.cod_objeto = str.cod_objeto 
                    where
                                o.cod_status = 2
                                and o.apagado = 0 ";
            if ($excecoes!="") $sql .= "and c.cod_classe not in (".$excecoes.") ";
            if ($parentesco_excecoes!="") $sql .= "and o.cod_objeto not in (select distinct(pa2.cod_objeto) from parentesco pa2 where pa2.cod_pai in (".$parentesco_excecoes.")) ";
            $sql .= "and (o.titulo ilike ('%".$query."%')
                                    or o.descricao ilike ('%".$query."%')
                                    or txt.valor ilike ('%".$query."%')
                                    or str.valor ilike ('%".$query."%'))
                                and c.indexar = 1
                                and (o.data_publicacao <= ".date("YmdHi")."00 and o.data_validade >= ".date("YmdHi")."00) 
                    order by o.titulo";
            
            $sqlCont = "select count(*) as total from (" . $sql . ") as sqlbusca";
            
            $rs = $_page->_db->ExecSQL($sqlCont);
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
               if ($retorno["fim"] > $retorno["total"]) $retorno["fim"] = $retorno["total"];
               $retorno["paginas"] = intval($retorno["total"] / $retorno["paginacao"]);
               if ($retorno["total"] % $retorno["paginacao"] > 0) $retorno["paginas"]++;
               
               $rs = $_page->_db->ExecSQL($sql, $retorno["inicio"]-1, $retorno["paginacao"]);
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
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto que deseja recupear as tags
     * @return string - tags separadas por virgula
     */
    function PegaTags(&$_page, $cod_objeto)
    {
        $tags = "";
        $sql = "select nome_tag from tag t1 inner join tagxobjeto t2 on t1.cod_tag=t2.cod_tag where t2.cod_objeto=".$cod_objeto;
        $rs = $_page->_db->ExecSQL($sql);
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
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $titulo - Titulo do objeto
     * @return array - array com metadados do objeto
     */
    function PegaDadosObjetoPeloTitulo(&$_page, $titulo)
    {
        $sql = $_page->_db->sqlobj." where ".$_page->_db->nomes_tabelas["objeto"].".titulo = '".$titulo."'";
        $rs = $_page->_db->ExecSQL($sql);
        $dados = $rs->fields;
        return $dados;
    }

    /**
     * Recupera dados do objeto pelo código
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Código do objeto
     * @return array - array com metadados do objeto
     */
    function PegaDadosObjetoPeloID(&$_page, $cod_objeto)
    {
        if (is_numeric($cod_objeto))
        {
            $sql = $_page->_db->sqlobj." where ".$_page->_db->nomes_tabelas["objeto"].".cod_objeto=".$cod_objeto;
            $rs = $_page->_db->ExecSQL($sql);
            $dados = $rs->fields;
            return $dados;
        }
        return false;
    }
    
    /**
     * Instancia objeto da classe Objeto e já popula o mesmo
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @return \Objeto
     */
    function CriarObjeto(&$_page, $cod_objeto)
    {
        $objeto = new Objeto($_page, $cod_objeto);
        return $objeto;
    }

    /**
     * Pega o caminho do objeto e retorna string com caminho recursivo do objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @return string - Caminho até o objeto, separado por virgulas
     */
    function PegaCaminhoObjeto(&$_page, $cod_objeto)
    {
        $result='';
        $result = $this->RecursivaCaminhoObjeto($_page, $cod_objeto);
        return $result;
    }

    /**
     * Busca o caminho do objeto recursivamente, utilizando tabela parentesco
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @return string - Caminho do objeto
     */
    function RecursivaCaminhoObjeto(&$_page, $cod_objeto)
    {
        $result = array();
        $sql = "select cod_pai 
        from parentesco 
        where cod_objeto=".$cod_objeto." 
        order by ordem desc";
        $rs = $_page->_db->ExecSQL($sql);

        if ($rs->_numOfRows>0)
        {
            while (!$rs->EOF)
            {
                $result[] = $rs->fields['cod_pai'];
                $rs->MoveNext();
            }
            return implode (',',$result);
        } 
        else 
        {
            return _ROOT;
        }
    }

    /**
     * Pega o caminho do objeto e retorna array com codigo e titulo de todo o parentesco
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Caminho do objeto em array com dados [cod_objeto], [titulo]
     */
    function PegaCaminhoObjetoComTitulo(&$_page, $cod_objeto)
    {
        $result=array();

        $sql = "select
        ".$_page->_db->nomes_tabelas["parentesco"].".ordem,
        ".$_page->_db->nomes_tabelas["objeto"].".cod_objeto,
        ".$_page->_db->nomes_tabelas["objeto"].".titulo
        from objeto ".$_page->_db->nomes_tabelas["objeto"]." 
        inner join parentesco ".$_page->_db->nomes_tabelas["parentesco"]." on ".$_page->_db->nomes_tabelas["objeto"].".cod_objeto=".$_page->_db->nomes_tabelas["parentesco"].".cod_pai
        where ".$_page->_db->nomes_tabelas["parentesco"].".cod_objeto=$cod_objeto
        group by ".$_page->_db->nomes_tabelas["parentesco"].".ordem, 
        ".$_page->_db->nomes_tabelas["objeto"].".cod_objeto, ".$_page->_db->nomes_tabelas["objeto"].".titulo
        order by ".$_page->_db->nomes_tabelas["parentesco"].".ordem desc";

        $res = $_page->_db->ExecSQL($sql);
        $row = $res->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result[]= array('cod_objeto'=>$row[$i]['cod_objeto'],'titulo'=>$row[$i]['titulo']);
        }

        return $result;
    }

    /**
     * Busca todas as propriedades de determinado objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Array com propriedades
     */
    function PegaPropriedades(&$_page, $cod_objeto)
    {
        $result=array();
        
        // Busca lista de propriedades da classe
        $sql = "select ".$_page->_db->nomes_tabelas["propriedade"].".cod_tipodado, 
                ".$_page->_db->nomes_tabelas["propriedade"].".cod_propriedade,
                ".$_page->_db->nomes_tabelas["propriedade"].".nome,
                ".$_page->_db->nomes_tabelas["tipodado"].".tabela, 
                ".$_page->_db->nomes_tabelas["tipodado"].".nome as tipodado,
                ".$_page->_db->nomes_tabelas["propriedade"].".cod_referencia_classe, 
                ".$_page->_db->nomes_tabelas["propriedade"].".campo_ref
                from objeto ".$_page->_db->nomes_tabelas["objeto"]." 
                inner join propriedade ".$_page->_db->nomes_tabelas["propriedade"]." on ".$_page->_db->nomes_tabelas["propriedade"].".cod_classe = ".$_page->_db->nomes_tabelas["objeto"].".cod_classe
                inner join tipodado ".$_page->_db->nomes_tabelas["tipodado"]." on ".$_page->_db->nomes_tabelas["propriedade"].".cod_tipodado = ".$_page->_db->nomes_tabelas["tipodado"].".cod_tipodado 
                where cod_objeto=".$cod_objeto;
        $res = $_page->_db->ExecSQL($sql);
        
        $join = array();
        $campos = array();
        $tipo = array();

        $row = $res->GetRows();

        // Adiciona propriedades ao array props[]
        for ($i=0; $i<sizeof($row); $i++)
        {
            // caso propriedade seja obj_ref busca codigo do objeto referenciado
            if (($row[$i]["tabela"]=="tbl_objref") && (!$this->EMetadado($_page, $row[$i]["campo_ref"])))
            {
                $sql = "select ".$_page->_db->nomes_tabelas["objeto"].".cod_objeto 
                        from tbl_objref ".$_page->_db->nomes_tabelas["tbl_objref"]."
                        inner join objeto ".$_page->_db->nomes_tabelas["objeto"]." on ".$_page->_db->nomes_tabelas["objeto"].".cod_objeto=".$_page->_db->nomes_tabelas["tbl_objref"].".valor
                        where ".$_page->_db->nomes_tabelas["tbl_objref"].".cod_propriedade=".$row[$i]["cod_propriedade"]." 
                        and ".$_page->_db->nomes_tabelas["tbl_objref"].".cod_objeto=".$cod_objeto;
                $res2 = $_page->_db->ExecSQL($sql);
                $propriedade = $res2->fields;
                if ($propriedade["cod_objeto"]) $dados = $this->PegaPropriedades($_page, $propriedade["cod_objeto"]);
                $row[$i]["valor_saida"] = $dados[strtolower($row[$i]["campo_ref"])];
            }
            $props[] = $row[$i];
        }

        // Monta SQLs para busca dos valroes das propriedades em suas respectivas tabelas
        if (isset($props) && is_array($props))
        {
            foreach ($props as $row)
            {
                $result[$row['nome']]['tipo'] = $row['tabela'];
                $tabela = 'tbl_'.$row['nome'];
                $array_nomes[] = $row['nome'];
				
                switch ($row['tabela'])
                {
                    case 'tbl_objref':
                        if ($this->EMetadado($_page, $row['campo_ref']))
                        {
                            $tipo[] = 'ref';
                            $join[] = " left join tbl_objref as ".$tabela." on (".$tabela.".cod_propriedade = ". $row['cod_propriedade']." and ".$tabela.".cod_objeto=".$_page->_db->nomes_tabelas["objeto"].".cod_objeto) ";
                            $join[] = " left join objeto as ".$tabela."_objeto on (".$tabela.".valor=".$tabela."_objeto.cod_objeto)";
                            $campos[] = $tabela."_objeto.".$row['campo_ref']." as ".$row['nome'];
                            $campos[] = $tabela."_objeto.cod_objeto as ".$row['nome']."_referencia";
                        }
                        else
                        {
                            $tipo[] = 'ref_prop';
                            $campos[] = "'".$row['valor_saida']."' as ".$row['nome'];
                            $campos[] = $row['valor']." as ".$row['nome']."_referencia";
                        }
                        break;
                    case 'tbl_blob':
                        $tipo[] = 'blob';
                        $join[] = " left join tbl_blob as ".$tabela." on (".$tabela.".cod_propriedade = ". $row['cod_propriedade']." and ".$tabela.".cod_objeto=".$_page->_db->nomes_tabelas["objeto"].".cod_objeto) ";
                        $campos[] = $tabela.".cod_blob as ".$row['nome']."_cod_blob";
                        $campos[] = $tabela.".arquivo as ".$row['nome']."_arquivo";
                        $campos[] = $tabela.".tamanho as ".$row['nome']."_tamanho";
                        break;
                    case 'tbl_date':
                        $tipo[] = 'date';
                        $join[] = " left join tbl_date as ".$tabela." on (".$tabela.".cod_propriedade=".$row['cod_propriedade']." and ".$tabela.".cod_objeto=".$_page->_db->nomes_tabelas["objeto"].".cod_objeto)";
                        $campos[] = $tabela.".valor as ".$row['nome'];
                        break;
                    default:
                        $tipo[] = 'default';
                        $join[] = " left join ".$row['tabela']." as ".$tabela." on (".$tabela.".cod_propriedade=".$row['cod_propriedade']." and ".$tabela.".cod_objeto=".$_page->_db->nomes_tabelas["objeto"].".cod_objeto )";
                        $campos[] = $tabela.".valor as ".$row['nome'];
                        break;
                }
            }
		
            // Monta SQL com dados dos arrays de montagem
            $sql = "select ".implode(',',$campos)." from objeto ".$_page->_db->nomes_tabelas["objeto"]." ".implode(' ',$join)." where ".$_page->_db->nomes_tabelas["objeto"].".cod_objeto=".$cod_objeto;
            $res = $_page->_db->ExecSQL($sql);
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
                            $result[$array_nomes[$key]]['valor'] = $dados[$array_nomes[$key]];
                    }
                }
            }
        }
        return $result;
    }
    
    /**
     * Lista objetos filhos de determinado objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto pai
     * @param string $classe - Classes para buscar filhos
     * @param string $ordem - Ordem do resultado
     * @param int $inicio - Registro inicial para paginacao
     * @param int $limite - Numero de registros para trazer na paginação
     * @return array - Array de objetos
     */
    function ListaFilhos(&$_page, $cod_objeto, $classe='*', $ordem='', $inicio=-1, $limite=-1)
    {
            return $this->LocalizarObjetos($_page, $classe, '', $ordem, $inicio, $limite, $cod_objeto, 0);
    }

    /**
     * Usa tabela parentesco para buscar os codigos dos objetos filho
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a buscar os filhos
     * @return array - array com codigo dos objetos
     */
    function ListaCodFilhos(&$_page, $cod_objeto)
    {
        $sql = "SELECT cod_objeto "
                . "FROM parentesco "
                . "WHERE cod_pai = ".$cod_objeto." "
                . "AND ordem = 1;";
        $res = $_page->_db->ExecSQL($sql);
        while ($row = $res->FetchRow())
        {
            $result[] = $row['cod_objeto'];
        }
        return $result;
    }

    /**
     * Cria informações a serem testadas pelo SQL
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $str - String para criar as informações
     * @return string|array - Array com dados a serem testados
     */
    function CriaInfoTeste(&$_page, $str)
    {
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
                if ($this->EMetadado($_page, $passo_dois[1]))
                {
                    if ($passo_dois[1] == 'data_publicacao' || $passo_dois[1] == 'data_validade')
                    {
                        $passo_dois[1] = $_page->_db->Day($_page->_db->nomes_tabelas["objeto"].'.'.$passo_dois[1]);
                        $passo_dois[3] = ConverteData($passo_dois[3],16);
                    }
                    $passo_dois[1] = $_page->_db->nomes_tabelas["objeto"].'.'.$passo_dois[1];
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
                        $_page->AdicionarAviso("Operador ".$exp." desconhecido.",true);
                }
            }
        }
        
        return $result;
    }

    /**
     * Verifica se propriedade é metadado
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $teste - Nome da propriedade
     * @return boolean
     */
    function EMetadado(&$_page, $teste)
    {
        if (strpos($teste,'.'))
        {
            $teste = substr($teste,strpos($teste,'.')+1);
        }
        if (in_array($teste,$_page->_db->metadados)) return true;

        if (strpos($teste,'objeto.') || strpos($teste,$_page->_db->nomes_tabelas['objeto'].".")) return true;
        return false;
    }

    /**
     * Localiza objetos no banco de dados
     * @param object $_page - Referência de objeto da classe Pagina
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
    function LocalizarObjetos(&$_page, $classe, $qry, $ordem='', $inicio=-1, $limite=-1, $pai=-1, $niveis=-1, $apagados=false, $likeas='', $likenocase='', $tags='')
    {
        if (!isset($classe) || $classe==null || $classe=='') return false;
		
        $array_qry = $this->CriaInfoTeste($_page, $qry);
        $pai_join = $this->CriaSQLPais($_page, $pai, $niveis);
        $usuario_where = $this->CriaCondicaoUsuario($_page);
        $tags_join = "";
        $tags_where = "";
        $tags_temp = "";

        if ($tags!="")
        {
            $array_tags = preg_split("[,]", $tags);
            $tags_join .= " inner join tagxobjeto ".$_page->_db->nomes_tabelas['tagxobjeto']." on ".$_page->_db->nomes_tabelas['objeto'].".cod_objeto=".$_page->_db->nomes_tabelas['tagxobjeto'].".cod_objeto 
            inner join tag ".$_page->_db->nomes_tabelas['tag']." on ".$_page->_db->nomes_tabelas['tagxobjeto'].".cod_tag=".$_page->_db->nomes_tabelas['tag'].".cod_tag ";
            $tags_where .= " and (";
            foreach ($array_tags as $tag)
            {
                $tags_temp .= " or ".$_page->_db->nomes_tabelas['tag'].".nome_tag='".trim($tag)."'";
            }
            $tags_where .= substr($tags_temp, 3);
            $tags_where .= ")";
        }
	
        // Deve buscar objetos apagados?
        if (!$apagados) $apagado_where = " and (".$_page->_db->nomes_tabelas['objeto'].".apagado<>1)";

        $cod_classe_array = array();

        // Se ordem não tiver sido informada, ordena por peso
        if ($ordem=='') $ordem = array('peso');
        else
        {
            if (!is_array($ordem)) $ordem = explode (",", $ordem);
        }

        if(!$likeas=='')
        {
            $like_as = " and ".$_page->_db->nomes_tabelas['objeto'].".titulo LIKE '".$likeas."'";
        }
        // Além de perguntar sobre 'ilike', também garante que só um LIKE será usado na Query (caso programador tente usar LIKE e iLIKE na mesma chamada)
        if((!$likenocase=='') || ((!$likeas=='') && (!$likenocase=='')))
        {
            $like_as = " and ".$_page->_db->nomes_tabelas['objeto'].".titulo ILIKE '".strtolower($likenocase)."'";
        }
        
        // Verifica se tem propriedade na ordem
        $tem_propriedade_na_ordem=false;
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
            if (!$this->EMetadado($_page, $array_ordem[$key]['campo'])) $tem_propriedade_na_ordem = true;
        }
        
        // Verifica se tem propriedade na query
        $tem_propriedade_na_qry = false;
        foreach ($array_qry as $condicao)
        {
            if (!$this->EMetadado($_page, $condicao[0])) $tem_propriedade_na_qry = true;
        }
        
        // Prepara SQL para as classes
        $multiclasse = false; //Classe única. Nesse caso NÃO é preciso criar a temp table
        $todas_as_classes = false;
        if ($classe=='*')
        {
            $todas_as_classes = true;
            $multiclasse = true;  //Classe unica e falso. Nesse caso e preciso cria a temp table
        }
        else
        {
            if (!is_array($classe)) $classe = explode (",",strtolower($classe));
            if (count($classe)>1) $multiclasse = true; //Classe unica e falso. Nesse caso e preciso cria a temp table
        }
        $classes = $this->CodigoDasClasses($_page, $classe);

        if (($tem_propriedade_na_ordem) || ($multiclasse) && ($tem_propriedade_na_qry))
        {
            if (!isset($classes_where)) $classes_where = "";
            $sql_out = $this->_LocalizarObjetosComTabelaTemporaria ($_page, $classes, $array_qry, $array_ordem, $apagado_where.$tags_where.$usuario_where.$classes_where, $pai_join.$tags_join);
            $sqlfinal = "select * from ".$sql_out['tbl'].$sql_out['ordem'];
        }
        else
        {
            $sql_out = $this->_LocalizarObjetosSemTabelaTemporaria ($_page, $classes, $array_qry, $array_ordem);
            $classes_where = "";
            if (isset($sql_out['classes']) && is_array($sql_out['classes']))
            {
                $classes_where = ' and '.$_page->_db->CreateTest($_page->_db->nomes_tabelas['objeto'].'.cod_classe',$sql_out['classes']);
            }
            $sqlfinal = "select ".$_page->_db->sqlobjsel;
            if (isset($sql_out['campos'])) $sqlfinal .= $sql_out['campos'];
            $sqlfinal .= $_page->_db->sqlobjfrom.$pai_join.$tags_join;
            if (isset($sql_out['from'])) $sqlfinal .= $sql_out['from'];
            $sqlfinal .= ' where (1=1)'.$apagado_where.$tags_where;
            if (isset($sql_out['where'])) $sqlfinal .= $sql_out['where'];
            $sqlfinal .= $usuario_where.$classes_where;
            if (isset($like_as)) $sqlfinal .= $like_as;
            if (isset($sql_out['ordem'])) $sqlfinal .= $sql_out['ordem'];
        }
		
        $res = $_page->_db->ExecSQL($sqlfinal, $inicio, $limite);
        $row = $res->GetRows();
		
        $objetos = array();

        // Vai criando objetos Objeto e populando array
        for ($i=0; $i<sizeof($row); $i++)
        {
            if ($_SESSION['usuario']['perfil'] < _PERFIL_MILITARIZADO || ($_SESSION['usuario']['perfil']==_PERFIL_DEFAULT || $_SESSION['usuario']['perfil']==_PERFIL_MILITARIZADO) && ($row[$i]["data_publicacao"]<=date("YmdHi")."00" && $row[$i]["data_validade"]>=date("YmdHi")."00"))
            {
                $obj = new Objeto($_page);
                $obj->povoar($_page, $row[$i]);
                if (!in_array($obj, $objetos)) $objetos[] = $obj;
            }
        }
		
        // Apaga tabela temporária caso tenha sido utilizada
        if (isset($sql_out['tbl']) && $sql_out['tbl'] != '')
        {
            $_page->_db->DropTempTable($sql_out['tbl']);
        }

        return $objetos;
    }

    /**
     * Busca códigos das classes e retorna em array
     * @param object $_page - Referência de objeto da classe Pagina
     * @param array $classes - Array com prefixos das classes
     * @return array - Array com codigos das classes
     */
    function CodigoDasClasses(&$_page, $classes)
    {
        $this->CarregaClasses($_page);
        $saida=array();
        if ($classes=='*')
        {
            return $_SESSION['classesNomes'];
        }
        else
        {
            foreach ($classes as $nome)
            {
                if (isset($_SESSION['classesNomes'][strtolower($nome)])) $saida[] = $_SESSION['classesNomes'][strtolower($nome)];
                else
                {
                    if (isset($_SESSION['classesPrefixos'][strtolower($nome)])) $saida[] = $_SESSION['classesPrefixos'][strtolower($nome)];
                }
            }
        }
        return $saida;
    }

    /**
     * Carrega as classes do portal e guarda em session
     * @param object $_page - Referência de objeto da classe Pagina
     */
    function CarregaClasses(&$_page)
    {
        if ((!isset($_SESSION['classesPrefixos'])) || (!is_array($_SESSION['classesPrefixos'])) || count($_SESSION['classesPrefixos']) == 0 || ($_page->_usuario->EstaLogado()))
        {
            $sql = "select cod_classe, 
            prefixo, 
            nome, 
            indexar,
            descricao,
            sistema 
            from classe 
            order by nome";
            $rs = $_page->_db->ExecSQL($sql);

            if ($rs->_numOfRows > 0){
                while ($row = $rs->FetchRow()){
                    $_SESSION['classesPrefixos'][$row['prefixo']] = $row['cod_classe'];
                    $_SESSION['classesNomes'][strtolower($row['nome'])] = $row['cod_classe'];
                    $_SESSION['classes'][$row['cod_classe']] = $row;
                    if (!isset($_SESSION['classesIndexaveis']) || !is_array($_SESSION['classesIndexaveis'])) $_SESSION['classesIndexaveis'] = array();

                    if ($row['indexar']) {
                        if (!in_array($row['cod_classe'], $_SESSION['classesIndexaveis'])) $_SESSION['classesIndexaveis'][] = $row['cod_classe'];
                    }
                }
            }
        }
    }

    /**
     * Localiza objetos usando tabela temporária
     * @param object $_page - Referência de objeto da classe Pagina
     * @param array $classes - Array com codigos das classes
     * @param array $array_qry - Array com query
     * @param array $array_ordem - Array com propriedades para ordenar
     * @param string $default_where - Where default
     * @param string $pai_join - Sql com join para pai já montado
     * @return array
     */
    function _LocalizarObjetosComTabelaTemporaria (&$_page, $classes, $array_qry, $array_ordem, $default_where, $pai_join)
    {
        
        $tbl = $_page->_db->GetTempTable();
		
        // Variavel para controlar a criacao dos campos na tabela temporaria //
        $primeiro_loop=true;
        $campo_incluido=array();
        $campo_incluido_natabela=array();
        $ordem_temporaria=array();
		
        $sqls_insert = array();
		
        foreach ($classes as $cod_classe)
        {
            
            $temp_campos=array();
            $temp_from=array();
            $temp_where=array();
            $campo_incluido=array();
			
            //Constroi SQL para casos em que existem propriedades na ordem
            foreach ($array_ordem as $item)
            {
                if (!isset($item['orientacao'])) $item['orientacao'] = "asc";
                
                if (!$this->EMetadado($_page, $item['campo']))
                {
                    $info = $this->CriaSQLPropriedade($_page, $item['campo'], $item['orientacao'], $cod_classe);
                    
                    if ($info["tabela"]=="tbl_objref") $item['campo'] .= "_ref";
                    
                    if (!in_array($info['field'],$campo_incluido_natabela))
                    {
                        $tbl["colunas"][] = $_page->_db->AddFieldToTempTable($tbl,$info);
                        $campo_incluido_natabela[]=$info['field'];
                    }
                    
                    if (!in_array($info['field'],$campo_incluido))
                    {
                        $temp_campos[]=$info['field'];
                        $temp_from[]=$info['from'];
                        $temp_where[]=$info['where'];
                        $campo_incluido[]=$info['field'];
                    }
                }
                
                $string_temp = $item['campo'].' '.$item['orientacao'];
                if (!in_array($string_temp, $ordem_temporaria)) $ordem_temporaria[]=$item['campo'].' '.$item['orientacao'];
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
                    if ($this->EMetadado($_page, $condicao[0]))
                    {
                        if (preg_match('/floor/',$condicao[0])) {
                            $condicao[0]=str_replace('objeto.','',$condicao[0]);
                        }
                        $temp_where[]=' ('.$condicao[0]." ".$condicao[1]." '".$condicao[2]."')";
                    }
                    else
                    {
                        $info = $this->CriaSQLPropriedade($_page, $condicao[0],"", $cod_classe);
                        if (!in_array($info['field'],$campo_incluido_natabela))
                        {
                            $tbl["colunas"][] = $_page->_db->AddFieldToTempTable($tbl,$info);
                            $campo_incluido_natabela[]=$info['field'];
                        }
                        if (!in_array($info['field'],$campo_incluido))
                        {
                            $temp_campos[]=$info['field'];
                            $temp_from[]=$info['from'];
                            $temp_where[]=$info['where'];
                            $campo_incluido[]=$info['field'];
                        }
			
                        $temp_where[]= ' ('.$info['field']." ".$condicao[1]." ".$info['delimitador'].$condicao[2].$info['delimitador'].')';                   
                    }
                }
            }
			//fim
            $campos=','.implode($temp_campos,',');
            $from = implode($temp_from,' ');
            $where = implode($temp_where,' and ');
			
            $sqls_insert[] = 'insert into '.$tbl["nome"].
                    " select ".$_page->_db->sqlobjsel.$campos.$_page->_db->sqlobjfrom.$pai_join.$from.' where (1=1) and '.$where.$default_where;
			//$_page->_db->ExecSQL($sql);

        }
		
        $sqlCreate = $_page->_db->tipodados["temp"]." ".$_page->_db->tipodados["temp2"].$tbl["nome"]." (".implode(", ", $tbl["colunas"]).")";
        $_page->_db->ExecSQL($sqlCreate);
		
        foreach($sqls_insert as $sqls)
        {
            $_page->_db->ExecSQL($sqls);
        }

        $result['tbl']=$tbl["nome"];
        $result['ordem']=' order by '.implode($ordem_temporaria,',');
			
        return $result;
    }
    
    /**
     * Localizar objetos sem utilização de tabela temporária
     * @param object $_page - Referência de objeto da classe Pagina
     * @param array $classes - Array com codigos das classes
     * @param array $array_qry - Array com condicoes da query
     * @param array $array_ordem - Array com propriedades para ordenação
     * @return array - Dados da consulta
     */
    function _LocalizarObjetosSemTabelaTemporaria(&$_page, $classes, $array_qry, $array_ordem)
    {
        foreach ($array_ordem as $item)
        {
            $temp_array = $_page->_db->nomes_tabelas["objeto"].'.'.$item['campo'];
            if (isset($item['orientacao'])) $temp_array .= $item['orientacao'];
            $result['ordem'][]= $temp_array;
            if (!$this->EMetadado($_page, $item['campo']))
            {
                $result['campos'][]=$item['campo'];
            }
        }
		
        foreach ($classes as $cod_classe)
        {
            $input = array();
            $input = $this->CriaSQLParaCondicao($_page, $array_qry, $cod_classe);
            if (isset($input) && is_array($input) && count($input)>0 && ($input['where']!="" || $input['from']!=""))
            {
                $result['where'][] = $input['where'];
                $result['from'][] = $input['from'];
            }
            $result['classes'][] = $cod_classe;
        }

        if (isset($result['where']) && is_array($result['where']))
        {
            $result['where']=' and '.implode($result['where'],' and ');
        }

        if (isset($result['campos']) && is_array($result['campos'])) $result['campos']=implode($result['campos'],',');

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
        if (is_array($result['ordem'])) $result['ordem']=' order by '.implode($result['ordem'],',');
        return $result;
    }

    /**
     * Cria condições SQL com base no array query recebido
     * @param object $_page - Referência de objeto da classe Pagina
     * @param array $array_qry - Array com condições query
     * @param int $cod_classe - Código da classe
     * @return string
     */
    function CriaSQLParaCondicao(&$_page, $array_qry, $cod_classe)
    {
        $out = array("where"=>"", "from"=>"", "condicao"=>array());
        foreach ($array_qry as $condicao)
        {
            if (!is_array($condicao))
            {
                $out['where'] .= ' '.$condicao;
            }
            else
            {
                if ($this->EMetadado($_page, $condicao[0]))
                {
                    $condicao[0] = str_replace($_page->_db->nomes_tabelas["objeto"].'.(','(',$condicao[0]);
                    $out['where'].=' '.$condicao[0].' '.$condicao[1]." '".$condicao[2]."'";
                }
                else
                {
                    $temp = $this->CriaSQLPropriedade($_page, $condicao[0], "", $cod_classe);
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
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_pai - Codigo do objeto pai
     * @param int $cod_usuario - Codigo do usuario
     * @param string $ord1 - Coluna para ordenação
     * @param string $ord2 - tipo de ordenação, asc ou desc
     * @param int $inicio - Primeiro registro a ser retornado para paginação
     * @param int $limite - Numero de registros para paginação
     * @return Array Objetos
     */
    function LocalizarPendentes(&$_page, $cod_pai, $cod_usuario, $ord1, $ord2, $inicio=-1, $limite=-1)
    {
        $sql_pendentes = "SELECT t2.cod_objeto, t2.titulo 
        from pendencia t1 
        inner join objeto t2 on t1.cod_objeto=t2.cod_objeto 
        inner join parentesco t3 on t1.cod_objeto=t3.cod_objeto 
        where t3.cod_pai=".$cod_pai." 
        and t2.apagado=0
        order by t2.".$ord1." ".$ord2;
        $rs = $_page->_db->ExecSQL($sql_pendentes, $inicio, $limite);
        return $rs->GetRows(); 
    }
	
    /**
     * Localiza objetos que tiveram a publicação rejeitada
     * @param object $_page - Referência de objeto da classe Pagina
     * @return Array com objetos
     */
    function LocalizarRejeitados(&$_page)
    {
        $objetos=array();
        $usuario_atual = $_SESSION['usuario']["cod_usuario"];
        $sql_rejeitados = "SELECT cod_objeto,titulo from objeto where cod_status IN ("._STATUS_REJEITADO.") and cod_usuario = ".$usuario_atual." and apagado=0";
        $rs = $_page->_db->ExecSQL($sql_rejeitados);
        while ($row_rejeitados=$rs->FetchRow())
        {
            $obj[] = $row_rejeitados;
        }
        if (count($obj))
        {
            foreach ($obj as $obj_atual)
            {
                //$perfil_atual = $_page->Administracao->PegaPerfilDoUsuarioNoObjeto($usuario_atual,$obj_atual["cod_objeto"]);
                //if (($perfil_atual==_PERFIL_ADMINISTRADOR)||$perfil_atual==(_PERFIL_EDITOR)) {
                $objetos[]=$obj_atual;
                //}
            }
        }

        return $objetos;
    }

    /**
     * Cria SQL de condição de acordo com nível do usuário
     * @param object $_page - Referência de objeto da classe Pagina
     * @return string - SQL com condições
     */
    function CriaCondicaoUsuario(&$_page)
    {
        $sql_condicao = "";
        switch ($_SESSION['usuario']['perfil'])
        {
            case _PERFIL_DEFAULT:
                $sql_condicao = $this->CondicaoPublicado($_page).$this->CondicaoData($_page);
                break;
            case _PERFIL_AUTOR:
                //$sql_condicao=$this->CondicaoAutor();
                //$sql_condicao=$this->CondicaoData($_page);
                break;
            case _PERFIL_RESTRITO:
                $sql_condicao=$this->CondicaoPublicado($_page).$this->CondicaoData($_page);
                break;
            case _PERFIL_MILITARIZADO:
                $sql_condicao=$this->CondicaoData($_page);
                break;
            case _PERFIL_ADMINISTRADOR:
                //$sql_condicao=$this->CondicaoData($_page);
                break;
            default:
                //$sql_condicao = $this->CondicaoPublicado($_page).$this->CondicaoData($_page);
                //$sql_condicao = $this->CondicaoData($_page);
                break;
        }

        return $sql_condicao;
    }

	function CriaClasseInfo(&$_page, $classe)
	{
		if (!is_array($classe))
		{
			if ($classe!='')
			$classe = explode(',',$classe);
		}
		if ((!is_array($classe)) || (!count($classe)))
		return false;
		$sql = "select cod_classe from classe where ".$_page->_db->CreateTest('nome',$classe);
		$rs = $_page->_db->ExecSQL($sql);
		while ($row=$rs->FetchRow())
		{
			$cod_classe_array[]=$row['cod_classe'];
		}
		if (count ($cod_classe_array)!=count($classe))
		{
			$_page->AdicionarAviso("Uma ou mais classes inexistentes em ".implode(",",$classe).".");
		}
		if (count($cod_classe_array))
		{
			$whereclasse=$_page->_db->CreateTest('objeto.cod_classe',$cod_classe_array);
		}
		else
		$whereclasse=" 1=0 ";

		$whereclasse = " and ".$whereclasse;
		return array("cod_classe_array"=>$cod_classe_array,"sql"=>$whereclasse);
	}

	function PegaNomeClasse(&$_page, $cod_classe)
	{
		$sql = "select nome, 
		prefixo 
		from classe 
		where cod_classe=".$cod_classe;
		$rs = $_page->_db->ExecSQL($sql);
		return $rs->fields;
	}

    function CriaSQLPropriedade(&$_page, $campo, $direcao, $cod_classe)
    {
        
        $info = $this->PegaInfoSobrePropriedade($_page, $cod_classe, $campo);
		
        if ($info!=null && $info!='')
        {
            $montagem["tabela"] = $info['tabela'];
            $montagem['from'] = " left join ".$info['tabela']." as ".$campo." on ";
            $on = ' '.$_page->_db->nomes_tabelas["objeto"].'.cod_objeto='.$campo.'.cod_objeto';
            $montagem['type'] = $info['nome'];
            $montagem['field'] = "";
            if ($info['tabela']=='tbl_objref')
            {
                $montagem['from'] .= '(('.$on.') and ('.$campo.'.cod_propriedade='.$info['cod_propriedade'].'))';
                $montagem['where'] = '(1 = 1) and '.$_page->_db->nomes_tabelas["objeto"].'.cod_classe='.$cod_classe;
                $montagem['from'] .= " left join objeto as ".$campo."_ref on ".$campo.".valor=".$campo."_ref.cod_objeto";
                if (!$this->EMetadado($_page, $info['campo_ref']))
                {
                    $propriedade = $this->PegaInfoSobrePropriedade($_page, $info['cod_referencia_classe'], $info['campo_ref']);
                    //$montagem['from'] .= '(('.$on.') and ('.$campo."_property.cod_propriedade=".$propriedade['cod_propriedade'].'))';
                    $montagem['from'] .= " left join ".$propriedade['tabela']." as ".$campo."_campo_ref on ".$campo.'_ref.cod_objeto='.$campo.'_property.cod_objeto';
                    $montagem['field'] .= $campo."_property.valor";
                    $montagem['delimitador']=$propriedade['delimitador'];
                    //$montagem['where'] .= $campo."_property.cod_propriedade=".$propriedade['cod_propriedade'];
                }
                else
                {
                    //$montagem['from'] .= '(('.$on.') and ('.$campo.'.cod_propriedade='.$info['cod_propriedade'].'))';
                    $montagem['field'] .=  $campo."_ref.".$info['campo_ref'];
                    $montagem['delimitador']="'";
                }
            }
            else
            {
                $montagem['from'] .= $on;
                $montagem['where'] = $campo.".cod_propriedade=".$info['cod_propriedade'];
                $montagem['field'] .= $campo.".valor";
                $montagem['delimitador']="'";
            }
        }
        else
        {
            $ClasseNome = $this->PegaNomeClasse($_page, $cod_classe);
            $_page->AdicionarAviso("Classe ".$ClasseNome['nome']." n&atilde;o tem propriedade $campo.",true);
        }
        return $montagem;
    }

	function PegaInfoSobrePropriedade(&$_page, $cod_classe, $prop)
	{
		$tabelas = $_page->_db->nomes_tabelas;
                
                // Removendo parenteses para verificacao da propriedade
//                $prop = preg_replace("[\(|\)]", "", $prop);
		
		$sql = "select ".$tabelas["propriedade"].".cod_propriedade, 
		".$tabelas["tipodado"].".nome, 
		".$tabelas["tipodado"].".tabela, 
		".$tabelas["propriedade"].".cod_referencia_classe, 
		".$tabelas["propriedade"].".campo_ref, 
		".$tabelas["tipodado"].".delimitador 
		from propriedade ".$tabelas["propriedade"]." 
		inner join tipodado ".$tabelas["tipodado"]." 
		on ".$tabelas["propriedade"].".cod_tipodado = ".$tabelas["tipodado"].".cod_tipodado
		where ".$tabelas["propriedade"].".cod_classe=".$cod_classe;
		
		if (!intval($prop))
			$sql .=" and ".$tabelas["propriedade"].".nome='".$prop."'";
		else
			$sql .=" and ".$tabelas["propriedade"].".cod_propriedade=".$prop;
			
		$rs = $_page->_db->ExecSQL($sql);
		$return = $rs->fields;
		return $return;
	}

	function Limites($inicio,$limite)
	{
		if ($limite!="")
		{
			$result=" limit ".intval($inicio).",$limite";
		}
		else
		{
			if ($inicio)
			$result=" limit $inicio";
		}
		return $result;
	}

	function CriaSQLQuery(&$_page, $qry, $cod_classe_array)
	{
		//dump ($qry);
		if ($qry!='')
		{
			if (!is_array($qry))
			{
				if (strpos($qry,'&&')===false)
				{
					$qry=explode('||',$qry);
					$cola=" OR ";
				}
				else
				{
					$qry=explode('&&',$qry);
					$cola=" AND ";
				}
			}
		}
		else
		$qry=array();
		//dump($qry);
		foreach ($qry as $value)
		{
			//dump ($value);
			preg_match("|(.+?)([=\<\>]{1,2})(.+)|",$value,$item);
			if ($item[1]!='')
			{
				//dump ($item);
				$item[1]=trim($item[1]);
				$item[3]=trim($item[3]);
				if ($this->EMetadado($_page, $item[1]))
				{
					if ($result['where']!='')
					$result['where'].=$cola;
					if ($item[1]=='data_publicacao' || $item[1]=='data_validade')
					{
						if ($item[2]=='=')
						{
							$item[1]=$this->db->Day('objeto.'.$item[1]);
							$data=ConverteData($item[3],16);
						}
						else
						{
							$data=ConverteData($item[3],15);
						}
						$result['where'].=$item[1].$item[2];
						$result['where'].=$data;
					}
					else
					{
						$result['where'].=' objeto.'.$item[1].$item[2];
						if (is_numeric($item[3]))
						{
							$result['where'].=$item[3];
						}
						else
						{
							$result['where'].="'".$item[3]."'";
						}
					}
				}
				else
				{
					if (is_array($cod_classe_array))
					{
						//var_dump($cod_classe_array);
						$sqltoproperty=$this->CriaSQLPropriedade($_page, $cod_classe_array, $item[1], '');
						if ($sqltoproperty[0]['field'])
						{
							$start=true;
							foreach ($sqltoproperty as $property)
							{
								//echo "<br>$property<br>";
								//dump($property);
								if ($start)
								{
									$field=$property['field'];
									$result['joined']=$field;
									if (($result['sort']!='') && ($property['sort']!=''))
									$result['sort'].=',';
									$result['sort'].=$property['sort'];
									$result['join'].=$property['join'];
									$result['join'].=' and ('.$property['where'];
									$start=false;
								}
								else
								{
									$result['join'].=' or '.$property['where'];
								}
							}
							$result['join'].=')';
							if ($result['where']!='')
							$result['where'].=$cola;
							$result['where'].=$field.$item[2];

							if ($property['type']=='data')
							{
								$item[3] = ConverteData($item[3],15);

							}

							if (is_numeric($item[3]))
							{
								$result['where'].=$item[3];
							}
							else
							{
								$result['where'].="'".$item[3]."'";
							}
							$result['field'].=','.$field;
						}
						else
						{
							$_page->AdicionarAviso('Erro no processamento de um comando Localizar.<br> Express&atilde;o "'.$value.'" pede um campo n&atilde;o existente.',true);
						}
					}
				}
			}
			else
			{
				$_page->AdicionarAviso('Erro no processamento de um comando Localizar.<br> Express&atilde;o "'.$value.'" n&atilde;o pode ser analisada.',true);
			}
		}
		if ($result['where']!='')
		{
			$result['where'] = ' and ('.$result['where'].')';
		}
		return $result;
	}

	function CriaSQLPais(&$_page, $pai, $niveis, $campo="objeto.cod_pai")
	{
		$return = "";
		if ($pai!=-1)
		{
		 	$return = " inner join parentesco ".$_page->_db->nomes_tabelas["parentesco"]." 
				on (".$_page->_db->nomes_tabelas["parentesco"].".cod_objeto = ".$_page->_db->nomes_tabelas["objeto"].".cod_objeto 
				and ".$_page->_db->nomes_tabelas["parentesco"].".cod_pai=".$pai;
		 	if ($niveis>=0)
		 		$return.= " and ordem<=".($niveis+1).')';
		 	else
		 		$return .=')';
		}
		return $return;
	}

	function PegaIDFilhos(&$_page, $pai, $niveis)
	{
		$sql = "select cod_pai from parentesco where cod_pai=$pai";
		$res = $_page->_db->ExecSQL($sql);
		while ($row = $res->FetchRow())
		{
			$list[]=$row['cod_objeto'];
		}
		return $list;
	}
	
	function PegaIDPai(&$_page, $cod_objeto, $nivel, $excecoes, $desc=false)
	{
		$rtnLista = array();
		$contador = 0;
		$sql = "select ".$_page->_db->nomes_tabelas["parentesco"].".cod_pai, 
		".$_page->_db->nomes_tabelas["objeto"].".titulo from parentesco ".$_page->_db->nomes_tabelas["parentesco"]." 
		inner join objeto ".$_page->_db->nomes_tabelas["objeto"]." on ".$_page->_db->nomes_tabelas["objeto"].".cod_objeto = ".$_page->_db->nomes_tabelas["parentesco"].".cod_pai 
		where ".$_page->_db->nomes_tabelas["parentesco"].".cod_objeto = $cod_objeto 
		order by ".$_page->_db->nomes_tabelas["parentesco"].".ordem";
		if ($desc)
			$sql .= " desc";
		$res = $_page->_db->ExecSQL($sql);
		while ($row = $res->FetchRow())
		{
			$arrCodeTitulo = array($row['cod_pai'] => $row['titulo']);
			if (($contador < $nivel) && !(in_array($row['cod_pai'],$excecoes)))
			{
			array_push_associative($rtnLista, $arrCodeTitulo);
			$contador = $contador + 1;
			}
		}
		//array_flip($rtnLista);
		return $rtnLista;
	}
	
	function PegaNumFilhos(&$_page, $pai)
	{
		$sql = "SELECT count(*) as total 
		FROM parentesco ".$_page->_db->nomes_tabelas["parentesco"]." 
		LEFT JOIN objeto ".$_page->_db->nomes_tabelas["objeto"]." on ".$_page->_db->nomes_tabelas["parentesco"].".cod_objeto = ".$_page->_db->nomes_tabelas["objeto"].".cod_objeto 
		WHERE ".$_page->_db->nomes_tabelas["parentesco"].".cod_pai = $pai 
		AND ".$_page->_db->nomes_tabelas["objeto"].".apagado <> 1";
		$res = $_page->_db->ExecSQL($sql);
		return $res->fields["total"];
	}

	function EFilho(&$_page, $cod_objeto, $cod_pai)
	{
		$sql = "select ".$_page->_db->nomes_tabelas["parentesco"].".cod_objeto 
		from parentesco ".$_page->_db->nomes_tabelas["parentesco"]." 
		where ".$_page->_db->nomes_tabelas["parentesco"].".cod_pai=$cod_pai 
		and ".$_page->_db->nomes_tabelas["parentesco"].".cod_objeto=$cod_objeto";
		$res = $_page->_db->ExecSQL($sql);
		return !$res->EOF;
	}

	function ShowObjectResume(&$_page, $cod_objeto)
	{
		$obj = $this->CriarObjeto($_page, $cod_objeto);
		foreach ($obj as $key => $quadro)
		{
			if ($key == 'CaminhoObjeto')
				$arrCaminho = $quadro;
		}
		//echo $obj['CaminhoObjeto']."<br>";
		//var_dump_pre($cod_objeto);
		return array('url'=>$obj->Valor('url'), 'titulo'=>$obj->Valor('titulo'), 'descricao'=>$obj->Valor('descricao'), 'codigo'=>$obj->Valor('cod_objeto'),'caminho'=>$arrCaminho);
	}

	function EnviaEmailSolicitacao(&$_page, $cod_chefia, $cod_objeto,$mensagemsubmetida)
	{
		global $PORTAL_NAME;
	  include('email.class.php');
	  $arrInfoUsuario = $_page->_usuario->PegaInformacaoUsuario($_page, $cod_chefia);
	  $arrInfoDadosObjeto = $_page->_adminobjeto->PegaDadosObjetoPeloID($_page, $cod_objeto);
	  
		 $texConteudo = "<font align=\"left\">Esta mensagem &eacute; para informar a solicita&ccedil;&atilde;o de publica&ccedil;&atilde;o de objetos por parte do usu&aacute;rio <b>".$_SESSION['usuario']['nome']."</b> dentro do ".$PORTAL_NAME.".
		 <br>
		 Voc&ecirc; deve efetuar login no sistema, utilizando seu usu&aacute;rio e senha, <a href=\""._URL."/login\">clicando aqui</a>. Dentro das <b>Op&ccedil;&otilde;es de Menu</b>, localize o bot&atilde;o de <i>objetos aguardando aprova&ccedil;&atilde;o</i>.
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
		 <center>"._PORTAL_NAME."</center></font>";
	  
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
     * @param object $_page - Referência de objeto da classe Pagina
     * @param integer $codClasse - Código da classe
     * @param integer $codPele - Código da pele
     * @param string $codTexto - Antes ou depois de gravar o objeto
     */
    function ExecutaScript(&$_page, $codClasse=0, $codPele=0, $codTexto="antes")
    {
        $cod_classe = (int)htmlspecialchars($codClasse, ENT_QUOTES, "UTF-8");
        $cod_pele = (int)htmlspecialchars($codPele, ENT_QUOTES, "UTF-8");
        
        $ClasseUtilizada = $this->PegaNomeClasse($_page, $cod_classe);
        $PeleUtilizada = $_page->_administracao->PegaListaDePeles($_page, $cod_pele);
        
        if (count($PeleUtilizada) == 1 && file_exists($_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$PeleUtilizada[0]['prefixo']."_".$ClasseUtilizada['prefixo']."_".$codTexto.".php"))	
        {
                include($_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$PeleUtilizada['prefixo']."_".$ClasseUtilizada['prefixo']."_".$codTexto.".php");
        }
        elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$ClasseUtilizada['prefixo']."_".$codTexto.".php"))
        {
                include($_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$ClasseUtilizada['prefixo']."_".$codTexto.".php");
        }
    }

	function ExecutaScriptDepois(&$_page, $codClasse, $codPele) {
		$ClasseUtilizada = $this->PegaNomeClasse($_page, $codClasse);
		$PeleUtilizada = $_page->_administracao->PegaListaDePeles($_page, $codPele);
		
		if (file_exists($_SERVER['DOCUMENT_ROOT']."/html/skin/".$PeleUtilizada['prefixo']."/exec_".$ClasseUtilizada['prefixo']."_depois.php"))	{
			include($_SERVER['DOCUMENT_ROOT']."/html/skin/".$PeleUtilizada['prefixo']."/exec_".$ClasseUtilizada['prefixo']."_depois.php");
			return $_SERVER['DOCUMENT_ROOT']."/html/skin/".$PeleUtilizada['prefixo']."/exec_".$ClasseUtilizada['prefixo']."_depois.php";
		}
		elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$ClasseUtilizada['prefixo']."depois.php")) {
			include($_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$ClasseUtilizada['prefixo']."_depois.php");
			return $_SERVER['DOCUMENT_ROOT']."/html/execscript/exec_".$ClasseUtilizada['prefixo']."_depois.php";
		}
	}

        function estaSobAreaProtegida(&$_page, $cod_objeto)
        {
            $_page->IncluirAdmin();
            $protegido = false;
            $caminho = $_page->_adminobjeto->RecursivaCaminhoObjeto($_page, $cod_objeto);
            $caminho = explode(",", $caminho);
            $caminho[] = $cod_objeto;

            $objBlob = new Objeto($_page, $cod_objeto);

            // pegando permissao do usuario no objeto
            $permissao = false;
            if (isset($_SESSION['usuario']["cod_usuario"]))
                $permissao = $_page->_administracao->PegaPerfilDoUsuarioNoObjeto($_page, $_SESSION['usuario']["cod_usuario"], $cod_objeto);
            //xd($permissao);

            // verificando se o objeto está publicado
            if ($objBlob->metadados["cod_status"]!="2" && !$permissao)
            {
               return false;
            }

            // verificando se tem objeto protegido no parentesco
            foreach ($caminho as $obj)
            {
                $objeto = new Objeto($_page, $obj);
                if (preg_match("/_protegido.*/", $objeto->metadados["script_exibir"]))
                {
                    $protegido = true;
                    break;
                }
            }

            if ($protegido && (!$permissao || $permissao>_PERFIL_MILITARIZADO))
            {
                return false;
            }

            return true;
        }

}

?>
