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

/**
 * Classe que contém métodos para manipulação de objetos
 */
class Administracao
{
    public $classesPrefixos;
    public $classesNomes;
    public $classesIndexaveis;
    public $_index;
    public $_page;

    /**
     * Método construtor da classe Administracao.class.php
     * @param object $_page - Referência de objeto da classe Pagina
     */
    function __construct(&$_page)
    {
        $this->_page = $_page;
        
        $this->metadados = $this->_page->_db->metadados;
        $this->classesIndexaveis = array();
    }
    
    /**
     * Adiciona propriedade em classe
     * @param int $cod_classe - Codigo da classe
     * @param array $novo - Dados da propriedade
     */
    function AcrescentarPropriedadeAClasse($cod_classe, $novo)
    {
        $sql = "INSERT INTO ".$this->_page->_db->tabelas["propriedade"]["nome"]." ("
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_classe"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_tipodado"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_referencia_classe"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["campo_ref"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["nome"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["posicao"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["rotulo"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["descricao"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["obrigatorio"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["seguranca"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["valorpadrao"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["rot1booleano"].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["rot2booleano"].") "
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
        $this->_page->_db->ExecSQL($sql);
    }
    
    /**
     * Altera a lista de objetos que podem conter objetos de determinada classe
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe
     * @param array $lista - Array com códigos dos objetos
     */
//    function AlterarListaDeObjetosQueContemClasse(&$_page, $cod_classe, $lista)
//    {
//        $sql = 'delete from classexobjeto 
//        where cod_classe='.$cod_classe;
//        $this->_page->_db->ExecSQL($sql);	
//
//        if (is_array($lista))
//        {
//            foreach ($lista as $item)
//            {
//                $this->_page->_db->ExecSQL("insert into classexobjeto(cod_classe,cod_objeto) values(".$cod_classe.",".$item.")");
//            }
//        }
//    }
    
    /**
     * Altera objeto no banco de dados
     * @param array $dados - Dados do objeto
     * @param bool $log - Indica se deve gerar log ou não
     * @return int - Código do objeto alterado
     */
    function AlterarObjeto($dados, $log = true)
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
        $sql_pele = "SELECT ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pele"]." AS cod_pele "
                . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $row_pele = $this->_page->_db->ExecSQL($sql_pele);
        $row_pele = $row_pele->GetRows();
        $row_pele = $row_pele[0];
        if (is_array($row_pele) && $row_pele['cod_pele'] != $cod_pele)
        {
            $this->TrocaPeleFilhos($cod_objeto, $cod_pele);
        }

        // Objeto root deverá ser sempre publicado
        if ($cod_objeto == 1 || $cod_objeto == $this->_page->config["portal"]["objroot"])
        {
            $cod_status = _STATUS_PUBLICADO;
        }
        
        // verifica se já existe objeto com a URL amigável
        $url_amigavel = $this->verificaExistenciaUrlAmigavel($url_amigavel, $cod_objeto);
			
        $sql = "UPDATE ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                . " SET ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pai"]." = ".$cod_pai.", "
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["script_exibir"]." = '".$script_exibir."', "
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_classe"]." = ".$cod_classe.", "
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_usuario"]." = ".$cod_usuario.", ";
        if ($cod_pele > 0) 
        {
            $sql .= " ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pele"]." = ".$cod_pele.", ";
        }
        else 
        {
            $sql .= " ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pele"]." = null, ";
        }
        $sql .= " ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_status"]." = ".$cod_status.", "
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["titulo"]." = '".$titulo."', "
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["descricao"]." = '".$descricao."', "
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["data_publicacao"]." = '".ConverteData($data_publicacao, 27)."', "
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["data_validade"]." = '".ConverteData($data_validade, 27)."', "
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["peso"]." = ".$peso.", "
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["url_amigavel"]." = '".$url_amigavel."', "
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["versao"]." = ".$this->_page->_db->tabelas["objeto"]["colunas"]["versao"]." + 1 "
                . "WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $this->_page->_db->ExecSQL($sql);

        $this->ApagarPropriedades($cod_objeto, false);
        $this->GravarPropriedades($cod_objeto, $cod_classe, $proplist);
        $this->GravarTags($cod_objeto, $tagslist);
			
        if ($log)
        {
            $this->_page->_log->IncluirLogObjeto($cod_objeto, _OPERACAO_OBJETO_EDITAR);
        }
        
        return $cod_objeto;
    }

    /**
     * Busca lista de classes no banco de dados e popula propriedades de classes
     */
    function CarregaClasses()
    {
        if (count($_SESSION['classesPrefixos']) == 0) $this->_page->_adminobjeto->CarregaClasses();
        
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
    function CodigoDaClasse($prefixo)
    {
        $this->CarregaClasses();
        return $this->classesPrefixos[$prefixo];
    }

    /**
     * Busca lista de peles no banco de dados. Caso esteja logado com usuário
     * admin ve todas as peles, caso contrario somente peles publicas
     * @param int $rcvPele - Código da pele
     * @return array
     */
    function PegaListaDePeles($rcvPele=NULL)
    {
        $result=array();
        $sqladd = "";
        
        if ($rcvPele && $rcvPele!=NULL && $rcvPele!=0) $sqladd = " AND ".$this->_page->_db->tabelas["pele"]["colunas"]["cod_pele"]." = ".$rcvPele;
        
        $sql = "SELECT ".$this->_page->_db->tabelas["pele"]["colunas"]["cod_pele"]." AS codigo, "
                . " ".$this->_page->_db->tabelas["pele"]["colunas"]["nome"]." AS texto, "
                . " ".$this->_page->_db->tabelas["pele"]["colunas"]["prefixo"]." AS prefixo, "
                . " ".$this->_page->_db->tabelas["pele"]["colunas"]["publica"]." AS publica "
                . " FROM ".$this->_page->_db->tabelas["pele"]["nome"]." "
                . " WHERE 1=1 ";
        if ($_SESSION['usuario']['perfil'] != _PERFIL_ADMINISTRADOR) {
            $sql .= " AND ".$this->_page->_db->tabelas["pele"]["colunas"]["publica"]." = '1'";
        }
        $sql .= $sqladd;
        $sql .= " ORDER BY ".$this->_page->_db->tabelas["pele"]["colunas"]["nome"];
        
        $res = $this->_page->_db->ExecSQL($sql);
        return $res->GetRows();
    }

    /**
     * Busca lista de usuários dependentes, caso usuario logado 
     * seja administrador traz todos usuários
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_usuario - código do usuário chefe
     * @return array
     */
    function PegaListadeDependentes($cod_usuario)
    {
        $result=array();
        
        if($_SESSION['usuario']['perfil'] == _PERFIL_ADMINISTRADOR)
        {
            $sql = "SELECT ".$this->_page->_db->tabelas["usuario"]["colunas"]["cod_usuario"]." AS codigo, "
                    . " ".$this->_page->_db->tabelas["usuario"]["colunas"]["nome"]." AS texto, "
                    . " ".$this->_page->_db->tabelas["usuario"]["colunas"]["secao"]." AS secao "
                    . " FROM ".$this->_page->_db->tabelas["usuario"]["nome"]." "
                    . " WHERE ".$this->_page->_db->tabelas["usuario"]["colunas"]["valido"]." = 1 "
                    . " OR ".$this->_page->_db->tabelas["usuario"]["colunas"]["cod_usuario"]." = ".$cod_usuario." "
                    . " ORDER BY ".$this->_page->_db->tabelas["usuario"]["colunas"]["secao"].", ".$this->_page->_db->tabelas["usuario"]["colunas"]["nome"];
        }
        else
        {
            if ($_SESSION['usuario']['cod_usuario'] == $cod_usuario)
            {
                $sql = "SELECT ".$this->_page->_db->tabelas["usuario"]["colunas"]["cod_usuario"]." AS codigo, "
                    . " ".$this->_page->_db->tabelas["usuario"]["colunas"]["nome"]." AS texto, "
                    . " ".$this->_page->_db->tabelas["usuario"]["colunas"]["secao"]." AS secao "
                    . " FROM ".$this->_page->_db->tabelas["usuario"]["nome"]." "
                    . " WHERE ".$this->_page->_db->tabelas["usuario"]["colunas"]["valido"]." = 1 "
                    . " AND (".$this->_page->_db->tabelas["usuario"]["colunas"]["chefia"]." = ".$cod_usuario." OR ".$this->_page->_db->tabelas["usuario"]["colunas"]["cod_usuario"]." = ".$cod_usuario.") "
                    . " ORDER BY ".$this->_page->_db->tabelas["usuario"]["colunas"]["secao"].", ".$this->_page->_db->tabelas["usuario"]["colunas"]["nome"];
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
        
        $rs = $this->_page->_db->ExecSQL($sql);
        $result = $rs->GetRows();

        return $result;
    }

//    /**
//     * Chama metodo PegaListadeDependentes e com retorno deste método
//     * e passa o array de resposta para o método CriaDropDown
//     * @param object $_page - Referência de objeto da classe Pagina
//     * @param int $cod_usuario
//     * @return string com lista de <options>
//     */
//    function DropDownListaDependentes(&$_page, $cod_usuario)
//    {
//        $lista=$this->PegaListadeDependentes($_page, $cod_usuario);
//        return $this->CriaDropDown($lista, $cod_usuario, false, 30);
//    }

    /**
     * Recebe array e monta string com <options> para o select do dropdown
     * @param type $lista
     * @param type $selecionado
     * @param type $branco
     * @param type $nummaxletras
     * @return string
     */
    function CriaDropDown($lista, $selecionado, $branco=true, $nummaxletras=0)
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
    function PegaPropriedadesDaClasse($cod_classe)
    {
        $sql = "SELECT ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_tipodado"]." AS cod_tipodado, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_propriedade"]." AS cod_propriedade, "
                . " ".$this->_page->_db->tabelas["tipodado"]["nick"].".".$this->_page->_db->tabelas["tipodado"]["colunas"]["nome"]." AS tipodado, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["campo_ref"]." AS campo_ref, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["nome"]." AS nome, "
                . " ".$this->_page->_db->tabelas["tipodado"]["nick"].".".$this->_page->_db->tabelas["tipodado"]["colunas"]["tabela"]." AS tabela, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_referencia_classe"]." AS cod_referencia_classe, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["posicao"]." AS posicao, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["descricao"]." AS descricao, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["rotulo"]." AS rotulo, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["obrigatorio"]." AS obrigatorio, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["seguranca"]." AS seguranca, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["valorpadrao"]." AS valorpadrao, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["rot1booleano"]." AS rot1booleano, "
                . " ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["rot2booleano"]." AS rot2booleano "
                . " FROM ".$this->_page->_db->tabelas["propriedade"]["nome"]." ".$this->_page->_db->tabelas["propriedade"]["nick"]." "
                . " INNER JOIN ".$this->_page->_db->tabelas["tipodado"]["nome"]." ".$this->_page->_db->tabelas["tipodado"]["nick"]." "
                . " ON ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_tipodado"]." = ".$this->_page->_db->tabelas["tipodado"]["nick"].".".$this->_page->_db->tabelas["tipodado"]["colunas"]["cod_tipodado"]." "
                . " WHERE ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_classe"]." = ".$cod_classe." "
                . " ORDER BY ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["posicao"];
        $rs = $this->_page->_db->ExecSQL($sql);
        
        return $rs->GetRows(); 
    }

    /**
     * Busca lista de objetos, com codigo do objeto e propriedade informada, 
     * de determinada classe no banco de dados e retorna array com informações.
     * @param int $cod_classe - Código da classe
     * @param string $propriedade - Propriedade que deseja valor
     * @return array
     */
    function PegaListaDeObjetos($cod_classe, $propriedade)
    {
        $result=array();
        if (in_array($propriedade, $this->_page->_db->metadados))
        {
            $sql = "SELECT ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." as codigo, "
                    . " ".$this->_page->_db->tabelas["objeto"]["colunas"][$propriedade]." as texto "
                    . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                    . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_classe"]." = ".$cod_classe." "
                    . " AND ".$this->_page->_db->tabelas["objeto"]["colunas"]["apagado"]." <> 1 "
                    . " ORDER BY ".$this->_page->_db->tabelas["objeto"]["colunas"][$propriedade];
//                    . " ".$propriedade." as texto "
//                    . "from objeto "
//                    . "where cod_classe=".$cod_classe." "
//                    . "and apagado <> 1 "
//                    . "order by ".$propriedade;
        }
        else
        {
            $info = $this->_page->_adminobjeto->CriaSQLPropriedade($propriedade, ' asc', $cod_classe);
            $sql = "SELECT ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." AS codigo, "
                    . " ".$info['field']." as texto "
                    . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." ".$this->_page->_db->tabelas["objeto"]["nick"]." "
                    . " ".$info['from']." "
                    . " WHERE ".$info['where']." "
                    . " AND ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["apagado"]." <> 1 "
                    . " ORDER BY ".$info['field'];
        }
        $res=$this->_page->_db->ExecSQL($sql);
        
        return $res->GetRows();
    }

    /**
     * Troca pele de filhos recursivamente de determinado objeto
     * @param int $cod_objeto - Codigo do objeto pai
     * @param int $cod_pele - Código da pele
     */
    function TrocaPeleFilhos($cod_objeto, $cod_pele)
    {
        $filhos = $this->_page->_adminobjeto->ListaCodFilhos($cod_objeto);

        if (is_array($filhos) && count($filhos) > 0)
        {
            $sql_pele_filhos = "UPDATE ".$this->_page->_db->tabelas["objeto"]["nome"]." ";
            if ($cod_pele==0) $sql_pele_filhos .= " SET ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pele"]." = null ";
            else $sql_pele_filhos .= " SET ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pele"]." = ".$cod_pele." ";
            $sql_pele_filhos .= "WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." IN (".join(',',$filhos).")";
            $this->_page->_db->ExecSQL($sql_pele_filhos);
            
            foreach ($filhos as $filho)
            {
                $this->TrocaPeleFilhos($filho, $cod_pele);
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
    function verificaExistenciaUrlAmigavel($url, $cod_objeto=0, $nivel=0, $tamanho=0)
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
                . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto "
                . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["url_amigavel"]." = '".$url."' ";
        if ($cod_objeto>0) $sql .= " AND NOT ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $rs = $this->_page->_db->ExecSQL($sql);
        if ($tamanho==0) $tamanho = strlen($url);
        if ($rs->_numOfRows > 0)
        {
            $nivel++;
            $url = substr($url, 0, $tamanho).$nivel;
            $url = $this->verificaExistenciaUrlAmigavel($url, $cod_objeto, $nivel, $tamanho);
        }
        return $url;
    }

    /**
     * Apaga propriedades de determinado objeto
     * @param int $cod_objeto - Codigo do objeto a remover as propriedades
     * @param bool $tudo - Indica se deve apagar blobs também
     */
    function ApagarPropriedades($cod_objeto, $tudo = true)
    {
        $sql = "SELECT ".$this->_page->_db->tabelas["tipodado"]["nick"].".".$this->_page->_db->tabelas["tipodado"]["colunas"]["tabela"]." AS tabela "
                . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." ".$this->_page->_db->tabelas["objeto"]["nick"]."  "
                . " INNER JOIN ".$this->_page->_db->tabelas["propriedade"]["nome"]." ".$this->_page->_db->tabelas["propriedade"]["nick"]." "
                    . " ON ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_classe"]." = ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_classe"]." "
                . " INNER JOIN ".$this->_page->_db->tabelas["tipodado"]["nome"]." ".$this->_page->_db->tabelas["tipodado"]["nick"]." "
                    . " ON ".$this->_page->_db->tabelas["propriedade"]["nick"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_tipodado"]." = ".$this->_page->_db->tabelas["tipodado"]["nick"].".".$this->_page->_db->tabelas["tipodado"]["colunas"]["cod_tipodado"]." "
                . " WHERE ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;

        if (!$tudo)
        {
            $sql .= " AND ".$this->_page->_db->tabelas["tipodado"]["nick"].".".$this->_page->_db->tabelas["tipodado"]["colunas"]["tabela"]." <> 'tbl_blob'";   
        }

        $res = $this->_page->_db->ExecSQL($sql);
        $row = $res->GetRows();

        for ($i=0; $i<sizeof($row); $i++)
        {
            if ($row[$i]['tabela']=='tbl_blob')
            {
                if (isset($this->_page->config["portal"]["uploadpath"]) && $this->_page->config["portal"]["uploadpath"]!="")
                {
                    $sql = "SELECT ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["cod_blob"]." AS cod_blob, "
                            . " ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["cod_blob"]." AS arquivo "
                            . " FROM ".$this->_page->_db->tabelas["tbl_blob"]["nome"]." "
                            . " WHERE ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
                    $res_blob = $this->_page->_db->ExecSQL($sql);
                    $row_blob = $res_blob->GetRows();

                    for ($j=0; $j<sizeof($row_blob); $j++)
                    {
                        $file_ext = Blob::PegaExtensaoArquivo($row_blob[$j]['arquivo']);
                        if (file_exists($this->_page->config["portal"]["uploadpath"]."/".Blob::identificaPasta($this->_page, $row_blob[$j]['cod_blob'])."/".$row_blob[$j]['cod_blob'].'.'.$file_ext))
                        {
                            $checkDelete = unlink($this->_page->config["portal"]["uploadpath"]."/".Blob::identificaPasta($this->_page, $row_blob[$j]['cod_blob'])."/".$row_blob[$j]['cod_blob'].'.'.$file_ext);
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
            
            $sql = "DELETE FROM ".$this->_page->_db->tabelas[$row[$i]['tabela']]["nome"]." "
                    . " WHERE ".$this->_page->_db->tabelas[$row[$i]['tabela']]["colunas"]["cod_objeto"]." = ".$cod_objeto;
            $this->_page->_db->ExecSQL($sql);
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
    function GravarPropriedades($cod_objeto, $cod_classe, $proplist, $array_files=array())
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
                        $info = $this->_page->_adminobjeto->PegaInfoSobrePropriedade($cod_classe, $ar_fld[1]);
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
                            
                            if ($this->_page->_db->config["bd"]["tipo"] == "oracle11")
                            {
                                $sql = "INSERT INTO ".$this->_page->_db->tabelas[$info['tabela']]["nome"]." ("
                                        . " ".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_propriedade"].", "
                                        . " ".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_objeto"].", "
                                        . " ".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["valor"]." "
                                        . " ) VALUES ( "
                                        . " ".$info['cod_propriedade'].", "
                                        . " ".$cod_objeto.", "
                                        . " :valor "
                                        . ")";
                                $bind = array("valor" => $this->_page->_db->Slashes($valor));
                            }
                            else
                            {
                                $sql = "INSERT INTO ".$this->_page->_db->tabelas[$info['tabela']]["nome"]." ("
                                        . "".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_propriedade"].", "
                                        . "".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_objeto"].", "
                                        . "".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["valor"].""
                                        . ") VALUES ("
                                        . "".$info['cod_propriedade'].", "
                                        . "".$cod_objeto.", "
                                        . " ? "
//                                        . "".$info['delimitador'].$this->_page->_db->Slashes($valor).$info['delimitador'].""
                                        . ")";
                                $bind = array(1 => $this->_page->_db->Slashes($valor));
                            }
                            $sql = $this->_page->_db->getCon()->prepare($sql);
                            $rs = $this->_page->_db->ExecSQL(array($sql, $bind));
                        }
                    }
                }
                else
                {
                    $ar_fld = explode("^", $ar_fld[1]);

                    $info = $this->_page->_adminobjeto->PegaInfoSobrePropriedade($cod_classe, $ar_fld[0]);

                    if ($info['tabela'] == "tbl_blob")
                    {
                        $sql = "SELECT "
                                . " ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["cod_blob"]." AS cod_blob, "
                                . " ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                                . " ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["cod_propriedade"]." AS cod_propriedade, "
                                . " ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["arquivo"]." AS arquivo, "
                                . " ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["tamanho"]." AS tamanho "
                                . " FROM ".$this->_page->_db->tabelas["tbl_blob"]["nome"]." "
                                . " WHERE ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["cod_propriedade"]." = ".$info["cod_propriedade"]." "
                                . " AND ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
                        $rs = $this->_page->_db->ExecSQL($sql);
                        while ($row = $rs->FetchRow())
                        {
                            $this->_page->_blob->apagaBlob($row['cod_blob'], $row['arquivo']);
                        }
                    }

                    $sql = "DELETE FROM ".$this->_page->_db->tabelas[$info['tabela']]["nome"]." "
                            . " WHERE ".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_propriedade"]." = ".$info['cod_propriedade']." "
                            . " AND ".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_objeto"]." = ".$cod_objeto;
                    $this->_page->_db->ExecSQL($sql);  
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
                    $info = $this->_page->_adminobjeto->PegaInfoSobrePropriedade($cod_classe, $prop);
                    
                    // Apaga registro, caso já exista
                    $sql = "DELETE FROM ".$this->_page->_db->tabelas[$info['tabela']]["nome"]." "
                            . " WHERE ".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_propriedade"]." = ".$info['cod_propriedade']." "
                            . " AND ".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_objeto"]." = ".$cod_objeto;
                    $this->_page->_db->ExecSQL($sql);
                    
                    if ($source=='post') $data = fread(fopen($valor['tmp_name'], "rb"), filesize($valor['tmp_name']));
                    else {
                        if (isset($valor['data'])) $data = stripslashes($valor['data']);
                        else $data = fread(fopen($valor['tmp_name'], "rb"), filesize($valor['tmp_name']));
                    }

                    // caso seja gravação do blob no banco
                    if (!isset($this->_page->config["portal"]["uploadpath"]) || $this->_page->config["portal"]["uploadpath"]=="")
                    {
                        $campo = gzcompress($data);
                        $sql = "INSERT INTO ".$this->_page->_db->tabelas[$info['tabela']]["nome"]." ("
                                . "".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_propriedade"].", "
                                . "".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_objeto"].", "
                                . "".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["valor"].", "
                                . "".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["arquivo"].", "
                                . "".$this->_page->_db->tabelas[$info['tabela']]["colunas"]["tamanho"].""
                                . ") values ("
                                . "".$info['cod_propriedade'].", "
                                . "".$cod_objeto.", "
                                . "".$info['delimitador'].$this->_page->_db->BlobSlashes($data).$info['delimitador'].", "
                                . "'".$valor['name']."', "
                                . "".filesize($valor['tmp_name']).")";
                        $this->_page->_db->ExecSQL($sql);
                    }
                    // gravação do arquivo em disco
                    else
                    {
                        $campos = array();
                        $campos[$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_propriedade"]] = (int)$info['cod_propriedade'];
                        $campos[$this->_page->_db->tabelas[$info['tabela']]["colunas"]["cod_objeto"]] = (int)$cod_objeto;
                        $campos[$this->_page->_db->tabelas[$info['tabela']]["colunas"]["arquivo"]] = strtolower($valor['name']);
                        $campos[$this->_page->_db->tabelas[$info['tabela']]["colunas"]["tamanho"]] = filesize($valor['tmp_name']);
                        $cod_blob = $this->_page->_db->Insert($this->_page->_db->tabelas[$info['tabela']]["nome"], $campos);
                        
                        // Chama o método de gravação de blob no disco
                        $this->_page->_blob->gravarBlob($valor, $cod_blob);
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
    function CriarObjeto($dados, $log = true, $array_files = array())
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
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["script_exibir"]] = isset($dados['script_exibir'])?htmlspecialchars($dados['script_exibir'], ENT_QUOTES, "UTF-8"):"";
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pai"]] = (int)htmlspecialchars($dados['cod_pai'], ENT_QUOTES, "UTF-8");
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["cod_classe"]] = (int)htmlspecialchars($dados['cod_classe'], ENT_QUOTES, "UTF-8");
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["cod_usuario"]] = isset($dados['cod_usuario'])?(int)htmlspecialchars($dados['cod_usuario'], ENT_QUOTES, "UTF-8"):$_SESSION['usuario']['cod_usuario'];
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pele"]] = isset($dados['cod_pele'])?(int)htmlspecialchars($dados['cod_pele'], ENT_QUOTES, "UTF-8"):"";
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["cod_status"]] = (int)htmlspecialchars($dados['cod_status'], ENT_QUOTES, "UTF-8");
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["titulo"]] = htmlspecialchars($dados['titulo'], ENT_QUOTES, "UTF-8");
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["descricao"]] = htmlspecialchars($dados['descricao'], ENT_QUOTES, "UTF-8");
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["data_publicacao"]] = ConverteData(htmlspecialchars($dados['data_publicacao'], ENT_QUOTES, "UTF-8"), 27);
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["data_validade"]] = ConverteData(htmlspecialchars($dados['data_validade'], ENT_QUOTES, "UTF-8"), 27);
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["peso"]] = (int)htmlspecialchars($dados['peso'], ENT_QUOTES, "UTF-8");
//        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["script_exibir"]'script_exibir'] = htmlspecialchars($dados['script_exibir'], ENT_QUOTES, "UTF-8");
        if ($dados['url_amigavel']=="") $dados['url_amigavel'] = limpaString($campos[$this->_page->_db->tabelas["objeto"]["colunas"]["titulo"]]);
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["url_amigavel"]] = $this->verificaExistenciaUrlAmigavel($dados['url_amigavel']);
        
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["apagado"]] = 0;
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["objetosistema"]] = 0;
        $campos[$this->_page->_db->tabelas["objeto"]["colunas"]["versao"]] = 1;
        
        if ($campos[$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pele"]]==0) { unset($campos[$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pele"]]); }
        
        $cod_objeto = $this->_page->_db->Insert($this->_page->_db->tabelas["objeto"]["nome"], $campos);
        
        
        // grava as propriedades do objeto
        $this->GravarPropriedades($cod_objeto, $dados['cod_classe'], $proplist, $array_files);
//        xd($proplist);
        // grava as relações de parentesco do objeto
        $this->CriaParentesco($cod_objeto, $dados['cod_pai']);
        // grava as tags
        $this->GravarTags($cod_objeto, $tagslist);
        
        // grava o log
        if ($log) $this->_page->_log->IncluirLogObjeto($cod_objeto, _OPERACAO_OBJETO_CRIAR);
        
        return $cod_objeto;
    }
    
    function GravaVersao($cod_objeto)
    {
        $obj = new Objeto($this->_page, $cod_objeto);
        $obj->PegaListaDePropriedades();
        $arr_obj = serialize($obj);
        $ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0');
        
        $sql = "";
        $bind = array();
        
        if ($this->_page->_db->config["bd"]["tipo"] == "oracle11")
        {
            $sql = "INSERT INTO ".$this->_page->_db->tabelas["versaoobjeto"]["nome"]." ("
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["cod_objeto"].", "
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["versao"].", "
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["conteudo"].", "
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["data_criacao"].", "
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["cod_usuario"].", "
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["ip"].""
                . ") VALUES ("
                . " :cod_objeto, "
                . " :versao, "
                . " :conteudo, "
                . " TO_TIMESTAMP(:data, 'YYYY-MM-DD HH24:MI:SS'), "
                . " :cod_usuario, "
                . " :ip )";
            $bind = array("cod_objeto" => $cod_objeto,
                "versao" => $obj->Valor("versao"),
                "conteudo" => $arr_obj, 
                "data" => date("Y-m-d H:i:s"),
                "cod_usuario" => $_SESSION["usuario"]["cod_usuario"],
                "ip" => $ip);
        }
        else
        {
            $sql = "INSERT INTO ".$this->_page->_db->tabelas["versaoobjeto"]["nome"]." ("
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["cod_objeto"].", "
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["versao"].", "
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["conteudo"].", "
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["data_criacao"].", "
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["cod_usuario"].", "
                . "".$this->_page->_db->tabelas["versaoobjeto"]["colunas"]["ip"].""
                . ") VALUES ("
                . " ?, "
                . " ?, "
                . " ?, "
                . " ?, "
                . " ?, "
                . " ? )";
            $bind = array(1 => $cod_objeto,
                2 => $obj->Valor("versao"),
                3 => $arr_obj, 
                4 => date("Y-m-d H:i:s"),
                5 => $_SESSION["usuario"]["cod_usuario"],
                6 => $ip);
        }
        
        $sql = $this->_page->_db->getCon()->prepare($sql);
        $rs = $this->_page->_db->ExecSQL(array($sql, $bind));

        $this->_page->_log->RegistraLogWorkFlow("Criada versão ".$obj->Valor("versao"), $cod_objeto, 1);
    }
    
    function cacheFlush()
    {
        GLOBAL $ADODB_CACHE_DIR;
        
        if ($this->_page->_db->config["bd"]["cache"] === true) 
        {
            if (defined("_DBCACHEPATH")) $ADODB_CACHE_DIR = _DBCACHEPATH;
            $this->_page->_db->getCon()->CacheFlush();
        }
    }

    /**
     * Grava tags do objeto no banco de dados
     * @param int $cod_objeto - Codigo do objeto
     * @param array $tagslist - Lista de tags
     */
    function GravarTags($cod_objeto, $tagslist)
    {
        if (is_array($tagslist) && count($tagslist)>=1)
        {
            $this->ApagarTags($cod_objeto);

            foreach ($tagslist as $tag)
            {
                $tag = trim($tag);
                $sql = "SELECT ".$this->_page->_db->tabelas["tag"]["colunas"]["cod_tag"]." AS cod_tag "
                        . " FROM ".$this->_page->_db->tabelas["tag"]["nome"]." "
                        . " WHERE ".$this->_page->_db->tabelas["tag"]["colunas"]["nome_tag"]." = '".$tag."'";
                $rs = $this->_page->_db->ExecSQL($sql);
                if ($rs->_numOfRows == 0)
                {
                    $cod_tag = $this->_page->_db->Insert($this->_page->_db->tabelas["tag"]["nome"], array($this->_page->_db->tabelas["tag"]["colunas"]["nome_tag"] => $tag));
                }
                else
                {
                    $row = $rs->FetchRow();
                    $cod_tag = $row["cod_tag"];
                }

                $sql = "INSERT INTO "
                        . " ".$this->_page->_db->tabelas["tagxobjeto"]["nome"]." ("
                        . "".$this->_page->_db->tabelas["tagxobjeto"]["colunas"]["cod_tag"].", "
                        . "".$this->_page->_db->tabelas["tagxobjeto"]["colunas"]["cod_objeto"].""
                        . ") VALUES ("
                        . "".$cod_tag.", "
                        . "".$cod_objeto.")";
                $rs = $this->_page->_db->ExecSQL($sql);
            }
        }
    }

    /**
     * Remove tags do objeto e do banco caso não tenha nenhum 
     * outro objeto utilizando
     * @param int $cod_objeto - Codigo do objeto
     */
    function ApagarTags($cod_objeto)
    {
        $sql = "DELETE FROM ".$this->_page->_db->tabelas["tagxobjeto"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["tagxobjeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $rs = $this->_page->_db->ExecSQL($sql);

        $sql = "DELETE FROM ".$this->_page->_db->tabelas["tag"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["tag"]["colunas"]["cod_tag"]." NOT IN (SELECT ".$this->_page->_db->tabelas["tagxobjeto"]["colunas"]["cod_tag"]." FROM ".$this->_page->_db->tabelas["tagxobjeto"]["nome"].")";
        $rs = $this->_page->_db->ExecSQL($sql);
    }
    
    /**
     * Remove pele do banco de dados e desativa de objetos
     * @param int $cod_pele - Codigo da pele
     */
    function ApagarPele($cod_pele)
    {
        $sql = "UPDATE ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                . "SET ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pele"]." = null "
                . "WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pele"]." = ".$cod_pele;
	$this->_page->_db->ExecSQL($sql);
	
	$sql = "DELETE FROM ".$this->_page->_db->tabelas["pele"]["nome"]." "
                . "WHERE ".$this->_page->_db->tabelas["pele"]["colunas"]["cod_pele"]." = ".$cod_pele;
	$this->_page->_db->ExecSQL($sql);
	
	return 0;
    }
    
    /**
     * Atualiza informações da pele
     * @param int $cod_pele
     * @param string $nome
     * @param string $prefixo
     * @param int $publica
     */
    function AtualizarPele($cod_pele, $nome, $prefixo, $publica)
    {
        $sql = "UPDATE ".$this->_page->_db->tabelas["pele"]["nome"]." "
                . " SET ".$this->_page->_db->tabelas["pele"]["colunas"]["nome"]." = '".$nome."', "
                . " ".$this->_page->_db->tabelas["pele"]["colunas"]["prefixo"]." = '".$prefixo."', "
                . " ".$this->_page->_db->tabelas["pele"]["colunas"]["publica"]." = ".$publica." "
                . " WHERE ".$this->_page->_db->tabelas["pele"]["colunas"]["cod_pele"]." = ".$cod_pele;
	$this->_page->_db->ExecSQL($sql);
    }
    
     /**
     * Cria pele
     * @param string $nome
     * @param string $prefixo
     * @param int $publica
     */
    function CriaPele($nome, $prefixo, $publica)
    {
        $campos=array();
        $campos[$this->_page->_db->tabelas["pele"]["colunas"]["nome"]] = $nome;
        $campos[$this->_page->_db->tabelas["pele"]["colunas"]["prefixo"]] = $prefixo;
        $campos[$this->_page->_db->tabelas["pele"]["colunas"]["publica"]] = $publica;
                
        return($this->_page->_db->Insert($this->_page->_db->tabelas["pele"]["nome"], $campos));
    }

    /**
     * Apaga lista de relação de parentesco de objeto
     * @param int $cod_objeto - Codigo do objeto
     */
    function ApagarParentesco($cod_objeto)
    {
        $this->_page->_db->ExecSQL("DELETE FROM ".$this->_page->_db->tabelas["parentesco"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." = ".$cod_objeto);
    }

    /**
     * Cria relação de parentesco entre objetos
     * @param int $cod_objeto - Codigo do objeto
     * @param int $cod_pai - Codigo do objeto pai
     */
    function CriaParentesco($cod_objeto, $cod_pai)
    {
        // duplica parentesco do objeto pai, incrementando o nível
        $sql = "INSERT INTO ".$this->_page->_db->tabelas["parentesco"]["nome"]." ("
                . "".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"].", "
                . "".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_pai"].", "
                . "".$this->_page->_db->tabelas["parentesco"]["colunas"]["ordem"].") "
                . " SELECT ".$cod_objeto.", "
                . "".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_pai"].", "
                . "".$this->_page->_db->tabelas["parentesco"]["colunas"]["ordem"]."+1 "
                . "FROM ".$this->_page->_db->tabelas["parentesco"]["nome"]." "
                . "WHERE ".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." = ".$cod_pai;
        $this->_page->_db->ExecSQL($sql);
        
        // cria parentesco entre objeto e o pai
        $sql = "INSERT INTO ".$this->_page->_db->tabelas["parentesco"]["nome"]." ("
                . " ".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"].", "
                . " ".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_pai"].", "
                . " ".$this->_page->_db->tabelas["parentesco"]["colunas"]["ordem"].""
                . ") "
                . "values (".$cod_objeto.", ".$cod_pai.", 1)";
        $this->_page->_db->ExecSQL($sql);
    }

    /**
     * Verifica se classe é indexavel
     * @param int $cod_classe - Codigo da classe
     * @return int - 0 ou 1
     */
    function ClasseIndexavel($cod_classe)
    {
        $this->CarregaClasses();
        return (in_array($cod_classe, $this->classesIndexaveis));
    }

    /**
     * Apaga objeto, fisicamente ou logicamente
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     * @param bool $definitivo - indica se deve apagar realmente, ou mandar para lixeira
     */
    function ApagarObjeto($cod_objeto, $definitivo = false)
    {
        if (!$definitivo)
        {
            $sql = "SELECT distinct ".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                    . " ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_status"]." AS cod_status "
                    . " FROM ".$this->_page->_db->tabelas["parentesco"]["nome"]." ".$this->_page->_db->tabelas["parentesco"]["nick"]." "
                    . " INNER JOIN ".$this->_page->_db->tabelas["objeto"]["nome"]." ".$this->_page->_db->tabelas["objeto"]["nick"]." "
                        . " ON ".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." = ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." "
                    . " WHERE (".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto." "
                    . " OR ".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." = ".$cod_objeto.") "
                    . " AND (".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["apagado"]." = 0"
                        . " OR ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["apagado"]." is null) ";
            $res = $this->_page->_db->ExecSQL($sql);

            while ($row = $res->FetchRow())
            {
                if ($row['cod_status'] == _STATUS_SUBMETIDO)
                {
                    $this->RemovePendencia("Removida pendência de publicação por remoção do objeto", $row['cod_objeto']);
                }
                
                $sql = "UPDATE ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                        . " SET ".$this->_page->_db->tabelas["objeto"]["colunas"]["apagado"]." = 1, "
                        . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["data_exclusao"]." = ".date("YmdHis")." "
                        . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$row['cod_objeto'];
                $this->_page->_db->ExecSQL($sql);
                
                $this->_page->_log->IncluirLogObjeto($cod_objeto,_OPERACAO_OBJETO_REMOVER);
            
                if ($row["cod_objeto"] != $cod_objeto)
                {
                    $this->ApagarObjeto($row["cod_objeto"], false);
                }
            }
        }
        else
        {
            $this->ApagarEmDefinitivo($cod_objeto);
        }

        $this->cacheFlush();
    }
    
    /**
     * Verifica se objeto é indexavel
     * @param int $cod_objeto - Codigo do objeto a verificar
     * @return int - 0 ou 1
     */
    function ObjetoIndexado($cod_objeto)
    {
        $sql = "select indexar from classe left join objeto on objeto.cod_classe=classe.cod_classe
                        where cod_objeto=$cod_objeto";
        $rs = $this->_page->_db->ExecSQL($sql);
        $row = $rs->fields;
        return $row['indexar'];
    }

    /**
     * Verifica se usuário é dono do objeto
     * @param int $cod_usuario - Codigo do usuario
     * @param int $cod_objeto - Codigo do objeto
     * @return boolean
     */
    function UsuarioEDono($cod_usuario, $cod_objeto)
    {
        $sql = "select cod_objeto from objeto where cod_objeto=$cod_objeto and cod_usuario=$cod_usuario";
        $rs = $this->_page->_db->ExecSQL($sql);
        if ($rs->_numOfRows > 0) return true;
        else return false;
    }

    /**
     * Busca informações sobre usuário que é dono de determinado objeto
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Dados do usuário dono do objeto
     */
    function QuemEDono($cod_objeto)
    {
        $sql = "select usuario.cod_usuario as cod_usuario, usuario.nome as nome, "
                . "usuario.email as email, usuario.login as login, usuario.chefia as chefia, "
                . "usuario.valido as valido from objeto inner join usuario "
                . "on usuario.cod_usuario = objeto.cod_usuario where cod_objeto=".$cod_objeto;
        $rs = $this->_page->_db->ExecSQL($sql);
        return $rs->GetRows(); 
    }

    /**
     * Rejeita publicação do objeto
     * @param string $mensagem - Mensagem de rejeição
     * @param int $cod_objeto - Codigo do objeto
     */
    function RejeitarObjeto($mensagem, $cod_objeto)
    {
        if (($_SESSION['usuario']['perfil']==_PERFIL_ADMINISTRADOR) || ($_SESSION['usuario']['perfil']==_PERFIL_EDITOR)
                || (($_SESSION['usuario']['perfil']==_PERFIL_AUTOR) && $this->UsuarioEdono($_SESSION['usuario']['cod_usuario'], $cod_objeto)))
        {
            $this->TrocaStatusObjeto($mensagem, $cod_objeto, _STATUS_REJEITADO);
            $this->_page->_db->ExecSQL("DELETE FROM ".$this->_page->_db->tabelas["pendencia"]["nome"]." "
                            . " WHERE ".$this->_page->_db->tabelas["pendencia"]["colunas"]["cod_objeto"]." = ".$cod_objeto);
        }
    }

    /**
     * Publica objeto
     * @param string $mensagem - mensagem de publicação
     * @param int $cod_objeto - Codigo do objeto
     */
    function PublicarObjeto($mensagem, $cod_objeto)
    {			
        if ($_SESSION['usuario']['perfil'] <= _PERFIL_EDITOR)
        {
//            xd($mensagem);
            $this->TrocaStatusObjeto($mensagem, $cod_objeto, _STATUS_PUBLICADO);
            $this->_page->_db->ExecSQL("DELETE FROM ".$this->_page->_db->tabelas["pendencia"]["nome"]." "
                            . " WHERE ".$this->_page->_db->tabelas["pendencia"]["colunas"]["cod_objeto"]." = ".$cod_objeto);

            if (defined("_avisoPublicacao") && _avisoPublicacao==true)
            {
                $objetoPublicado = new Objeto($this->_page, $cod_objeto);
                $array_objeto = null;
                $array_objeto[] = array($objetoPublicado->metadados["cod_objeto"], $objetoPublicado->metadados["titulo"]);
                $caminhoObjeto = $this->_page->_adminobjeto->PegaIDPai($cod_objeto, 100, array(0), array(), false);
                foreach ($caminhoObjeto as $codigo=>$titulo) 
                {
                    $array_objeto[] = array($codigo, $titulo[0]);
                }

                $mensagemEmail = "<html><head><title>Objeto Publicado</title></head>
                <body>
                Objeto publicado no site: <b>".$this->_page->config["portal"]["nome"]."</b><br>
                Data: ".date("d/m/Y H:i:s")."<br>
                Objeto: <a href=\"".$this->_page->config["portal"]["url"]."/index.php/content/view/".$array_objeto[0][0].".html\" target=\"_blank\">".$array_objeto[0][1]."</a><br><br>
                Caminho do objeto: <br>";

                for ($i=1; $i<sizeof($array_objeto); $i++) {
                    $mensagemEmail .= $i." - <a href=\"".$this->_page->config["portal"]["url"]."/index.php/content/view/".$array_objeto[$i][0].".html\" target=\"_blank\">".$array_objeto[$i][1]."</a><br>";
                }

                $mensagemEmail .= "<br><small>Mensagem gerada automaticamente. Nao responda.</small>
                </body></html>";

                $destinatario = _emailAvisoPublicacao;
                $remetente =  _remetenteAvisoPublicacao;
                $assunto = "Objeto publicado no site: ".$this->_page->config["portal"]["nome"];
                $wassent = EnviarEmail($remetente, $destinatario, $assunto, $mensagemEmail); 
            }
        }
    }

    /**
     * Despublica objeto
     * @param string $mensagem - Mensagem de despublicação
     * @param int $cod_objeto - Codigo do objeto
     */
    function DesPublicarObjeto($mensagem, $cod_objeto)
    {			
        if (($_SESSION['usuario']['perfil']==_PERFIL_ADMINISTRADOR) || ($_SESSION['usuario']['perfil']==_PERFIL_EDITOR))
        {
            $this->TrocaStatusObjeto($mensagem, $cod_objeto, _STATUS_PRIVADO);
        }
    }

    /**
     * Envia objeto para publicação, solicita publicação do objeto
     * @param string $mensagem - mensagem de solicitação de publicação
     * @param int $cod_objeto - Codigo do objeto
     */
    function SubmeterObjeto($mensagem, $cod_objeto)
    {
        $dadosObjeto = $this->_page->_adminobjeto->PegaDadosObjetoPeloID($cod_objeto);

        if ((($_SESSION['usuario']['perfil']==_PERFIL_AUTOR) || ($this->UsuarioEdono($_SESSION['usuario']['cod_usuario'],$cod_objeto))) && ($dadosObjeto['cod_status'] == _STATUS_PRIVADO))
        {
            $this->TrocaStatusObjeto($mensagem, $cod_objeto, _STATUS_SUBMETIDO);

            $sql = "select ".$_SESSION["usuario"]["chefia"]." as cod_usuario,".$cod_objeto." as cod_objeto from usuarioxobjetoxperfil inner join parentesco on (usuarioxobjetoxperfil.cod_objeto=parentesco.cod_pai or usuarioxobjetoxperfil.cod_objeto=parentesco.cod_objeto) where parentesco.cod_objeto=".$cod_objeto." group by cod_usuario, cod_usuario";
            $rs = $this->_page->_db->ExecSQL($sql, 1, 1);
            $campos = $rs->fields;

            $sql = "select * from pendencia where cod_usuario = ".$campos['cod_usuario']." and cod_objeto = ".$campos['cod_objeto'];
            $rs = $this->_page->_db->ExecSQL($sql);

            if (!$rs->GetRows())
            {
                $sql = "insert into pendencia(cod_usuario, cod_objeto) values (".$campos['cod_usuario'].", ".$campos['cod_objeto'].")";
                $this->_page->_db->ExecSQL($sql);
            }

            $EnviaEmailSolicitacao = $this->_page->_adminobjeto->EnviaEmailSolicitacao($_SESSION['usuario']['chefia'], $cod_objeto, $mensagem);
        }
    }

    /**
     * Remove solicitação de publicação
     * @param string $mensagem - mensagem de remoção da pendencia
     * @param int $cod_objeto - Codigo do objeto
     */
    function RemovePendencia($mensagem, $cod_objeto)
    {
        $this->TrocaStatusObjeto($mensagem, $cod_objeto, _STATUS_PRIVADO);
        $sql = "DELETE FROM ".$this->_page->_db->tabelas["pendencia"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["pendencia"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $this->_page->_db->ExecSQL($sql);
    }

    /**
     * Troca status do objeto
     * @param string $mensagem - Mensagem da troca de status
     * @param int $cod_objeto - Codigo do objeto
     * @param int $cod_status - Codigo do novo status
     */
    function TrocaStatusObjeto($mensagem, $cod_objeto, $cod_status)
    {
        if ($cod_objeto != $this->_page->config["portal"]["objroot"])
        {
            $this->_page->_db->ExecSQL("UPDATE ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                    . " SET ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_status"]." = ".$cod_status." "
                    . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto);
            $this->_page->_log->RegistraLogWorkFlow($mensagem, $cod_objeto, $cod_status);
            $this->cacheFlush();
        }
    }

    /**
     * Define status para criação do objeto conforme perfil do usuário
     * @return int - Codigo do status
     */
    function PegaStatusNovoObjeto()
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

    /**
     * Cria cópia de determinado objeto
     * @param int $cod_objeto - codigo do objeto a ser copiado
     * @param int $cod_pai - Codigo do objeto pai onde sera criado novo objeto
     */
    function CopiarObjeto($cod_objeto, $cod_pai)
    {
        $this->DuplicarObjeto($cod_objeto, $cod_pai);
        $this->RemoveObjetoDaPilha($cod_objeto);
    }

    /**
     * Move determinado objeto
     * @param int $cod_objeto - Codigo do objeto a ser movido
     * @param int $cod_pai - Codigo do objeto pai onde ficara objeto movido
     */
    function MoverObjeto($cod_objeto, $cod_pai)
    {
        if ($cod_objeto == -1)
        {
                $cod_objeto = $this->PegaPrimeiroDaPilha();
        }
        $sql = "UPDATE ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                . " SET ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pai"]." = ".$cod_pai." "
                . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $this->_page->_db->ExecSQL($sql);

        $this->ApagarParentesco($cod_objeto);
        $this->CriaParentesco($cod_objeto, $cod_pai);
        
        $this->_page->_log->RegistraLogWorkflow("Objeto movido para ".$cod_pai, $cod_objeto, _OPERACAO_OBJETO_MOVER);

        $sql = "SELECT ".$this->_page->_db->tabelas["objeto"]["nome"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pai"]." AS cod_pai, "
                . " ".$this->_page->_db->tabelas["objeto"]["nome"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto "
                . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                . " INNER JOIN ".$this->_page->_db->tabelas["parentesco"]["nome"]." "
                . " ON ".$this->_page->_db->tabelas["objeto"]["nome"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$this->_page->_db->tabelas["parentesco"]["nome"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." "
                . " WHERE ".$this->_page->_db->tabelas["parentesco"]["nome"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto." "
                . " GROUP BY ".$this->_page->_db->tabelas["objeto"]["nome"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"].", "
                . " ".$this->_page->_db->tabelas["objeto"]["nome"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_pai"];
        $res = $this->_page->_db->ExecSQL($sql);
        $row = $res->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
                $this->ApagarParentesco($row[$i]['cod_objeto']);
                $this->CriaParentesco($row[$i]['cod_objeto'], $row[$i]['cod_pai']);
        }

        $this->RemoveObjetoDaPilha($cod_objeto);
    }

    /**
     * Cola objeto da pilha como link
     * @param int $cod_objeto - codigo do objeto a ser colado como link
     * @param int $cod_pai - codigo do objeto que será pai do link
     */
    function ColarComoLink($cod_objeto, $cod_pai)
    {
        if ($cod_objeto == -1)
        {
            $cod_objeto = $this->PegaPrimeiroDaPilha();
        }

        $orig_obj = $this->_page->_adminobjeto->CriarObjeto($cod_objeto);
        $dados = $orig_obj->metadados;

        $status = $this->PegaStatusNovoObjeto();
        
        $cod_classe_interlink = $this->CodigoDaClasse("interlink");

        $campos=array();
        $campos['cod_pai'] = $cod_pai;
        $campos['cod_classe'] = $cod_classe_interlink;
        $campos['cod_usuario'] = $dados['cod_usuario'];
        $campos['cod_status'] = $dados['cod_status'];
        $campos['titulo'] = $this->_page->_db->Slashes($dados['titulo']);
        $campos['descricao'] = $this->_page->_db->Slashes($dados['descricao']);
        $campos['data_publicacao'] = ConverteData($dados['data_publicacao'],27);
        $campos['data_validade'] = ConverteData($dados['data_validade'],27);

        $novo_cod_objeto = $this->_page->_db->Insert('objeto',$campos);		
//        xd($novo_cod_objeto);

        $this->GravarPropriedades($novo_cod_objeto, $cod_classe_interlink, array('property___link'=>$cod_objeto));
        $this->RemoveObjetoDaPilha($cod_objeto);
        $this->CriaParentesco($novo_cod_objeto, $cod_pai);
    }

    /**
     * Pega primeiro objeto da pilha
     * @return int - codigo do primeiro objeto da pilha
     */
    function PegaPrimeiroDaPilha()
    {
        $sql = "SELECT ".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_objeto"]." AS cod_objeto "
                . " FROM ".$this->_page->_db->tabelas["pilha"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_usuario"]." = ".$_SESSION['usuario']['cod_usuario'];
        $rs = $this->_page->_db->ExecSQL($sql);
        $row = $rs->fields;
        
        return $row['cod_objeto'];
    }

    /**
     * Duplica objeto e seus filhos
     * @param int $cod_objeto - Codigo do objeto a duplicar
     * @param int $cod_pai - Codigo do objeto pai, onde ficara novo objeto
     * @return int - Codigo do novo objeto
     */
    function DuplicarObjeto($cod_objeto, $cod_pai=-1)
    {
        if ($cod_objeto == -1)
        {
            $cod_objeto = $this->PegaPrimeiroDaPilha();
        }

        $orig_obj = $this->_page->_adminobjeto->CriarObjeto($cod_objeto);
        $dados = $orig_obj->metadados;
        
        if ($cod_pai==-1) $cod_pai = $dados['cod_pai'];

        $campos = array();
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["script_exibir"]] = $dados['script_exibir'];
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["cod_pai"]] = $cod_pai;
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["cod_classe"]] = $dados['cod_classe'];
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["cod_usuario"]] = $dados['cod_usuario'];
        if (!is_null($dados['cod_pele'])) $campos[$this->_page->_db->tabelas['objeto']["colunas"]["cod_pele"]] = $dados['cod_pele'];
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["cod_status"]] = $dados['cod_status'];
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["titulo"]] = $this->_page->_db->Slashes($dados['titulo']);
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["descricao"]] = $this->_page->_db->Slashes($dados['descricao']);
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["data_publicacao"]] = ConverteData($dados['data_publicacao'],27);
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["data_validade"]] = ConverteData($dados['data_validade'],27);
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["url_amigavel"]] = $this->verificaExistenciaUrlAmigavel($dados['url_amigavel']);
        $campos[$this->_page->_db->tabelas['objeto']["colunas"]["peso"]] = $dados['peso'];

        $cod_objeto = $this->_page->_db->Insert($this->_page->_db->tabelas['objeto']["nome"], $campos);	
        $this->DuplicarPropriedades($cod_objeto, $orig_obj);
        $this->CriaParentesco($cod_objeto, $cod_pai);

        if ($orig_obj->PegaListaDeFilhos())
        {
            while ($childobj = $orig_obj->PegaProximoFilho())
            {
                $this->DuplicarObjeto($childobj->Valor("cod_objeto"), $cod_objeto);
            }
        }

        $this->_page->_log->IncluirLogObjeto($cod_objeto, _OPERACAO_OBJETO_CRIAR);
        
        $this->cacheFlush();
        
        return $cod_objeto;
    }

    /**
     * Duplica propriedades de determinado objeto em outro objeto
     * @param int $destino - codigo do objeto que recebera as propriedades
     * @param int $origem - codigo do objeto que tera proprieades duplicadas
     */
    function DuplicarPropriedades($destino, $origem)
    {
        $propriedades = $origem->PegaListaDePropriedades();
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
        $this->GravarPropriedades($destino, $origem->Valor("cod_classe"), $lista);
    }

    /**
     * Busca lista de classes que podem ser criadas abaixo de determinada classe
     * @param int $cod_classe - Codigo da classe a ser verificada
     * @return array - Lista de classes que podem ser criadas
     */
    function ListaDeClassesPermitidas($cod_classe)
    {
        $out=array();
        $sql = "SELECT "
                . " ".$this->_page->_db->tabelas["classexfilhos"]["nick"].".".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe_filho"]." AS cod_classe_filho, "
                . " ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["nome"]." AS nome, "
                . " ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["descricao"]." AS descricao, "
                . " ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["prefixo"]." AS prefixo "
                . " FROM ".$this->_page->_db->tabelas["classexfilhos"]["nome"]." ".$this->_page->_db->tabelas["classexfilhos"]["nick"]." "
                . " INNER JOIN ".$this->_page->_db->tabelas["classe"]["nome"]." ".$this->_page->_db->tabelas["classe"]["nick"]." "
                    . " ON ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"]." = ".$this->_page->_db->tabelas["classexfilhos"]["nick"].".".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe_filho"]." "
                . " WHERE ".$this->_page->_db->tabelas["classexfilhos"]["nick"].".".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe"]." = ".$cod_classe." "
                . " ORDER BY ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["nome"];
        $res = $this->_page->_db->ExecSQL($sql);
        return $res->GetRows();
    }
    
    /**
     * Verifica quais classes podem ser criadas abaixo de determinado objeto
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Lista de classes que podem ser criadas
     */
    function ListaDeClassesPermitidasNoObjeto($cod_objeto)
    {
        $out=array();
        $sql = "SELECT "
                . " ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"]." AS cod_classe, "
                . " ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["nome"]." AS nome, "
                . " ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["descricao"]." AS descricao, "
                . " ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["prefixo"]." AS prefixo "
                . " FROM ".$this->_page->_db->tabelas["classexobjeto"]["nome"]." ".$this->_page->_db->tabelas["classexobjeto"]["nick"]." "
                . " INNER JOIN ".$this->_page->_db->tabelas["classe"]["nome"]." ".$this->_page->_db->tabelas["classe"]["nick"]." "
                    . " ON ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"]." = ".$this->_page->_db->tabelas["classexobjeto"]["nick"].".".$this->_page->_db->tabelas["classexobjeto"]["colunas"]["cod_classe"]." "
                . " WHERE ".$this->_page->_db->tabelas["classexobjeto"]["nick"].".".$this->_page->_db->tabelas["classexobjeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto." "
                . " ORDER BY ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["nome"];
        $rs = $this->_page->_db->ExecSQL($sql);
        return $rs->GetRows();
    }

    /**
     * Envia objeto para pilha do usuario
     * @param int $cod_objeto - Codigo do objeto para ir para pilha
     */
    function CopiarObjetoParaPilha($cod_objeto)
    {
        $sql = "INSERT INTO ".$this->_page->_db->tabelas["pilha"]["nome"]." "
                . " (".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_objeto"].", ".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_usuario"].") "
                . " VALUES (".$cod_objeto.", ".$_SESSION['usuario']['cod_usuario'].")";
        $this->_page->_db->ExecSQL($sql);
    }

    /**
     * Remove objeto da pilha do usuario
     * @param int $cod_objeto - Codigo do objeto que deve sair da pilha
     */
    function RemoveObjetoDaPilha($cod_objeto, $user=1)
    {
        $sql = "DELETE FROM ".$this->_page->_db->tabelas["pilha"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        if ($user == 1)
        {
            $sql .= " AND ".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_usuario"]." = ".$_SESSION['usuario']['cod_usuario'];
        }
        $this->_page->_db->ExecSQL($sql);
    }

    /**
     * Limpa pilha do usuário
     */
    function LimparPilha()
    {
        $sql = "DELETE FROM ".$this->_page->_db->tabelas["pilha"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_usuario"]." =" .$_SESSION['usuario']['cod_usuario'];
        $this->_page->_db->ExecSQL($sql);
    }

    /**
     * Pega pilha do usuario logado
     * @return array - lista de objetos na pilha
     */
    function PegaPilha()
    {
        $result=array();
        $this->ContadorPilha=0;
        $sql = "SELECT ".$this->_page->_db->tabelas["pilha"]["nome"].".".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_objeto"]." AS codigo, "
                . ""
                . " ".$this->_page->_db->tabelas["objeto"]["nome"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["titulo"]." AS texto "
                . " FROM ".$this->_page->_db->tabelas["pilha"]["nome"]." "
                . " LEFT JOIN ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                    . " ON ".$this->_page->_db->tabelas["objeto"]["nome"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$this->_page->_db->tabelas["pilha"]["nome"].".".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_objeto"]." "
                . " WHERE ".$this->_page->_db->tabelas["pilha"]["nome"].".".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_usuario"]." = ".$_SESSION['usuario']['cod_usuario'];
        $rs = $this->_page->_db->ExecSQL($sql);
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
    function TemPilha()
    {
        if (!$this->ContadorPilha)
        {
            $sql = "SELECT COUNT(*) AS contador "
                    . " FROM ".$this->_page->_db->tabelas["pilha"]["nome"]." "
                    . " WHERE ".$this->_page->_db->tabelas["pilha"]["colunas"]["cod_usuario"]."=".$_SESSION['usuario']['cod_usuario'];
            $rs = $this->_page->_db->ExecSQL($sql);
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
    function DropDownPilha($selecionado='', $branco=false)
    {
        $lista = $this->PegaPilha();
        return $this->CriaDropDown($lista, $selecionado, $branco);
    }
    
    /**
     * Monta relacionamento entre classes e objetos, identificando os objetos onde a classe pode ser criada
     * @param int $cod_classe - Codigo da classe que será relacionada
     * @param array $relacao - Lista de códigos de objetos
     * @param array $relacaourl - Lista de urls amigáveis de objetos
     */
    function MontaRelacionamentoClassesObjetos($cod_classe, $relacao, $relacaourl)
    {
        // Atualiza lista de objetos onde pode ser criada
        $sql = "DELETE FROM ".$this->_page->_db->tabelas["classexobjeto"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["classexobjeto"]["colunas"]["cod_classe"]." = " . $cod_classe;
        $this->_page->_db->ExecSQL($sql);
        
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
                    $sql = "SELECT ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." as cod_objeto "
                            . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                            . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$cod;
                    $rs = $this->_page->_db->ExecSQL($sql);
                    if ($rs->_numOfRows > 0)
                    {
                        $objs[] = $cod;
                    }
                }
                else
                {
                    if ($url != "")
                    {
                        $sql = "SELECT ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." as cod_objeto "
                            . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                            . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["url_amigavel"]." = '".$url."'";
                        $rs = $this->_page->_db->ExecSQL($sql);
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
                $sql = "INSERT INTO ".$this->_page->_db->tabelas["classexobjeto"]["nome"]." "
                        . " (".$this->_page->_db->tabelas["classexobjeto"]["colunas"]["cod_classe"].", "
                        . " ".$this->_page->_db->tabelas["classexobjeto"]["colunas"]["cod_objeto"].") "
                        . " VALUES (".$cod_classe.", "
                        . " ".$obj.")";
                $this->_page->_db->ExecSQL($sql);
            }
        }
    }
    
    /**
     * Apaga e refaz relacionamento entre classes
     * @param int $cod_classe - Codigo da classe principal que esta tendo o relacionamento remontado
     * @param array $relacao - Codigos das classes que compoem o relacionamento
     * @param int $tipo - Informa tipo de relação: 1=contem, 2=está contido
     */
    function MontaRelacionamentoClasses($cod_classe, $relacao, $tipo)
    {
        // Apagando relação existente
        $sql = "DELETE FROM ".$this->_page->_db->tabelas["classexfilhos"]["nome"]." "
                . " WHERE ";
        if ($tipo == "1") $sql .= " ".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe"]." = " . $cod_classe;
        else $sql .= " ".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe_filho"]." = " . $cod_classe;
        $this->_page->_db->ExecSQL($sql);
        
        if (is_array($relacao) && count($relacao) > 0)
        {
            foreach ($relacao as $rel)
            {
                $cod = (int)htmlspecialchars($rel, ENT_QUOTES, "UTF-8");

                $sql = "INSERT INTO ".$this->_page->_db->tabelas["classexfilhos"]["nome"]." "
                        . " (".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe"].", "
                        . " ".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe_filho"].") ";
                if ($tipo=="1") $sql .= "VALUES (" . $cod_classe . ", " . $cod . ")";
                else $sql .= "VALUES (" . $cod . ", " . $cod_classe . ")";
                $this->_page->_db->ExecSQL($sql);
            }
        }
    }

    /**
     * Busca informações de determinada classe
     * @param int $cod_classe - Codigo da classe
     * @return array - lista com informações da classe
     */
    function PegaInfoDaClasse($cod_classe)
    {
        $sql = "SELECT ".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"]." AS cod_classe,  "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["nome"]." AS nome,  "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["prefixo"]." AS prefixo,  "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["descricao"]." AS descricao,  "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["temfilhos"]." AS temfilhos,  "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["sistema"]." AS sistema,  "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["indexar"]." AS indexar "
                . " FROM ".$this->_page->_db->tabelas["classe"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"]." = ".$cod_classe." "
                . " ORDER BY ".$this->_page->_db->tabelas["classe"]["colunas"]["nome"];
//        xd($sql);
        $rs = $this->_page->_db->ExecSQL($sql);
        $result['classe'] = $rs->fields;

        $sql = "SELECT ".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"]." AS cod_classe, "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["nome"]." AS nome "
                . " FROM ".$this->_page->_db->tabelas["classe"]["nome"]." "
                . " ORDER BY ".$this->_page->_db->tabelas["classe"]["colunas"]["nome"];
        $rs = $this->_page->_db->ExecSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result['todas'][$row[$i]['cod_classe']]=$row[$i];
        }

        $sql = "SELECT ".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe_filho"]." AS cod_classe_filho "
                . " FROM ".$this->_page->_db->tabelas["classexfilhos"]["nome"]." "
                . "WHERE ".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe"]." = ".$cod_classe;
        $rs = $this->_page->_db->ExecSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result['todas'][$row[$i]['cod_classe_filho']]['permitido']=true;
        }

        $sql = "SELECT ".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe"]." AS cod_classe "
                . " FROM ".$this->_page->_db->tabelas["classexfilhos"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["classexfilhos"]["colunas"]["cod_classe_filho"]." = ".$cod_classe;
        $rs = $this->_page->_db->ExecSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result['todas'][$row[$i]['cod_classe']]['criadoem']=true;
        }

        $prop = $this->PegaPropriedadesDaClasse($cod_classe);
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
                . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_classe"]." = ".$cod_classe;
        $rs = $this->_page->_db->ExecSQL($sql);
        $result['obj_conta'] = $rs->fields["cnt"];

        $sql = "SELECT "
                . " ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["titulo"]." AS titulo, "
                . " ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["url_amigavel"]." AS url_amigavel "
                . " FROM ".$this->_page->_db->tabelas["classexobjeto"]["nome"]." ".$this->_page->_db->tabelas["classexobjeto"]["nick"]." "
                . " INNER JOIN ".$this->_page->_db->tabelas["objeto"]["nome"]." ".$this->_page->_db->tabelas["objeto"]["nick"]." "
                . " ON ".$this->_page->_db->tabelas["classexobjeto"]["nick"].".".$this->_page->_db->tabelas["classexobjeto"]["colunas"]["cod_objeto"]." = ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." "
                . " WHERE ".$this->_page->_db->tabelas["classexobjeto"]["nick"].".".$this->_page->_db->tabelas["classexobjeto"]["colunas"]["cod_classe"]." = ".$cod_classe;
//                . " objeto.titulo, objeto.url_amigavel from classexobjeto "
//                . "inner join objeto on classexobjeto.cod_objeto=objeto.cod_objeto "
//                . "where classexobjeto.cod_classe=$cod_classe";
        $res = $this->_page->_db->ExecSQL($sql);
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
    function DropDownTipoDado($selecionado, $branco=false)
    {
        $lista = $this->PegaListaDeTipoDado();
        return $this->CriaDropDown($lista, $selecionado, $branco);
    }

    /**
     * Pega lista de tipos de dados no banco de dados
     * @return array - lista com tipos de dados
     */
    function PegaListaDeTipoDado()
    {
        $sql = "SELECT ".$this->_page->_db->tabelas["tipodado"]["colunas"]["cod_tipodado"]." AS codigo, "
                . " ".$this->_page->_db->tabelas["tipodado"]["colunas"]["nome"]." AS texto "
                . " FROM ".$this->_page->_db->tabelas["tipodado"]["nome"]." "
                . " ORDER BY ".$this->_page->_db->tabelas["tipodado"]["colunas"]["nome"];
        $rs = $this->_page->_db->ExecSQL($sql);
        return $rs->GetRows();
    }

    /**
     * Busca classes e envia valores para metodo que monta dropdown
     * @param int $selecionado - Valor que devera vir selecionado no dropdown
     * @param bool $branco - indica se devera ter <option> com value em branco
     * @return string - Lista de <option> para o <select>
     */
    function DropDownClasses($selecionado, $branco=false)
    {
        $lista = $this->PegaListaDeClasses($_page);
        return $this->CriaDropDown($lista, $selecionado, $branco);
    }
    
    /**
     * Pega lista de views nas pastas do site
     * @param object $_page - Referência de objeto da classe Pagina
     * @return array
     */
    function PegaListaDeViews(&$_page)
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
        $peles = $this->PegaListaDePeles();
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
     * @param object $_page - Referência de objeto da classe Pagina
     * @return array - lista de classes
     */
    function PegaListaDeClasses(&$_page)
    {
        $this->CarregaClasses();
        
        foreach ($this->classes as $cod => $dados)
                $saida[] = array ('codigo'=>$cod, 'texto'=> $dados["nome"]);

        return $saida;
    }

    /**
     * Cria elementos html checkboxes
     * @param string $nome - Nome do checkbox
     * @param array $lista - Lista de itens para checkboxes
     * @param string $codigo - Nome do campo do array onde esta o valor
     * @param string $texto - Nome do campo do array onde está o nome do chackbox
     * @param string $selecionado - Campo que devera vir checado
     * @param string $habilitado - Campo que indica se checkbox devera ser ativo ou desativado
     * @return string
     */
    function CriaCheckBox($nome,$lista,$codigo='codigo',$texto='texto',$selecionado='selecionado',$habilitado=true)
    {
        $txt = "";
        $result = "";
        if (!$habilitado) $txt=" disabled ";
        foreach ($lista as $item)
        {
            $result.= '<input '.$txt.'type="checkbox" name="'.$nome.'" value="'.$item[$codigo].'"';
            if (isset($item[$selecionado]) && $item[$selecionado]) $result.=" checked ";
            $result.='>'.$item[$texto]."<BR>";
        }
        return $result;
    }

    /**
     * Busca lista de prefixos das classes, excluindo classe informada
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe que nao deseja buscar prefixo
     * @return array - lista com prefixos
     */
    function PegaListaDePrefixos(&$_page, $cod_classe)
    {
        $result=array();
        $sql = "select prefixo from classe where cod_classe<>$cod_classe";
        $rs = $this->_page->_db->ExecSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result[]=$row[$i]['prefixo'];
        }
        return $result;
    }
    
    /**
     * Atualiza informações da classe
     * @param integer $cod_classe - Código da classe
     * @param array $dados - Dados da classe
     */
    function AtualizarClasse($cod_classe, $dados)
    {
         $sql = "UPDATE ".$this->_page->_db->tabelas["classe"]["nome"]." "
                . " SET ".$this->_page->_db->tabelas["classe"]["colunas"]["nome"]." = '" . $dados["nome"] . "', "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["prefixo"]." = '" . $dados["prefixo"] . "', "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["descricao"]." = '" . $dados["descricao"] . "', "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["temfilhos"]." = '" . $dados["temfilhos"] . "', "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["indexar"]." = '" . $dados["index"] . "' "
                . "WHERE ".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"]." = " . $cod_classe;
        $this->_page->_db->ExecSQL($sql);
    }
    
    /**
     * Cria classe no banco de dados
     * @param array $dados - Dados da classe
     * @return integer
     */
    function CriarClasse($dados)
    {
        $cod_classe = 0;
        
        $sql = "INSERT INTO ".$this->_page->_db->tabelas["classe"]["nome"]." "
                . " (".$this->_page->_db->tabelas["classe"]["colunas"]["nome"].", "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["prefixo"].", "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["descricao"].", "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["temfilhos"].", "
                . " ".$this->_page->_db->tabelas["classe"]["colunas"]["indexar"].") "
                . " VALUES ('" . $dados["nome"] . "', "
                . " '" . $dados["prefixo"] . "', "
                . " '" . $dados["descricao"] . "', "
                . " '" . $dados["temfilhos"] . "', "
                . " '" . $dados["index"] . "')";
        $this->_page->_db->ExecSQL($sql);
        
        $sql = "SELECT max(".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"].") AS cod FROM ".$this->_page->_db->tabelas["classe"]["nome"]."";
        $rs = $this->_page->_db->ExecSQL($sql);
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
    function ApagarPropriedadeDaClasse($cod_propriedade)
    {
        $sql = "SELECT ".$this->_page->_db->tabelas["tipodado"]["nome"].".".$this->_page->_db->tabelas["tipodado"]["colunas"]["tabela"]." AS tabela "
                . " FROM ".$this->_page->_db->tabelas["propriedade"]["nome"]." "
                . " LEFT JOIN ".$this->_page->_db->tabelas["tipodado"]["nome"]." "
                    . " ON ".$this->_page->_db->tabelas["tipodado"]["nome"].".".$this->_page->_db->tabelas["tipodado"]["colunas"]["cod_tipodado"]." = ".$this->_page->_db->tabelas["propriedade"]["nome"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_tipodado"]." "
                . " WHERE ".$this->_page->_db->tabelas["propriedade"]["nome"].".".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_propriedade"]." = ".$cod_propriedade;
        $rs = $this->_page->_db->ExecSQL($sql);
        $row = $rs->fields;

        if (isset($row['tabela']) && $row['tabela']!="")
        {
            if ($row['tabela'] == "tbl_blob")
            {
                $sql = "SELECT ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["cod_blob"]." AS cod_blob, "
                        . " ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["arquivo"]." AS arquivo "
                        . " FROM ".$this->_page->_db->tabelas["tbl_blob"]["nome"]." "
                        . " WHERE ".$this->_page->_db->tabelas["tbl_blob"]["colunas"]["cod_propriedade"]." = ".$cod_propriedade;
                $rs2 = $this->_page->_db->ExecSQL($sql);

                while ($row2 = $rs2->FetchRow())
                {
                    $file_ext = Blob::PegaExtensaoArquivo($row2['arquivo']);
                    if (file_exists($this->_page->config["portal"]["uploadpath"]."/".Blob::identificaPasta($this->_page, $row2['cod_blob'])."/".$row2['cod_blob'].'.'.$file_ext))
                    {
                        $checkDelete = unlink($this->_page->config["portal"]["uploadpath"]."/".Blob::identificaPasta($this->_page, $row2['cod_blob'])."/".$row2['cod_blob'].'.'.$file_ext);
                    }
                }
            }
            
            $sql = "DELETE FROM ".$this->_page->_db->tabelas[$row['tabela']]["nome"]." "
                    . " WHERE ".$this->_page->_db->tabelas[$row['tabela']]["colunas"]["cod_propriedade"]." = ".$cod_propriedade;
            $this->_page->_db->ExecSQL($sql);
        }
        
        $sql = "DELETE FROM ".$this->_page->_db->tabelas["propriedade"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_propriedade"]." = ".$cod_propriedade;
        $this->_page->_db->ExecSQL($sql);
    }

    /**
     * Renomeia propriedade
     * @param int $cod_classe - Codigo da classe
     * @param string $nomeatual - Nome atual da propriedade
     * @param string $nome - novo nome da propriedade
     */
    function RenomearPropriedadeDaClasse($cod_classe, $nomeatual, $nome)
    {
        $sql = "update propriedade set nome='$nome' where nome='$nomeatual' and cod_classe=$cod_classe";
        $this->_page->_db->ExecSQL($sql);
    }

    

    /**
     * Atualiza dados de propriedade ao criar ou alterar classe
     * @param int $cod_propriedade - Codigo da propriedade
     * @param array $dados - dados da proprieadde
     */
    function AtualizarDadosDaPropriedade($cod_propriedade, $dados)
    {
        $sql = "UPDATE ".$this->_page->_db->tabelas["propriedade"]["nome"]." SET ";
//        if(isset($dados["codrefclasse"]) && $dados["codrefclasse"]>0) 
//        {
            $sql .= " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_referencia_classe"]." = ".(!isset($dados["codrefclasse"]) || $dados["codrefclasse"]==0?"NULL":$dados["codrefclasse"]).", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["campo_ref"]." = '".$dados["camporef"]."', ";
//        }
        $sql .= " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["nome"]." = '".$dados['nome']."', "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["posicao"]." = ".$dados['posicao'].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["descricao"]." = '".$dados['descricao']."', "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["rotulo"]." = '".$dados['rotulo']."', "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["rot1booleano"]." = '".$dados['rot1booleano']."', "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["rot2booleano"]." = '".$dados['rot2booleano']."', "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["obrigatorio"]." = ".$dados['obrigatorio'].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["seguranca"]." = ".$dados['seguranca'].", "
                . " ".$this->_page->_db->tabelas["propriedade"]["colunas"]["valorpadrao"]." = '".$dados['valorpadrao']."' "
                . "WHERE ".$this->_page->_db->tabelas["propriedade"]["colunas"]["cod_propriedade"]." = ".$cod_propriedade;
//        xd($sql);
        $this->_page->_db->ExecSQL($sql);
    }

    /**
     * Busca lista de seções de usuários e envia retorno para funcao que monta dropdown
     * @param int $selecionado - Campo que deverá estar seleiconado
     * @param bool $branco - indica se deverá conter elemento <option> com value em branco
     * @return string - lista de <options> para popular <select>
     */
    function DropDownUsuarioSecao($selecionado=0, $branco=false){
        $lista = $this->PegaListaDeSecao($_page);
        return $this->CriaDropDown($lista,$selecionado,$branco,40);
    }

    /**
     * Busca lista de seçõas de usuários no banco
     * @param object $_page - Referência de objeto da classe Pagina
     * @return array - lista com seções
     */
    function PegaListaDeSecao(&$_page){
        $sql = "select DISTINCT secao as codigo, secao as texto from usuario "
                . "where valido=1 and secao <> '' order by secao";
        $rs = $this->_page->_db->ExecSQL($sql);
        return $rs->GetRows();
    }

    /**
     * Busca lista de usuarios e envia retorno para funcao que monta dropdown
     * @param int $selecionado - Campo que deverá estar seleiconado
     * @param bool $branco - indica se deverá conter elemento <option> com value em branco
     * @param string $secao - seção para buscar usuários
     * @return string - lista de <options> para popular <select>
     */
    function DropDownUsuarios($selecionado, $branco=false, $secao=NULL)
    {
        $lista = $this->_page->_usuario->PegaListaDeUsuarios($secao);
        return $this->CriaDropDown($lista,$selecionado,$branco,20);
    }

    /**
     * Apaga classe do banco de dados e objetos que pertencam a ela
     * @param int $cod_classe - Codigo da classe a ser apagada
     */
    function ApagarClasse($cod_classe)
    {
        // apagando a classe
        $sql  = "DELETE "
                . " FROM ".$this->_page->_db->tabelas["classe"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"]." = ".$cod_classe;
        $this->_page->_db->ExecSQL($sql);
    }
    
    /**
     * Cria view automaticamente para a classe, caso não exista na pasta de template do portal
     * @param type $cod_classe
     */
    function CriarTemplateClasse($cod_classe)
    {
        $dados = $this->PegaInfoDaClasse($cod_classe);
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
    function PegaPerfilDoUsuarioNoObjeto($cod_usuario, $cod_objeto)
    {
        if (empty($cod_usuario)) return false;
        $perfil = $this->_page->_usuario->PegaDireitosDoUsuario($cod_usuario);
        $caminho = explode(",", $this->_page->_adminobjeto->PegaCaminhoObjeto($cod_objeto));
        foreach ($perfil as $objeto => $cod_perfil)
        {
            if ((in_array($objeto, $caminho))) return $cod_perfil;
        }
        return false;
    }



    /**
     * Busca lista de objetos apagados logicamente
     * @param int $start - dados para paginação da busca
     * @param int $limit - dados para paginação da busca
     * @return array - Lista de objetos apagados
     */
    function PegaListaDeApagados($inicio, $start=-1, $limit=-1)
    {
        $out=array();
        $sql = "SELECT distinct ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["data_exclusao"]." AS data_exclusao, "
                . " ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["titulo"]." AS titulo, "
                . " ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                . " ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["nome"]." AS classe "
                . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." ".$this->_page->_db->tabelas["objeto"]["nick"]." "
//                . " LEFT JOIN ".$this->_page->_db->tabelas["parentesco"]["nome"]." ".$this->_page->_db->tabelas["parentesco"]["nick"]." "
//                . " ON ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_pai"]." "
                . " LEFT JOIN ".$this->_page->_db->tabelas["classe"]["nome"]." ".$this->_page->_db->tabelas["classe"]["nick"]." "
                . " ON ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_classe"]." = ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"]." "
                . " WHERE ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["apagado"]." = 1 "
                . " ORDER BY ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["data_exclusao"]." DESC";
        
        $rs = $this->_page->_db->ExecSQL($sql, $start, $limit);
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
    function PegaListaDeVencidos($ord1="titulo", $ord2="asc", $inicio=-1, $limite=-1, $cod_objeto=1)
    {
        $out=array();
        $sql = "SELECT ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["titulo"]." AS titulo, "
                . " ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                . " ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["data_validade"]." AS data_validade, "
                . " ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["nome"]." AS classe "
                . " FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." ".$this->_page->_db->tabelas["objeto"]["nick"]." "
                . " INNER JOIN ".$this->_page->_db->tabelas["classe"]["nome"]." ".$this->_page->_db->tabelas["classe"]["nick"]." "
                    . " ON ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_classe"]." = ".$this->_page->_db->tabelas["classe"]["nick"].".".$this->_page->_db->tabelas["classe"]["colunas"]["cod_classe"]." "
                . " INNER JOIN ".$this->_page->_db->tabelas["parentesco"]["nome"]." ".$this->_page->_db->tabelas["parentesco"]["nick"]." "
                    . " ON ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." "
                . " WHERE ".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto." "
                . " AND ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["data_validade"]." < ".date("Ymd")."000000 "
                . " AND ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["apagado"]." = 0 ";
        $rs = $this->_page->_db->ExecSQL($sql, $inicio, $limite);
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
    function ApagarLogObjeto($cod_objeto)
    {
        $sql = "DELETE FROM ".$this->_page->_db->tabelas["logobjeto"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["logobjeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $this->_page->_db->ExecSQL($sql);
    }
    
    /**
     * Apaga registros de log do objeto
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     */
    function ApagarLogWorkflow($cod_objeto)
    {
        $sql = "DELETE FROM ".$this->_page->_db->tabelas["logworkflow"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["logworkflow"]["colunas"]["cod_objeto"]." = ".$cod_objeto;
        $this->_page->_db->ExecSQL($sql);
    }

    /**
     * Apaga objeto em definitivo - fisicamente
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     */
    function ApagarEmDefinitivo($cod_objeto)
    {
        $sql = "SELECT ".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." AS cod_objeto "
                . " FROM ".$this->_page->_db->tabelas["parentesco"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto;
        $res=$this->_page->_db->ExecSQL($sql);
        $row = $res->GetRows();

        for ($c=0; $c<sizeof($row); $c++)
        {
            $this->ApagarEmDefinitivo($row[$c]["cod_objeto"]);
        }

        $this->_page->_db->ExecSQL("DELETE FROM ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto);
    }

    /**
     * Recupera objeto apagado logicamente
     * @param int $cod_objeto - Codigo do objeto a ser recuperado
     */
    function RecuperarObjeto($cod_objeto)
    {
        $sql = "SELECT distinct ".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." AS cod_objeto "
                    . " FROM ".$this->_page->_db->tabelas["parentesco"]["nome"]." ".$this->_page->_db->tabelas["parentesco"]["nick"]." "
                    . " INNER JOIN ".$this->_page->_db->tabelas["objeto"]["nome"]." ".$this->_page->_db->tabelas["objeto"]["nick"]." "
                        . " ON ".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." = ".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." "
                    . " WHERE (".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto." "
                    . " OR ".$this->_page->_db->tabelas["parentesco"]["nick"].".".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"]." = ".$cod_objeto.") "
                    . " AND (".$this->_page->_db->tabelas["objeto"]["nick"].".".$this->_page->_db->tabelas["objeto"]["colunas"]["apagado"]." = 1) ";
        $res = $this->_page->_db->ExecSQL($sql);

        while ($row = $res->FetchRow())
        {
            $sql = "UPDATE ".$this->_page->_db->tabelas["objeto"]["nome"]." "
                    . " SET ".$this->_page->_db->tabelas["objeto"]["colunas"]["apagado"]." = 0, "
                    . " ".$this->_page->_db->tabelas["objeto"]["colunas"]["data_exclusao"]." = null "
                    . " WHERE ".$this->_page->_db->tabelas["objeto"]["colunas"]["cod_objeto"]." = ".$row['cod_objeto'];
            $this->_page->_db->ExecSQL($sql);

            $this->_page->_log->IncluirLogObjeto($cod_objeto,_OPERACAO_OBJETO_RECUPERAR);

            if ($row["cod_objeto"] != $cod_objeto)
            {
                $this->RecuperarObjeto($row["cod_objeto"]);
            }
        }
    }

    /**
     * Verifica se propriedade tem preenchimento obrigatorio
     * @param int $cod_classe - Codigo da classe que propriedade pertence
     * @param array $propriedades - Lista de propriedades
     * @return boolean
     */
    function ValidarPropriedades($cod_classe, $propriedades)
    {
        $lista = $this->PegaPropriedadesDaClasse($cod_classe);
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
    function GravarObjeto($post, $acao, $publicar=0, &$cod)
    {
        $retorno = array();
        // executa scripts antes da gravacao do objeto
        
        $_POST = $post;
        $execAntes = $this->_page->_adminobjeto->ExecutaScript($post['cod_classe'], $post['cod_pele'], 'antes');
        $post = $_POST;
        
        $executa = false;
        $cod = 0;
        
//        xd($post);
        
        if ($acao=="create")
        {
            $cod = $this->CriarObjeto($post);
            $executa = true;
        }
        elseif ($acao=="edit")
        {
            $cod = $this->AlterarObjeto($post);
            $executa = true;
        }
        
        if ($executa === true)
        {
            
            $obj = new Objeto($this->_page, $cod);
            $this->GravaVersao($cod);
            $retorno["obj"] = $obj;
            
            if ($publicar==1)
            {
                $this->_page->_administracao->SubmeterObjeto('Solicitada publicação da versão '.$obj->Valor("versao"), $cod);
            }
            elseif ($publicar==2)
            {
                $this->_page->_administracao->PublicarObjeto('Publicada versão '.$obj->Valor("versao"), $cod);
            }
        }
        
        // chama a execução de scripts depois de gravar o objeto
        $execDepois = $this->_page->_adminobjeto->ExecutaScript($post['cod_classe'], $post['cod_pele'], 'depois');
        
        return $retorno;
    }

}
