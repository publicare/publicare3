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

/**
 * Classe para abstração de dados
 * Esta classe utiliza o ADODB.sf.net
 */
class DBLayer
{
    private $con = null;

    public $result;
    public $page_size=25;

    public $tabelas;
    public $metadados;
    public $tipodados;

    public $sqlobjsel;
    public $sqlobjfrom;
    public $sqlobj;
    public $sqltags;
    
    public $config;
		
    /**
     * Método construtor - Define variáveis e cria conexão com banco de dados
     */
    function __construct($config)
    {
        // pegando dados de conexao ao banco
        $this->config = $config;

	// criando alias para as tabelas
        // permite trabalhar de forma desacoplada ao nome das tabelas
        $this->tabelas = array(
            "classe" => array("nome" => "classe", "nick" => "t1", "colunas" => array("cod_classe" => "cod_classe", "nome" => "nome", "prefixo" => "prefixo", "descricao" => "descricao", "temfilhos" => "temfilhos", "sistema" => "sistema", "indexar" => "indexar")),
            "classexfilhos" => array("nome" => "classexfilhos", "nick" => "t2", "colunas" => array("cod_classe" => "cod_classe", "cod_classe_filho" => "cod_classe_filho")),
            "classexobjeto" => array("nome" => "classexobjeto", "nick" => "t3", "colunas" => array("cod_classe" => "cod_classe", "cod_objeto" => "cod_objeto")),
            "infoperfil" => array("nome" => "infoperfil", "nick" => "t4", "colunas" => array("cod_infoperfil" => "cod_infoperfil", "cod_perfil" => "cod_perfil", "acao" => "acao", "script" => "script", "donooupublicado" => "donooupublicado", "sopublicado" => "sopublicado", "sodono" => "sodono", "naomenu" => "naomenu", "ordem" => "ordem", "icone" => "icone")),
            "logobjeto" => array("nome" => "logobjeto", "nick" => "t5", "colunas" => array("cod_logobjeto" => "cod_logobjeto", "cod_objeto" => "cod_objeto", "estampa" => "estampa", "cod_usuario" => "cod_usuario", "cod_operacao" => "cod_operacao")),
            "logworkflow" => array("nome" => "logworkflow", "nick" => "t6", "colunas" => array("cod_logworkflow" => "cod_logworkflow", "cod_objeto" => "cod_objeto", "cod_usuario" => "cod_usuario", "mensagem" => "mensagem", "cod_status" => "cod_status", "estampa" => "estampa")),
            "objeto" => array("nome" => "objeto", "nick" => "t7", "colunas" => array("cod_objeto" => "cod_objeto", "cod_pai" => "cod_pai", "cod_classe" => "cod_classe", "cod_usuario" => "cod_usuario", "cod_pele" => "cod_pele", "cod_status" => "cod_status", "titulo" => "titulo", "descricao" => "descricao", "data_publicacao" => "data_publicacao", "data_validade" => "data_validade", "script_exibir" => "script_exibir", "apagado" => "apagado", "objetosistema" => "objetosistema", "peso" => "peso", "data_exclusao" => "data_exclusao", "url_amigavel" => "url_amigavel","versao" => "versao", "versao_publicada" => "versao_publicada")),
            "parentesco" => array("nome" => "parentesco", "nick" => "t8", "colunas" => array("cod_objeto" => "cod_objeto", "cod_pai" => "cod_pai", "ordem" => "ordem")),
            "pele" => array("nome" => "pele", "nick" => "t9", "colunas" => array("cod_pele" => "cod_pele", "nome" => "nome", "prefixo" => "prefixo", "publica" => "publica")), 
            "pendencia" => array("nome" => "pendencia", "nick" => "t10", "colunas" => array("cod_pendencia" => "cod_pendencia", "cod_usuario" => "cod_usuario", "cod_objeto" => "cod_objeto")), 
            "perfil" => array("nome" => "perfil", "nick" => "t11", "colunas" => array("cod_perfil" => "cod_perfil", "nome" => "nome", "cod_perfil_pai" => "cod_perfil_pai")),
            "pilha" => array("nome" => "pilha", "nick" => "t12", "colunas" => array("cod_pilha" => "cod_pilha", "cod_objeto" => "cod_objeto", "cod_usuario" => "cod_usuario", "cod_tipo" => "cod_tipo", "datahora" => "datahora")),
            "propriedade" => array("nome" => "propriedade", "nick" => "t13", "colunas" => array("cod_propriedade" => "cod_propriedade", "cod_classe" => "cod_classe", "cod_tipodado" => "cod_tipodado", "cod_referencia_classe" => "cod_referencia_classe", "campo_ref" => "campo_ref", "nome" => "nome", "posicao" => "posicao", "descricao" => "descricao", "rotulo" => "rotulo", "rot1booleano" => "rot1booleano", "rot2booleano" => "rot2booleano", "obrigatorio" => "obrigatorio", "seguranca" => "seguranca", "valorpadrao" => "valorpadrao")),
            "status" => array("nome" => "status", "nick" => "t14", "colunas" => array("cod_status" => "cod_status", "nome" => "nome")), 
            "tag" => array("nome" => "tag", "nick" => "t15", "colunas" => array("cod_tag" => "cod_tag", "nome_tag" => "nome_tag")),
            "tagxobjeto" => array("nome" => "tagxobjeto", "nick" => "t16", "colunas" => array("cod_tagxobjeto" => "cod_tagxobjeto", "cod_tag" => "cod_tag", "cod_objeto" => "cod_objeto")),
            "tbl_blob" => array("nome" => "tbl_blob", "nick" => "t17", "colunas" => array("cod_blob" => "cod_blob", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "arquivo" => "arquivo", "tamanho" => "tamanho")), 
            "tbl_boolean" => array("nome" => "tbl_boolean", "nick" => "t18", "colunas" => array("cod_boolean" => "cod_boolean", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
            "tbl_date" => array("nome" => "tbl_date", "nick" => "t19", "colunas" => array("cod_date" => "cod_date", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
            "tbl_float" => array("nome" => "tbl_float", "nick" => "t20", "colunas" => array("cod_float" => "cod_float", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
            "tbl_integer" => array("nome" => "tbl_integer", "nick" => "t21", "colunas" => array("cod_integer" => "cod_integer", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")),
            "tbl_objref" => array("nome" => "tbl_objref", "nick" => "t22", "colunas" => array("cod_objref" => "cod_objref", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
            "tbl_string" => array("nome" => "tbl_string", "nick" => "t23", "colunas" => array("cod_string" => "cod_string", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
            "tbl_text" => array("nome" => "tbl_text", "nick" => "t24", "colunas" => array("cod_text" => "cod_text", "cod_objeto" => "cod_objeto", "cod_propriedade" => "cod_propriedade", "valor" => "valor")), 
            "tipodado" => array("nome" => "tipodado", "nick" => "t25", "colunas" => array("cod_tipodado" => "cod_tipodado", "nome" => "nome", "tabela" => "tabela", "delimitador" => "delimitador")), 
            "usuario" => array("nome" => "usuario", "nick" => "t26", "colunas" => array("cod_usuario" => "cod_usuario", "secao" => "secao", "nome" => "nome", "login" => "login", "email" => "email", "ramal" => "ramal", "senha" => "senha", "chefia" => "chefia", "valido" => "valido", "data_atualizacao" => "data_atualizacao", "altera_senha" => "altera_senha", "ldap" => "ldap")), 
            "usuarioxobjetoxperfil" => array("nome" => "usuarioxobjetoxperfil", "nick" => "t27", "colunas" => array("cod_usuario" => "cod_usuario", "cod_objeto" => "cod_objeto", "cod_perfil" => "cod_perfil")),
            "versaoobjeto" => array("nome" => "versaoobjeto", "nick" => "t28", "colunas" => array("cod_versaoobjeto" => "cod_versaoobjeto", "cod_objeto" => "cod_objeto", "versao" => "versao", "conteudo" => "conteudo", "data_criacao" => "data_criacao", "cod_usuario" => "cod_usuario", "ip" => "ip"))
        );
        
        if (isset($this->config["bd"]["tabelas"]) && is_array($this->config["bd"]["tabelas"]) && count($this->config["bd"]["tabelas"]) > 0)
        {
            $this->tabelas = array_merge($this->tabelas, $this->config["bd"]["tabelas"]);
        }
		
	// definindo campos que sao metadados do objeto
        $this->metadados = array('cod_objeto', 'cod_pai', 'cod_usuario', 'cod_classe', 
            'classe', 'temfilhos', 'prefixoclasse', 'cod_pele', 'pele', 'prefixopele', 'cod_status', 
            'status', 'titulo', 'descricao', 'data_publicacao', 'data_validade', 'script_exibir', 
            'apagado', 'objetosistema', 'url', 'peso', 'tags', 'url_amigavel', 'versao',
            'versao_publicada'
        );
			
	// definindo tipos de dados para os bancos
        switch ($this->config["bd"]["tipo"]){
            // PostgreSQL
            case "postgres":
            case "pgsql":
                $this->tipodados = array("inteiro"=>"int",
                    "inteirogde"=>"bigint",
                    "inteiropqn"=>"smallint",
                    "float"=>"float",
                    "texto"=>"character varying(500)", 
                    "textogde"=>"text", 
                    "coluna"=>"column",
                    "temp"=>"CREATE TEMPORARY TABLE",
                    "temp2"=>"");
                break;
            // MySQL
            case "mysql":
            case "mysqli":
            case "pdo_mysql":
                $this->tipodados = array("inteiro"=>"int",
                    "inteirogde"=>"bigint(14)",
                    "inteiropqn"=>"tinyint",
                    "float"=>"float",
                    "texto"=>"varchar(255)", 
                    "textogde"=>"text", 
                    "coluna"=>"",
                    "temp"=>"CREATE TEMPORARY TABLE",
                    "temp2"=>"");
                break;
            // MICROSOFT SQL SERVER
            case "mssql":
                $this->tipodados = array("inteiro"=>"[int]",
                    "inteirogde"=>"[numeric](18, 0)",
                    "inteiropqn"=>"[tinyint]",
                    "float"=>"[numeric](18, 5)",
                    "texto"=>"[varchar](255)", 
                    "textogde"=>"[text]", 
                    "coluna"=>"",
                    "temp"=>"CREATE TABLE",
                    "temp2"=>"#");
                break;
            case "oracle11":
                $this->tipodados = array("inteiro"=>"number",
                    "inteirogde"=>"number(18,0)",
                    "inteiropqn"=>"number(3,0)",
                    "float"=>"number(18, 5)",
                    "texto"=>"varchar2(255)", 
                    "textogde"=>"long", 
                    "coluna"=>"",
                    "temp"=>"CREATE TABLE",
                    "temp2"=>"");
                break;
        }
		
	// definindo sql geral de consulta
        $this->sqlobjsel = " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto, "
                . $this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["cod_pai"]." AS cod_pai, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["cod_classe"]." AS cod_classe, "
                . " ".$this->tabelas["classe"]["nick"].".".$this->tabelas["classe"]["colunas"]["nome"]." AS classe, "
                . " ".$this->tabelas["classe"]["nick"].".".$this->tabelas["classe"]["colunas"]["temfilhos"]." AS temfilhos, "
                . " ".$this->tabelas["classe"]["nick"].".".$this->tabelas["classe"]["colunas"]["prefixo"]." AS prefixoclasse, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["cod_usuario"]." AS cod_usuario, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["cod_pele"]." AS cod_pele, "
                . " ".$this->tabelas["pele"]["nick"].".".$this->tabelas["pele"]["colunas"]["nome"]." AS pele, "
                . " ".$this->tabelas["pele"]["nick"].".".$this->tabelas["pele"]["colunas"]["prefixo"]." AS prefixopele, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["cod_status"]." AS cod_status, "
                . " ".$this->tabelas["status"]["nick"].".".$this->tabelas["status"]["colunas"]["nome"]." AS status, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["titulo"]." AS titulo, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["descricao"]." AS descricao, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["data_publicacao"]." AS data_publicacao, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["data_validade"]." AS data_validade, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["script_exibir"]." AS script_exibir, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["apagado"]." AS apagado, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["objetosistema"]." AS objetosistema, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["peso"]." AS peso, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["url_amigavel"]." AS url_amigavel, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["versao"]." AS versao, "
                . " ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["versao_publicada"]." AS versao_publicada ";
	
	// definindo clausula from do sql geral de consulta
        $this->sqlobjfrom = " FROM ".$this->tabelas["objeto"]["nome"]." ".$this->tabelas["objeto"]["nick"]." "
                . "LEFT JOIN ".$this->tabelas["classe"]["nome"]." ".$this->tabelas["classe"]["nick"]." "
                    . "ON ".$this->tabelas["classe"]["nick"].".".$this->tabelas["classe"]["colunas"]["cod_classe"]." = ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["cod_classe"]." "
                . "LEFT JOIN ".$this->tabelas["pele"]["nome"]." ".$this->tabelas["pele"]["nick"]." "
                    ."ON ".$this->tabelas["pele"]["nick"].".".$this->tabelas["pele"]["colunas"]["cod_pele"]." = ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["cod_pele"]." "
                . "LEFT JOIN ".$this->tabelas["status"]["nome"]." ".$this->tabelas["status"]["nick"]." "
                    . "ON ".$this->tabelas["status"]["nick"].".".$this->tabelas["status"]["colunas"]["cod_status"]." = ".$this->tabelas["objeto"]["nick"].".".$this->tabelas["objeto"]["colunas"]["cod_status"]." ";
	
	// criando sql geral de consulta de objetos
        $this->sqlobj = "SELECT ".$this->sqlobjsel." ".$this->sqlobjfrom;
		
        try {
            if ($this->config["bd"]["tipo"] == "oracle11")
            {
                define('ADODB_ASSOC_CASE', 0);
                putenv("NLS_COMP=LINGUISTIC");
                putenv("NLS_LANG=AMERICAN_AMERICA.AL32UTF8");
                putenv("NLS_SORT=BINARY_CI");
                $this->con = ADONewConnection("oci8");
            }
            else
            {
                $this->con = ADONewConnection($this->config["bd"]["tipo"]);
            }
            $this->con->debug = $this->config["bd"]["debug"];
            
            if ($this->config["bd"]["cache"] === true)
            {
                if ($this->config["bd"]["cachetipo"] == "disco")
                {
                    if ($this->config["bd"]["cachepath"] != "") 
                    {
                        try {
                            if (!is_dir($this->config["bd"]["cachepath"]))
                            {
                                mkdir($this->config["bd"]["cachepath"], 0775, true);
                            }
                            $ADODB_CACHE_DIR = $this->config["bd"]["cachepath"];
                        } catch (Exception $e) {
                            $this->config["bd"]["cache"] = false;
                        }
                    }
                }
                elseif ($this->config["bd"]["cachetipo"] == "memoria")
                {
                    $this->con->memCache = true;
                    $hosts = preg_split("[,]", $this->config["bd"]["cachehost"]);
                    $this->con->memCacheHost = $hosts;
                    $this->con->memCachePort = $this->config["bd"]["cacheporta"];
                    $this->con->memCacheCompress = $this->config["bd"]["cachecompress"];
                }
            }
            
            if ($this->config["bd"]["tipo"] == "oracle11")
            {
                
                $this->con->Connect($this->config["bd"]["host"].":".$this->config["bd"]["porta"], $this->config["bd"]["usuario"], $this->config["bd"]["senha"], $this->config["bd"]["nome"]) or die("Erro ao tentar conectar banco de dados");
            }
            else
            {
                $this->con->Connect($this->config["bd"]["host"].":".$this->config["bd"]["porta"], $this->config["bd"]["usuario"], $this->config["bd"]["senha"], $this->config["bd"]["nome"]) or die("Erro ao tentar conectar banco de dados");
            }
            
            switch ($this->config["bd"]["tipo"])
            {
                
                case "postgres":
                case "pgsql":
                    $this->con->Execute("SET CLIENT_ENCODING TO 'UTF8'");
                    break;
                case "mysql":
                case "mysqli":
                case "pdo_mysql":
                    $this->con->Execute("set names utf8");
                    break;
                case "mssql":
                    break;
                case "oracle11":
//                    $this->con->Execute("ALTER SESSION SET NLS_SORT=BINARY_CI;");
                    break;
            }
            $this->con->SetFetchMode(ADODB_FETCH_ASSOC);
//				ini_get_all();
        } catch (exception $e) {
            echo "Erro ao conectar banco de dados";
// 				echo "<pre>";
// 				var_dump($e);
// 				adodb_backtrace($e->gettrace());
// 				echo "</pre>";
            exit();
        } 
    }
	
    /**
     * Método destrutor - Fecha conexão com banco de dados
     */
    function __destruct()
    {
        $this->Close();
    }
    
    public function getCon()
    {
        return $this->con;
    }
    
    public function getConfig()
    {
        return $this->config;
    }
	
    /**
     * Cria array com nome e colunas iniciais para tabela temporária
     * @return array - Dados iniciais da tabela temporária
     */
    function GetTempTable()
    {
        $tablename = "temp_".mt_rand(1,300).date("U");

        $tabelaTemp = array();
        $tabelaTemp["nome"] = $tablename;

        $tabelaTemp["colunas"] = array("cod_objeto ".$this->tipodados["inteiro"]." NOT NULL",
                                        "cod_pai ".$this->tipodados["inteiro"]." NULL",
                                        "cod_classe ".$this->tipodados["inteiro"]." NULL",
                                        "classe ".$this->tipodados["texto"]." NULL",
                                        "temfilhos ".$this->tipodados["inteiro"]." NULL",
                                        "prefixoclasse ".$this->tipodados["texto"]." NULL",
                                        "cod_usuario ".$this->tipodados["inteiro"]." NULL",
                                        "cod_pele ".$this->tipodados["inteiro"]." NULL",
                                        "pele ".$this->tipodados["texto"]." NULL",
                                        "prefixopele ".$this->tipodados["texto"]." NULL",
                                        "cod_status ".$this->tipodados["inteiro"]." NULL",
                                        "status ".$this->tipodados["texto"]." NULL",
                                        "titulo ".$this->tipodados["texto"]." NULL",
                                        "descricao ".$this->tipodados["texto"]." NULL",
                                        "data_publicacao ".$this->tipodados["inteirogde"]." NULL",
                                        "data_validade ".$this->tipodados["inteirogde"]." NULL",
                                        "script_exibir ".$this->tipodados["texto"]." NULL",
                                        "apagado ".$this->tipodados["inteiropqn"]." NULL",
                                        "objetosistema ".$this->tipodados["inteiropqn"]." NULL",
                                        "peso ".$this->tipodados["inteiro"]." NULL",
                                        "url_amigavel ".$this->tipodados["texto"]." NULL",
                                        "versao ".$this->tipodados["inteiro"]." NULL",
                                        "versao_publicada ".$this->tipodados["inteiro"]." NULL");
        return $tabelaTemp;
    }
	
    /**
     * Executa SQL
     * @param string $sql - SQL a ser executado
     * @param int $start - Inicio dos registros, usado para paginação
     * @param int $limit - numero de registros, usado para paginação
     * @return ResultSet
     */
    function ExecSQL($sql, $start=-1, $limit=-1)
    {
        GLOBAL $ADODB_CACHE_DIR;
        
        if ($limit != -1)
        {
            if ($start == -1)
            {
                $start = 0;
            }
        }

        if ($limit != -1 && $start != -1)
        {
            if ($this->config["bd"]["cache"] === true && stripos($sql, "insert into") === false)
            {
                if ($this->config["bd"]["cachepath"] != "") $ADODB_CACHE_DIR = $this->config["bd"]["cachepath"];
                if (is_array($sql)) $this->con->CacheSelectLimit($this->config["bd"]["cachetempo"], $sql[0], $limit, $start, $sql[1]);
                else $this->result = $this->con->CacheSelectLimit($this->config["bd"]["cachetempo"], $sql, $limit, $start); 
            }
            else
            {
                if (is_array($sql)) $this->con->SelectLimit($sql[0], $limit, $start, $sql[1]);
                else $this->result = $this->con->SelectLimit($sql, $limit, $start);
            }
        }
        else 
        {
            if ($this->config["bd"]["cache"] === true && stripos($sql, "insert into") === false)
            {
                if ($this->config["bd"]["cachepath"] != "") $ADODB_CACHE_DIR = $this->config["bd"]["cachepath"];
                if (is_array($sql)) $this->result = $this->con->CacheExecute($this->config["bd"]["cachetempo"], $sql[0], $sql[1]);
                else $this->result = $this->con->CacheExecute($this->config["bd"]["cachetempo"], $sql);
            }
            else
            {
                if (is_array($sql)) $this->result = $this->con->Execute($sql[0], $sql[1]);
                else $this->result = $this->con->Execute($sql);
            }
        }

        return $this->result;
    }
		
    /**
     * Retorna array do result set
     * @param ResultSet $rs
     * @return array
     */
    function FetchAssoc($rs='')
    {
        $ret = false;
        if ($rs==null || $rs=="") $rs = $this->result;
        if ($rs->_numOfRows>0) $ret = $rs->GetRows();
        return $ret;
    }
	
    /**
     * Retorna TimeStamp publicare (YmdHis) atual
     * @return int
     */
    function TimeStamp()
    {
        return date("YmdHis");
    }
	
    /**
     * Apaga tabela temporária
     * @param string $tbl - Nome da tabela
     */
    function DropTempTable($tbl)
    {
        $sql = 'drop table '.$tbl;
        $this->ExecSQL($sql);	
    }
	
    /**
     * Adiciona coluna a tabela temporária
     * @param array $tbl - Dados da tabela temporária
     * @param array $field - Dados da cluna a adicionar
     * @return string
     */
    function AddFieldToTempTable($tbl, $field)
    {
        if (strpos($field['field'],'.')!==false)
        {
            $field['field']=substr($field['field'],0,strpos($field['field'],'.'));
        }
        $txt = null;
//        xd($field['type']);
        switch (trim($field['type']))
        {
            case 'data':
            case 'Data':
                $txt = $field['field'].' '.$this->tipodados["inteirogde"].' NULL';
                break;
            case 'número preciso':
            case 'Número Preciso':
                $txt = $field['field'].' '.$this->tipodados["float"].' NULL';
                break;
            case 'número':
            case 'Número':
                $txt = $field['field'].' '.$this->tipodados["inteiro"].' NULL';
                break;
            case 'ref_objeto':
            case 'Ref. Objeto':
            case 'string':
            case 'String':
                $txt = $field['field'].' '.$this->tipodados["texto"].' NULL';
                break;
            case 'texto avanc.':
            case 'Texto Avanc.':
                $txt = $field['field'].' '.$this->tipodados["textogde"].' NULL';
                break;
            case 'boolean':
            case 'Booleano':
                $txt = $field['field'].' '.$this->tipodados["inteiropqn"].' NULL';
                break;
        }
        return $txt;
    }

    /**
     * Remove caracteres especiais de array
     * @param array $array
     * @return array
     */
    function SpecialChars($array)
    {
        foreach ($array as $key=>$value)
        {
            $result[$key] = $this->Slashes($value, "\27");
        }
        return $result;
    }
    
    /**
     * Adiciona "\" antes de aspas
     * @param string $str
     * @return string
     */
    function Slashes($str)
    {
        $str = stripslashes($str);
        if ($this->config["bd"]["tipo"] == "mysql") return addslashes($str);
        if ($this->config["bd"]["tipo"] == "mysqli") return addslashes($str);
        return str_replace("'", "''", $str);
    }

    function Month($field)
    {
        return "(floor($field/100000000))";
    }

    function Day($field)
    {
        return "(floor($field/1000000))";
    }

    function Hour($field)
    {
        return "(floor($field/10000))";
    }

    function CreateDateTest($field,$condition,$value)
    {
        $zero_count=14-strlen($value);
        return "floor(".$field."/1".str_repeat("0",$zero_count).")".$condition.$value;
    }

    /**
     * Fecha conexão com banco e libera recursos
     */
    function Close()
    {
//        $this->con->CacheFlush();
        $this->con->Close();
    }

    /**
     * Executa insert no banco de dados
     * @param string $table - nome da tabela
     * @param array $fields - Campos e valores a serem inseridos
     * @return int - Codigo do registro inserido
     */
    function Insert($table, $fields)
    {
        $values = array();
        foreach ($fields as $value)
        {
            if (is_int($value)) $values[]=$value;
            else $values[]="'".$this->EscapeString($value)."'";		
        }

        $sql = sprintf("INSERT INTO %s (%s) VALUES(%s)",$table, implode(',',array_keys($fields)), implode(',',$values));

        if ($this->Query($sql)) return $this->InsertID($table);
        else return false;
    }
    
    /**
     * Retorna ID do registro inserido
     * @param string $table - nome da tabela
     * @return int - Codigo
     */
    function InsertID($table)
    {
        foreach ($this->tabelas as $id => $tab)
        {
            if ($tab["nome"]==$table)
            {
                $id2 = $id;
                if (substr($id, 0, 4)=="tbl_")
                {
                    $id2 = substr($id, 4);
                }
                $sql = "SELECT MAX(".$tab["colunas"]["cod_".$id2].") as cod FROM ".$table;
                $this->con->SetFetchMode(ADODB_FETCH_ASSOC);
                $this->result=$this->con->Execute($sql);
                return $this->result->fields["cod"];
            }
        }
        return false;
//        xd($table);
//        return $this->con->insert_Id();
//        $table2 = $table;
//        if (!($table == "index_word")){
//            $arr_temp = explode("_", $table);
//            if (sizeof($arr_temp)>=2) $table = $arr_temp[(sizeof($arr_temp)-1)];
//        } 		
//        $sql = "select max(cod_".$table.") as cod from ".$table2;
//        $this->con->SetFetchMode(ADODB_FETCH_ASSOC);
//        $this->result=$this->con->Execute($sql);
//        return $this->result->fields["cod"];
    }
	
    /**
     * Escapa strings
     * @param string $value
     * @return string
     */
    function EscapeString($value)
    {
        return $this->Slashes($value);
    }
	
    /**
     * Executa SQL
     * @param string $sql
     * @return ResultSet
     */
    function Query($sql)
    {
        $res = $this->con->Execute($sql);
        return $res;
    }
	
    /**
     * Cria condição WHERE pra consultas
     * @param string $field - Coluna da tabela
     * @param array $ar_values - Valores
     * @return string
     */
    function CreateTest($field, $ar_values)	
    {
        $sql = '';
        //x($ar_values);
        //x($field);
        foreach ($ar_values as $value)
        {

            if ($value != '')
            {
                if ($sql !='') $sql .= ' or ';
                if (is_numeric($value)) $sql .= "$field=$value";
                else $sql .="LOWER($field)='$value'";
            }
        }
        if ($sql!='') $sql = '('.$sql.')';
        else return " 1=1 ";
        return $sql;
    }
} 

