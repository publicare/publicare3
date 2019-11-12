<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Classe Administração, responsável por administrar os objetos (criar, editar objetos e classes)
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
 * Classe que contém métodos para manipulação de objetos
 */
class Administracao
{
    public $classesPrefixos;
    public $classesNomes;
    public $classesIndexaveis;
    public $_index;

    /**
     * Método construtor da classe Administracao.class.php
     * @param object $_page - Referência de objeto da classe Pagina
     */
    function __construct(&$_page)
    {
        $this->metadados = $_page->_db->metadados;
        $this->classesIndexaveis = array();
    }
    
    /**
     * Adiciona propriedade em classe
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe
     * @param array $novo - Dados da propriedade
     */
    function AcrescentarPropriedadeAClasse(&$_page, $cod_classe, $novo)
    {
        $sql = "INSERT INTO propriedade (cod_classe, "
                . "cod_tipodado, "
                . "cod_referencia_classe, "
                . "campo_ref, "
                . "nome, "
                . "posicao, "
                . "rotulo, "
                . "descricao, "
                . "obrigatorio, "
                . "seguranca, "
                . "valorpadrao, "
                . "rot1booleano, "
                . "rot2booleano) "
                . "VALUES (".$cod_classe.", "
                . "".$novo['tipodado'].", "
                . "".($novo['codrefclasse']==0?"NULL":$novo['codrefclasse']).", "
                . "'".$novo['camporef']."', "
                . "'".$novo['nome']."', "
                . "".$novo['posicao'].", "
                . "'".$novo['rotulo']."', "
                . "'".$novo['descricao']."', "
                . "".$novo['obrigatorio'].", "
                . "".$novo['seguranca'].", "
                . "'".$novo['valorpadrao']."', "
                . "'".$novo['rot1booleano']."', "
                . "'".$novo['rot2booleano']."')";
        $_page->_db->ExecSQL($sql);
    }
    
    /**
     * Altera a lista de objetos que podem conter objetos de determinada classe
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe
     * @param array $lista - Array com códigos dos objetos
     */
    function AlterarListaDeObjetosQueContemClasse(&$_page, $cod_classe, $lista)
    {
        $sql = 'delete from classexobjeto 
        where cod_classe='.$cod_classe;
        $_page->_db->ExecSQL($sql);	

        if (is_array($lista))
        {
            foreach ($lista as $item)
            {
                $_page->_db->ExecSQL("insert into classexobjeto(cod_classe,cod_objeto) values(".$cod_classe.",".$item.")");
            }
        }
    }
    
    /**
     * Altera objeto no banco de dados
     * @param object $_page - Referência de objeto da classe Pagina
     * @param array $dados - Dados do objeto
     * @param bool $log - Indica se deve gerar log ou não
     * @return int - Código do objeto alterado
     */
    function AlterarObjeto(&$_page, $dados, $log = true)
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
        $sql_pele = "SELECT cod_pele "
                . "FROM objeto "
                . "WHERE cod_objeto=".$cod_objeto;
        $row_pele = $_page->_db->ExecSQL($sql_pele);
        $row_pele = $row_pele->GetRows();
        $row_pele = $row_pele[0];
        if (is_array($row_pele) && $row_pele['cod_pele'] != $cod_pele)
        {
            $this->TrocaPeleFilhos($_page, $cod_objeto, $cod_pele);
        }

        // Objeto root deverá ser sempre publicado
        if ($cod_objeto == 1 || $cod_objeto == _ROOT)
        {
            $cod_status = _STATUS_PUBLICADO;
        }
        
        // verifica se já existe objeto com a URL amigável
        $url_amigavel = $this->verificaExistenciaUrlAmigavel($_page, $url_amigavel, $cod_objeto);
			
        $sql = "UPDATE objeto "
                . "SET cod_pai = ".$cod_pai.", "
                . "script_exibir = '".$script_exibir."', "
                . "cod_classe = ".$cod_classe.", "
                . "cod_usuario = ".$cod_usuario.", ";
        if ($cod_pele > 0) $sql .= "cod_pele = ".$cod_pele.", ";
        else $sql .= "cod_pele = null, ";
        $sql .= "cod_status = ".$cod_status.", "
                . "titulo = '".$titulo."', "
                . "descricao = '".$descricao."', "
                . "data_publicacao = '".ConverteData($data_publicacao, 27)."', "
                . "data_validade = '".ConverteData($data_validade, 27)."', "
                . "peso = ".$peso.", "
                . "url_amigavel = '".$url_amigavel."' "
                . "WHERE cod_objeto=".$cod_objeto;
        $_page->_db->ExecSQL($sql);

        $this->ApagarPropriedades($_page, $cod_objeto, false);
        $this->GravarPropriedades($_page, $cod_objeto, $cod_classe, $proplist);
        $this->GravarTags($_page, $cod_objeto, $tagslist);
			
        if ($log)
        {
            $_page->_log->IncluirLogObjeto($_page, $cod_objeto, _OPERACAO_OBJETO_EDITAR);
        }
        
        $this->cacheFlush($_page);

        return $cod_objeto;
    }

    /**
     * Busca lista de classes no banco de dados e popula propriedades de classes
     * @param object $_page - Referência de objeto da classe Pagina
     */
    function CarregaClasses(&$_page)
    {
        if (count($_SESSION['classesPrefixos']) == 0) $_page->_adminobjeto->CarregaClasses($_page);
        
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
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $prefixo - Prefixo da classe
     * @return int - Código da classe
     */
    function CodigoDaClasse(&$_page, $prefixo)
    {
        $this->CarregaClasses($_page);
        return $this->classesPrefixos[$prefixo];
    }

    /**
     * Busca lista de peles no banco de dados. Caso esteja logado com usuário
     * admin ve todas as peles, caso contrario somente peles publicas
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $rcvPele - Código da pele
     * @return array
     */
    function PegaListaDePeles(&$_page, $rcvPele=NULL)
    {
        $result=array();
        $sqladd = "";
        
        if ($rcvPele && $rcvPele!=NULL && $rcvPele!=0) $sqladd = " AND cod_pele=".$rcvPele;
        
        $sql = "SELECT cod_pele AS codigo, "
                . "nome AS texto, "
                . "prefixo, "
                . "publica "
                . "FROM pele WHERE 1=1";
        if ($_SESSION['usuario']['perfil'] != _PERFIL_ADMINISTRADOR) {
            $sql .= " AND publica='1'";
        }
        $sql .= $sqladd;
        $sql .= " ORDER BY texto";
        
        $res = $_page->_db->ExecSQL($sql);
        return $res->GetRows();
    }

    /**
     * Busca lista de usuários dependentes, caso usuario logado 
     * seja administrador traz todos usuários
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_usuario - código do usuário chefe
     * @return array
     */
    function PegaListadeDependentes(&$_page, $cod_usuario)
    {
        $result=array();
        
        if($_SESSION['usuario']['perfil'] == _PERFIL_ADMINISTRADOR)
        {
            $sql = "SELECT usuario.cod_usuario AS codigo, "
                    . "usuario.nome AS texto, "
                    . "usuario.secao AS secao "
                    . "FROM usuario "
                    . "WHERE valido = 1 OR cod_usuario = ".$cod_usuario." "
                    . "ORDER BY secao, texto";
        }
        else
        {
            if ($_SESSION['usuario']['cod_usuario'] == $cod_usuario)
            {
                $sql = "SELECT usuario.cod_usuario AS codigo, "
                        . "usuario.nome AS texto, "
                        . "usuario.secao AS secao "
                        . "FROM usuario "
                        . "WHERE valido = 1 "
                        . "AND (chefia = ".$cod_usuario." OR cod_usuario = ".$cod_usuario.") "
                        . "ORDER BY secao, texto";
            }
            else 
            {
                return false;
            }
        }
        
        $rs = $_page->_db->ExecSQL($sql);
        $result = $rs->GetRows();

        return $result;
    }

    /**
     * Chama metodo PegaListadeDependentes e com retorno deste método
     * e passa o array de resposta para o método CriaDropDown
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_usuario
     * @return string com lista de <options>
     */
    function DropDownListaDependentes(&$_page, $cod_usuario)
    {
        $lista=$this->PegaListadeDependentes($_page, $cod_usuario);
        return $this->CriaDropDown($lista, $cod_usuario, false, 30);
    }

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
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe
     * @return array
     */
    function PegaPropriedadesDaClasse(&$_page, $cod_classe)
    {
        $result = "";
        $sql = "select ".$_page->_db->nomes_tabelas["propriedade"].".cod_tipodado, 
                cod_propriedade,
                ".$_page->_db->nomes_tabelas["tipodado"].".nome as tipodado, 
                ".$_page->_db->nomes_tabelas["propriedade"].".campo_ref,
                ".$_page->_db->nomes_tabelas["propriedade"].".nome,
                ".$_page->_db->nomes_tabelas["tipodado"].".tabela,
                ".$_page->_db->nomes_tabelas["propriedade"].".cod_referencia_classe, 
                ".$_page->_db->nomes_tabelas["propriedade"].".posicao, 
                ".$_page->_db->nomes_tabelas["propriedade"].".descricao, 
                ".$_page->_db->nomes_tabelas["propriedade"].".rotulo, 
                ".$_page->_db->nomes_tabelas["propriedade"].".obrigatorio, 
                ".$_page->_db->nomes_tabelas["propriedade"].".seguranca, 
                ".$_page->_db->nomes_tabelas["propriedade"].".valorpadrao, 
                ".$_page->_db->nomes_tabelas["propriedade"].".rot1booleano, 
                ".$_page->_db->nomes_tabelas["propriedade"].".rot2booleano 
                from propriedade ".$_page->_db->nomes_tabelas["propriedade"]." 
                inner join tipodado ".$_page->_db->nomes_tabelas["tipodado"]." on ".$_page->_db->nomes_tabelas["propriedade"].".cod_tipodado = ".$_page->_db->nomes_tabelas["tipodado"].".cod_tipodado
                where ".$_page->_db->nomes_tabelas["propriedade"].".cod_classe=$cod_classe 
                order by ".$_page->_db->nomes_tabelas["propriedade"].".posicao";
        $rs = $_page->_db->ExecSQL($sql);
        
        return $rs->GetRows(); 
    }

    /**
     * Busca lista de objetos, com codigo do objeto e propriedade informada, 
     * de determinada classe no banco de dados e retorna array com informações.
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Código da classe
     * @param string $propriedade - Propriedade que deseja valor
     * @return array
     */
    function PegaListaDeObjetos(&$_page, $cod_classe, $propriedade)
    {
        $result=array();
        if (in_array($propriedade, $_page->_db->metadados))
        {
            $sql = "select cod_objeto as codigo,
            ".$propriedade." as texto 
            from objeto 
            where cod_classe=".$cod_classe." 
            and apagado <> 1 
            order by ".$propriedade;
        }
        else
        {
            $info = $_page->_adminobjeto->CriaSQLPropriedade($_page, cod_classe, $propriedade, ' asc');
            $sql = "select objeto.cod_objeto as codigo,
            ".$info['field']." as texto 
            from objeto ".$info['join']." 
            where ".$info['where']." 
            order by ".$info['sort'];
        }
        $res=$_page->_db->ExecSQL($sql);
        
        return $res->GetRows();
    }

    /**
     * Troca pele de filhos recursivamente de determinado objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto pai
     * @param int $cod_pele - Código da pele
     */
    function TrocaPeleFilhos(&$_page, $cod_objeto, $cod_pele)
    {
        $filhos = $_page->_adminobjeto->ListaCodFilhos($_page, $cod_objeto);

        if (is_array($filhos) && count($filhos) > 0)
        {
            $sql_pele_filhos = "UPDATE objeto ";
            if ($cod_pele==0) $sql_pele_filhos .= "SET cod_pele = null ";
            else $sql_pele_filhos .= "SET cod_pele = ".$cod_pele." ";
            $sql_pele_filhos .= "WHERE cod_objeto IN (".join(',',$filhos).")";
            $_page->_db->ExecSQL($sql_pele_filhos);
            
            foreach ($filhos as $filho)
            {
                $this->TrocaPeleFilhos($_page, $filho, $cod_pele);
            }
        }
    }

    

    

    /**
     * Verifica se já existe outro objeto utilizando a url amigável
     * se tiver adiciona número no final e verifica novamente
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $url - Url amigável para verificar
     * @param int $cod_objeto - Código do objeto
     * @param int $nivel - número a ser adicionado no final
     * @param int $tamanho - tamanho máximo da url amigável
     * @return string - url amigável a ser gravada
     */
    function verificaExistenciaUrlAmigavel(&$_page, $url, $cod_objeto=0, $nivel=0, $tamanho=0)
    {
        $url = limpaString($url);
        $url = strtolower($url);
        if (strlen($url)>249) $url = substr($url_amigavel, 0, 245);
        $sql = "select cod_objeto from objeto where url_amigavel='".$url."'";
        if ($cod_objeto>0) $sql .= " and not cod_objeto = ".$cod_objeto;
        $rs = $_page->_db->ExecSQL($sql);
        if ($tamanho==0) $tamanho = strlen($url);
        if ($rs->_numOfRows > 0)
        {
            $nivel++;
            $url = substr($url, 0, $tamanho).$nivel;
            $url = $this->verificaExistenciaUrlAmigavel($_page, $url, $cod_objeto, $nivel, $tamanho);
        }
        return $url;
    }

    /**
     * Apaga propriedades de determinado objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a remover as propriedades
     * @param bool $tudo - Indica se deve apagar blobs também
     */
    function ApagarPropriedades(&$_page, $cod_objeto, $tudo = true)
    {
        $sql = "SELECT tabela "
                . "FROM objeto "
                . "INNER JOIN propriedade ON objeto.cod_classe = propriedade.cod_classe "
                . "INNER JOIN tipodado ON propriedade.cod_tipodado = tipodado.cod_tipodado "
                . "WHERE cod_objeto = ".$cod_objeto;

        if (!$tudo)
        {
            $sql .= " AND tabela <> 'tbl_blob'";   
        }

        $res = $_page->_db->ExecSQL($sql);
        $row = $res->GetRows();

        for ($i=0; $i<sizeof($row); $i++)
        {
            if ($row[$i]['tabela']=='tbl_blob')
            {
                if (defined ("_BLOBDIR"))
                {
                    $sql = "select cod_blob, arquivo 
                    from tbl_blob 
                    where cod_objeto=$cod_objeto";
                    $res_blob = $_page->_db->ExecSQL($sql);
                    $row_blob = $res_blob->GetRows();

                    for ($j=0; $j<sizeof($row_blob); $j++)
                    {
                        $file_ext = Blob::PegaExtensaoArquivo($row_blob[$j]['arquivo']);
                        if (file_exists(_BLOBDIR."/".Blob::identificaPasta($row_blob[$j]['cod_blob'])."/".$row_blob[$j]['cod_blob'].'.'.$file_ext))
                        {
                            $checkDelete = unlink(_BLOBDIR."/".Blob::identificaPasta($row_blob[$j]['cod_blob'])."/".$row_blob[$j]['cod_blob'].'.'.$file_ext);
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
            
            $sql = "DELETE FROM ".$row[$i]['tabela']." "
                    . "WHERE cod_objeto = ".$cod_objeto;
            $_page->_db->ExecSQL($sql);
        }
    }

    /**
     * Grava propriedades do objeto
     * @global array $_FILES - Array com inputs file do PHP
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @param int $cod_classe - Codigo da classe
     * @param array $proplist - Lista de propriedades
     * @param array $array_files - Array com arquivos de upload
     */
    function GravarPropriedades(&$_page, $cod_objeto, $cod_classe, $proplist, $array_files='')
    {
        if (!is_array($array_files))
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
                        $info = $_page->_adminobjeto->PegaInfoSobrePropriedade($_page, $cod_classe, $ar_fld[1]);
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
                            $sql = "insert into ".$info['tabela']." (cod_propriedade, cod_objeto, valor) values (".
                            $info['cod_propriedade'].", ".$cod_objeto.", ".$info['delimitador'].$_page->_db->Slashes($valor).$info['delimitador'].")";
                            $_page->_db->ExecSQL($sql);
                        }
                    }
                }
                else
                {
                    $ar_fld = explode("^", $ar_fld[1]);

                    $info = $_page->_adminobjeto->PegaInfoSobrePropriedade($_page, $cod_classe, $ar_fld[0]);

                    if ($info['tabela'] == "tbl_blob")
                    {
                        $sql = "Select * from tbl_blob where cod_propriedade=".$info["cod_propriedade"]." and cod_objeto=".$cod_objeto;
                        $rs = $_page->_db->ExecSQL($sql);

                        while ($row = $rs->FetchRow())
                        {
                            $_page->_blob->apagaBlob($_page, $row['cod_blob'], $row['arquivo']);
                        }
                    }

                    $sql = "delete from ".$info['tabela']." where cod_propriedade=".$info['cod_propriedade'].
                                    " and cod_objeto=$cod_objeto";
                    $_page->_db->ExecSQL($sql);  
                }
            }
//            xd("parou");
        }

        // Gravando propriedades blob
        if (is_array($array_files))
        {
            foreach ($array_files as $key => $valor)
            {
                if (isset($valor['size']) && $valor['size'] > 0)
                {
                    $ar_fld = preg_split("[___]", $key);
                    $info = $_page->_adminobjeto->PegaInfoSobrePropriedade($_page, $cod_classe, $ar_fld[1]);
                    
                    // Apaga registro, caso já exista
                    $sql = "delete from ".$info['tabela']." where cod_propriedade=".$info['cod_propriedade'].
                                    " and cod_objeto=$cod_objeto";
                    $_page->_db->ExecSQL($sql);
                    
                    if ($source=='post') $data = fread(fopen($valor['tmp_name'], "rb"), filesize($valor['tmp_name']));
                    else $data = stripslashes($valor['data']);

                    // caso seja gravação do blob no banco
                    if (!defined ("_BLOBDIR"))
                    {
                        $campo = gzcompress($data);
                        $sql = "insert into ".$info['tabela']."(cod_propriedade, cod_objeto, valor, arquivo, tamanho) values (".
                        $info['cod_propriedade'].", ".$cod_objeto.", ".$info['delimitador'].$_page->_db->BlobSlashes($data).$info['delimitador'].", '".$valor['name']."', ".filesize($valor['tmp_name']).")";
                        $_page->_db->ExecSQL($sql);
                    }
                    // gravação do arquivo em disco
                    else
                    {
                        $campos = array();
                        $campos['cod_propriedade'] = (int)$info['cod_propriedade'];
                        $campos['cod_objeto'] = (int)$cod_objeto;
                        $campos['arquivo'] = strtolower($valor['name']);
                        $campos['tamanho'] = filesize($valor['tmp_name']);
                        $cod_blob = $_page->_db->Insert($info['tabela'], $campos);
                        
                        // Chama o método de gravação de blob no disco
                        $_page->_blob->gravarBlob($_page, $valor, $cod_blob);
                            
                    }
//                    }
                }
            }
        }
    }

    /**
     * Cria objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param array $dados - Dados do objeto a ser criado
     * @param bool $log - Indica se deve gerar log
     * @param array $array_files - Lista de arquivos
     * @return int - Codigo do objeto criado
     */
    function CriarObjeto(&$_page, $dados, $log = true, $array_files = '')
    {
        $fieldlist = array();
        $valuelist = array();
        $tagslist = array();
        $proplist = array();
        
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
        $campos['script_exibir'] = htmlspecialchars($dados['script_exibir'], ENT_QUOTES, "UTF-8");
        $campos['cod_pai'] = (int)htmlspecialchars($dados['cod_pai'], ENT_QUOTES, "UTF-8");
        $campos['cod_classe'] = (int)htmlspecialchars($dados['cod_classe'], ENT_QUOTES, "UTF-8");
        $campos['cod_usuario'] = (int)htmlspecialchars($dados['cod_usuario'], ENT_QUOTES, "UTF-8");
        $campos['cod_pele'] = (int)htmlspecialchars($dados['cod_pele'], ENT_QUOTES, "UTF-8");
        $campos['cod_status'] = (int)htmlspecialchars($dados['cod_status'], ENT_QUOTES, "UTF-8");
        $campos['titulo'] = htmlspecialchars($dados['titulo'], ENT_QUOTES, "UTF-8");
        $campos['descricao'] = htmlspecialchars($dados['descricao'], ENT_QUOTES, "UTF-8");
        $campos['data_publicacao'] = ConverteData(htmlspecialchars($dados['data_publicacao'], ENT_QUOTES, "UTF-8"), 27);
        $campos['data_validade'] = ConverteData(htmlspecialchars($dados['data_validade'], ENT_QUOTES, "UTF-8"), 27);
        $campos['peso'] = (int)htmlspecialchars($dados['peso'], ENT_QUOTES, "UTF-8");
        $campos['script_exibir'] = htmlspecialchars($dados['script_exibir'], ENT_QUOTES, "UTF-8");
        if ($dados['url_amigavel']=="") $dados['url_amigavel'] = limpaString($campos['titulo']);
        $campos['url_amigavel'] = $this->verificaExistenciaUrlAmigavel($_page, $dados['url_amigavel']);
        
        // grava objeto e recebe código de volta
        $sql = "INSERT INTO objeto "
                . "("
                . "cod_pai, "
                . "cod_classe, "
                . "cod_usuario, "
                . ($campos['cod_pele']==0?"":"cod_pele, ")
                . "cod_status, "
                . "titulo, "
                . "descricao, "
                . "data_publicacao, "
                . "data_validade, "
                . "script_exibir, "
                . "apagado,"
                . "objetosistema,"
                . "peso,"
                . "url_amigavel"
                . ") VALUES ("
                . $campos['cod_pai'].", "
                . $campos['cod_classe'].", "
                . $campos['cod_usuario'].", "
                . ($campos['cod_pele']==0?"":$campos['cod_pele'].", ").""
                . $campos['cod_status'].", "
                . "'".$campos['titulo']."', "
                . "'".$campos['descricao']."', "
                . $campos['data_publicacao'].", "
                . $campos['data_validade'].", "
                . "'".$campos['script_exibir']."', "
                . "0, "
                . "0, "
                . $campos['peso'].", "
                . "'".$campos['url_amigavel']."'"
                . ")";
        $_page->_db->ExecSQL($sql);
        $cod_objeto = $_page->_db->InsertID("objeto");
        
        // grava as propriedades do objeto
        $this->GravarPropriedades($_page, $cod_objeto, $dados['cod_classe'], $proplist, $array_files);
        // grava as relações de parentesco do objeto
        $this->CriaParentesco($_page, $cod_objeto, $dados['cod_pai']);
        // grava as tags
        $this->GravarTags($_page, $cod_objeto, $tagslist);
        // grava o log
        if ($log) $_page->_log->IncluirLogObjeto($_page, $cod_objeto, _OPERACAO_OBJETO_CRIAR);
        // esvazia o cache
        $this->cacheFlush($_page);

        return $cod_objeto;
    }
    
    function cacheFlush(&$_page)
    {
        if (ATIVA_CACHE_BANCO===true) $_page->_db->con->CacheFlush();
    }

    /**
     * Grava tags do objeto no banco de dados
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @param array $tagslist - Lista de tags
     */
    function GravarTags(&$_page, $cod_objeto, $tagslist)
    {
        if (is_array($tagslist) && count($tagslist)>=1)
        {
            $this->ApagarTags($_page, $cod_objeto);

            foreach ($tagslist as $tag)
            {
                $tag = trim($tag);
                $sql = "select cod_tag from tag where nome_tag='".$tag."'";
                $rs = $_page->_db->ExecSQL($sql);
                if ($rs->_numOfRows == 0)
                {
                    $cod_tag = $_page->_db->Insert("tag", array("nome_tag"=>$tag));
                }
                else
                {
                    $row = $rs->FetchRow();
                    $cod_tag = $row["cod_tag"];
                }

                $sql = "insert into tagxobjeto (cod_tag, cod_objeto) values (".$cod_tag.",".$cod_objeto.")";
                $rs = $_page->_db->ExecSQL($sql);
            }
        }
    }

    /**
     * Remove tags do objeto e do banco caso não tenha nenhum 
     * outro objeto utilizando
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     */
    function ApagarTags(&$_page, $cod_objeto)
    {
        $sql = "delete from tagxobjeto where cod_objeto=".$cod_objeto;
        $rs = $_page->_db->ExecSQL($sql);

        $sql = "delete from tag where cod_tag not in (select cod_tag from tagxobjeto)";
        $rs = $_page->_db->ExecSQL($sql);
    }

    /**
     * Apaga lista de relação de parentesco de objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     */
    function ApagarParentesco(&$_page, $cod_objeto)
    {
        $_page->_db->ExecSQL("delete from parentesco where cod_objeto =".$cod_objeto);
    }

    /**
     * Cria relação de parentesco entre objetos
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @param int $cod_pai - Codigo do objeto pai
     */
    function CriaParentesco(&$_page, $cod_objeto, $cod_pai)
    {
        // duplica parentesco do objeto pai, incrementando o nível
        $sql = "insert into parentesco(cod_objeto, cod_pai, ordem) "
                . "select ".$cod_objeto.", cod_pai, ordem+1 from parentesco "
                . "where cod_objeto=".$cod_pai;
        $_page->_db->ExecSQL($sql);
        
        // cria parentesco entre objeto e o pai
        $sql = "insert into parentesco(cod_objeto, cod_pai, ordem) "
                . "values (".$cod_objeto.", ".$cod_pai.", 1)";
        $_page->_db->ExecSQL($sql);
    }

    /**
     * Verifica se classe é indexavel
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe
     * @return int - 0 ou 1
     */
    function ClasseIndexavel(&$_page, $cod_classe)
    {
        $this->CarregaClasses($_page);
        return (in_array($cod_classe, $this->classesIndexaveis));
    }

    /**
     * Apaga objeto, fisicamente ou logicamente
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     * @param bool $definitivo - indica se deve apagar realmente, ou mandar para lixeira
     */
    function ApagarObjeto(&$_page, $cod_objeto, $definitivo=false)
    {
        if (!$definitivo)
        {
            $sql = "select parentesco.cod_objeto, cod_status 
            from parentesco inner join objeto on parentesco.cod_objeto=objeto.cod_objeto 
            where parentesco.cod_pai=$cod_objeto 
            or parentesco.cod_objeto=$cod_objeto";
            $res = $_page->_db->ExecSQL($sql);

            while ($row = $res->FetchRow())
            {
                $sql = "update objeto set apagado=1, data_exclusao='".date("Ymd")."' ";
                if ($row['cod_status'] == _STATUS_SUBMETIDO)
                {
                    $_page->_db->ExecSQL("delete from pendencia where cod_objeto=".$row["cod_objeto"]);
                    $sql .=", cod_status="._STATUS_PRIVADO;
                }

                $sql .= " where cod_objeto=".$row["cod_objeto"];
                $_page->_db->ExecSQL($sql);
            }
        }
        else
        {

            $sql = "select cod_objeto from parentesco where cod_pai=$cod_objeto";
            $res = $_page->_db->ExecSQL($sql);
            $row = $res->GetRows();

            for ($m=0; $m<sizeof($row); $m++)
            {
                $this->ApagarPropriedades($_page, $row[$m]['cod_objeto'],true);

                $sql = "delete from objeto where cod_objeto=".$row[$m]['cod_objeto'];
                $_page->_db->ExecSQL($sql);
            }

            $this->RemoveObjetoDaPilha($_page, $cod_objeto, 0);
            $this->ApagarParentesco($_page, $cod_objeto);
            $this->ApagarLogObjeto($_page, $cod_objeto);
            $this->ApagarLogWorkflow($_page, $cod_objeto);
            $this->ApagarTags($_page, $cod_objeto);
            $this->ApagarPropriedades($_page, $cod_objeto, true);

            $sql = "delete from objeto where cod_objeto=$cod_objeto";
            $_page->_db->ExecSQL($sql);
        }

        $_page->_log->IncluirLogObjeto($_page, $cod_objeto,_OPERACAO_OBJETO_REMOVER);
    }
    
    /**
     * Verifica se objeto é indexavel
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a verificar
     * @return int - 0 ou 1
     */
    function ObjetoIndexado(&$_page, $cod_objeto)
    {
        $sql = "select indexar from classe left join objeto on objeto.cod_classe=classe.cod_classe
                        where cod_objeto=$cod_objeto";
        $rs = $_page->_db->ExecSQL($sql);
        $row = $rs->fields;
        return $row['indexar'];
    }

    /**
     * Verifica se usuário é dono do objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_usuario - Codigo do usuario
     * @param int $cod_objeto - Codigo do objeto
     * @return boolean
     */
    function UsuarioEDono(&$_page, $cod_usuario,$cod_objeto)
    {
        $sql = "select cod_objeto from objeto where cod_objeto=$cod_objeto and cod_usuario=$cod_usuario";
        $rs = $_page->_db->ExecSQL($sql);
        if ($rs->_numOfRows > 0) return true;
        else return false;
    }

    /**
     * Busca informações sobre usuário que é dono de determinado objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Dados do usuário dono do objeto
     */
    function QuemEDono(&$_page, $cod_objeto)
    {
        $sql = "select usuario.cod_usuario as cod_usuario, usuario.nome as nome, "
                . "usuario.email as email, usuario.login as login, usuario.chefia as chefia, "
                . "usuario.valido as valido from objeto inner join usuario "
                . "on usuario.cod_usuario = objeto.cod_usuario where cod_objeto=".$cod_objeto;
        $rs = $_page->_db->ExecSQL($sql);
        return $rs->GetRows(); 
    }

    /**
     * Rejeita publicação do objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $mensagem - Mensagem de rejeição
     * @param int $cod_objeto - Codigo do objeto
     */
    function RejeitarObjeto(&$_page, $mensagem, $cod_objeto)
    {
        if (($_SESSION['usuario']['perfil']==_PERFIL_ADMINISTRADOR) || ($_SESSION['usuario']['perfil']==_PERFIL_EDITOR)
                || (($_SESSION['usuario']['perfil']==_PERFIL_AUTOR) && $this->UsuarioEdono($_page, $_SESSION['usuario']['cod_usuario'], $cod_objeto)))
        {
            $this->TrocaStatusObjeto($_page, $mensagem, $cod_objeto, _STATUS_REJEITADO);
            $_page->_db->ExecSQL("delete from pendencia where cod_objeto=".$cod_objeto);
        }
    }

    /**
     * Publica objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $mensagem - mensagem de publicação
     * @param int $cod_objeto - Codigo do objeto
     */
    function PublicarObjeto(&$_page, $mensagem, $cod_objeto)
    {			
        if ($_SESSION['usuario']['perfil'] <= _PERFIL_EDITOR)
        {
            $this->TrocaStatusObjeto($_page, $mensagem, $cod_objeto, _STATUS_PUBLICADO);
            $_page->_db->ExecSQL("delete from pendencia where cod_objeto=".$cod_objeto);

            if (defined("_avisoPublicacao") && _avisoPublicacao==true)
            {
                $objetoPublicado = new Objeto($_page, $cod_objeto);
                $array_objeto = null;
                $array_objeto[] = array($objetoPublicado->metadados["cod_objeto"], $objetoPublicado->metadados["titulo"]);
                $caminhoObjeto = $_page->_adminobjeto->PegaIDPai($_page, $cod_objeto, 100, array(0));
                foreach ($caminhoObjeto as $codigo=>$titulo) 
                {
                    $array_objeto[] = array($codigo, $titulo);
                }

                $mensagemEmail = "<html><head><title>Objeto Publicado</title></head>
                <body>
                Objeto publicado no site: <b>"._PORTAL_NAME."</b><br>
                Data: ".date("d/m/Y H:i:s")."<br>
                Objeto: <a href=\""._URL."/index.php/content/view/".$array_objeto[0][0].".html\" target=\"_blank\">".$array_objeto[0][1]."</a><br><br>
                Caminho do objeto: <br>";

                for ($i=1; $i<sizeof($array_objeto); $i++) {
                    $mensagemEmail .= $i." - <a href=\""._URL."/index.php/content/view/".$array_objeto[$i][0].".html\" target=\"_blank\">".$array_objeto[$i][1]."</a><br>";
                }

                $mensagemEmail .= "<br><small>Mensagem gerada automaticamente. Nao responda.</small>
                </body></html>";

                $destinatario = _emailAvisoPublicacao;
                $remetente =  _remetenteAvisoPublicacao;
                $assunto = "Objeto publicado no site: "._PORTAL_NAME;
                $wassent = EnviarEmail($remetente, $destinatario, $assunto, $mensagemEmail); 
            }
        }
    }

    /**
     * Despublica objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $mensagem - Mensagem de despublicação
     * @param int $cod_objeto - Codigo do objeto
     */
    function DesPublicarObjeto(&$_page, $mensagem, $cod_objeto)
    {			
        if (($_SESSION['usuario']['perfil']==_PERFIL_ADMINISTRADOR) || ($_SESSION['usuario']['perfil']==_PERFIL_EDITOR))
        {
            $this->TrocaStatusObjeto($_page, $mensagem, $cod_objeto, _STATUS_PRIVADO);
        }
    }

    /**
     * Envia objeto para publicação, solicita publicação do objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $mensagem - mensagem de solicitação de publicação
     * @param int $cod_objeto - Codigo do objeto
     */
    function SubmeterObjeto(&$_page, $mensagem, $cod_objeto)
    {
        $dadosObjeto = $_page->_adminobjeto->PegaDadosObjetoPeloID($_page, $cod_objeto);

        if ((($_SESSION['usuario']['perfil']==_PERFIL_AUTOR) || ($this->UsuarioEdono($_page, $_SESSION['usuario']['cod_usuario'],$cod_objeto))) && ($dadosObjeto['cod_status'] == _STATUS_PRIVADO))
        {
            $this->TrocaStatusObjeto($_page, $mensagem, $cod_objeto, _STATUS_SUBMETIDO);

            $sql = "select ".$_SESSION["usuario"]["chefia"]." as cod_usuario,".$cod_objeto." as cod_objeto from usuarioxobjetoxperfil inner join parentesco on (usuarioxobjetoxperfil.cod_objeto=parentesco.cod_pai or usuarioxobjetoxperfil.cod_objeto=parentesco.cod_objeto) where parentesco.cod_objeto=".$cod_objeto." group by cod_usuario, cod_usuario";
            $rs = $_page->_db->ExecSQL($sql, 1, 1);
            $campos = $rs->fields;

            $sql = "select * from pendencia where cod_usuario = ".$campos['cod_usuario']." and cod_objeto = ".$campos['cod_objeto'];
            $rs = $_page->_db->ExecSQL($sql);

            if (!$rs->GetRows())
            {
                $sql = "insert into pendencia(cod_usuario, cod_objeto) values (".$campos['cod_usuario'].", ".$campos['cod_objeto'].")";
                $_page->_db->ExecSQL($sql);
            }

            $EnviaEmailSolicitacao = $_page->_adminobjeto->EnviaEmailSolicitacao($_page, $_SESSION['usuario']['chefia'], $cod_objeto, $mensagem);
        }
    }

    /**
     * Remove solicitação de publicação
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $mensagem - mensagem de remoção da pendencia
     * @param int $cod_objeto - Codigo do objeto
     */
    function RemovePendencia(&$_page, $mensagem, $cod_objeto)
    {
        $this->TrocaStatusObjeto($_page, $mensagem, $cod_objeto, _STATUS_PRIVADO);
        $sql = "delete from pendencia where cod_objeto = ".$cod_objeto;
        $_page->_db->ExecSQL($sql);
    }

    /**
     * Troca status do objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $mensagem - Mensagem da troca de status
     * @param int $cod_objeto - Codigo do objeto
     * @param int $cod_status - Codigo do novo status
     */
    function TrocaStatusObjeto(&$_page, $mensagem, $cod_objeto, $cod_status)
    {
        if ($cod_objeto != _ROOT)
        {
            $_page->_db->ExecSQL("update objeto set cod_status=".$cod_status." where cod_objeto=$cod_objeto");
            $_page->_log->RegistraLogWorkFlow($_page, $mensagem, $cod_objeto, $cod_status);
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
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - codigo do objeto a ser copiado
     * @param int $cod_pai - Codigo do objeto pai onde sera criado novo objeto
     */
    function CopiarObjeto(&$_page, $cod_objeto, $cod_pai)
    {
        $this->DuplicarObjeto($_page, $cod_objeto, $cod_pai);
        $this->RemoveObjetoDaPilha($_page, $cod_objeto);
    }

    /**
     * Move determinado objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a ser movido
     * @param int $cod_pai - Codigo do objeto pai onde ficara objeto movido
     */
    function MoverObjeto(&$_page, $cod_objeto, $cod_pai)
    {
        if ($cod_objeto==-1)
        {
                $cod_objeto=$this->PegaPrimeiroDaPilha($_page);
        }
        $sql = "update objeto set cod_pai=$cod_pai where cod_objeto=$cod_objeto";
        $_page->_db->ExecSQL($sql);

        $this->ApagarParentesco($_page, $cod_objeto);
        $this->CriaParentesco($_page, $cod_objeto, $cod_pai);

        $sql = "select objeto.cod_pai,objeto.cod_objeto from objeto inner join parentesco on objeto.cod_objeto=parentesco.cod_objeto 
                        where parentesco.cod_pai=".$cod_objeto." group by objeto.cod_objeto, objeto.cod_pai";
        $res = $_page->_db->ExecSQL($sql);
        $row = $res->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
                $this->ApagarParentesco($_page, $row[$i]['cod_objeto']);
                $this->CriaParentesco($_page, $row[$i]['cod_objeto'], $row[$i]['cod_pai']);
        }

        $this->RemoveObjetoDaPilha($_page, $cod_objeto);
    }

    /**
     * Cola objeto da pilha como link
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - codigo do objeto a ser colado como link
     * @param int $cod_pai - codigo do objeto que será pai do link
     */
    function ColarComoLink(&$_page, $cod_objeto, $cod_pai)
    {
        if ($cod_objeto==-1)
        {
            $cod_objeto=$this->PegaPrimeiroDaPilha($_page);
        }

        $orig_obj = $_page->_adminobjeto->CriarObjeto($_page, $cod_objeto);
        $dados = $orig_obj->metadados;

        $status = $this->PegaStatusNovoObjeto();
        
        $cod_classe_interlink = $this->CodigoDaClasse($_page, "interlink");

        $campos=array();
        $campos['cod_pai'] = $cod_pai;
        $campos['cod_classe'] = $cod_classe_interlink;
        $campos['cod_usuario'] = $dados['cod_usuario'];
        $campos['cod_status'] = $dados['cod_status'];
        $campos['titulo'] = $_page->_db->Slashes($dados['titulo']);
        $campos['descricao'] = $_page->_db->Slashes($dados['descricao']);
        $campos['data_publicacao'] = ConverteData($dados['data_publicacao'],27);
        $campos['data_validade'] = ConverteData($dados['data_validade'],27);

        $novo_cod_objeto = $_page->_db->Insert('objeto',$campos);		
//        xd($novo_cod_objeto);

        $this->GravarPropriedades($_page, $novo_cod_objeto, $cod_classe_interlink, array('property___link'=>$cod_objeto));
        $this->RemoveObjetoDaPilha($_page, $cod_objeto);
        $this->CriaParentesco($_page, $novo_cod_objeto, $cod_pai);
    }

    /**
     * Pega primeiro objeto da pilha
     * @param object $_page - Referência de objeto da classe Pagina
     * @return int - codigo do primeiro objeto da pilha
     */
    function PegaPrimeiroDaPilha(&$_page)
    {
        $sql = "select pilha.cod_objeto from pilha
                        where pilha.cod_usuario=".$_SESSION['usuario']['cod_usuario'];
        $rs = $_page->_db->ExecSQL($sql);
        $row = $rs->fields;

        return $row['cod_objeto'];
    }

    /**
     * Duplica objeto e seus filhos
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a duplicar
     * @param int $cod_pai - Codigo do objeto pai, onde ficara novo objeto
     * @return int - Codigo do novo objeto
     */
    function DuplicarObjeto(&$_page, $cod_objeto, $cod_pai=-1)
    {
        if ($cod_objeto==-1)
        {
            $cod_objeto=$this->PegaPrimeiroDaPilha($_page);
        }

        $orig_obj = $_page->_adminobjeto->CriarObjeto($_page, $cod_objeto);
        $dados = $orig_obj->metadados;
        
        if ($cod_pai==-1) $cod_pai=$dados['cod_pai'];

        $campos = array();
        $campos['script_exibir'] = $dados['script_exibir'];
        $campos['cod_pai'] = $cod_pai;
        $campos['cod_classe'] = $dados['cod_classe'];
        $campos['cod_usuario'] = $dados['cod_usuario'];
        if (!is_null($dados['cod_pele'])) $campos['cod_pele'] = $dados['cod_pele'];
        $campos['cod_status'] = $dados['cod_status'];
        $campos['titulo'] = $_page->_db->Slashes($dados['titulo']);
        $campos['descricao'] = $_page->_db->Slashes($dados['descricao']);
        $campos['data_publicacao'] = ConverteData($dados['data_publicacao'],27);
        $campos['data_validade'] = ConverteData($dados['data_validade'],27);
        $campos['url_amigavel'] = $this->verificaExistenciaUrlAmigavel($_page, $dados['url_amigavel']);
        $campos['peso'] = $dados['peso'];

        $cod_objeto = $_page->_db->Insert('objeto', $campos);	
        $this->DuplicarPropriedades($_page, $cod_objeto, $orig_obj);
        $this->CriaParentesco($_page, $cod_objeto, $cod_pai);

        if ($orig_obj->PegaListaDeFilhos($_page))
        {
            while ($childobj = $orig_obj->PegaProximoFilho())
            {
                $this->DuplicarObjeto($_page, $childobj->Valor($_page, "cod_objeto"), $cod_objeto);
            }
        }

        $_page->_log->IncluirLogObjeto($_page, $cod_objeto, _OPERACAO_OBJETO_CRIAR);
        
        $this->cacheFlush($_page);
        
        return $cod_objeto;
    }

    /**
     * Duplica propriedades de determinado objeto em outro objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $destino - codigo do objeto que recebera as propriedades
     * @param int $origem - codigo do objeto que tera proprieades duplicadas
     */
    function DuplicarPropriedades(&$_page, $destino, $origem)
    {
        $propriedades = $origem->PegaListaDePropriedades($_page);
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
        $this->GravarPropriedades($_page, $destino, $origem->Valor($_page, "cod_classe"), $lista);
    }

    /**
     * Busca lista de classes que podem ser criadas abaixo de determinada classe
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe a ser verificada
     * @return array - Lista de classes que podem ser criadas
     */
    function ListaDeClassesPermitidas(&$_page, $cod_classe)
    {
        $out=array();
        $sql = "select cod_classe_filho, classe.nome, classe.descricao,classe.prefixo from classexfilhos
                        inner join classe on classe.cod_classe=classexfilhos.cod_classe_filho
                        where classexfilhos.cod_classe=$cod_classe order by classe.nome";
        $res = $_page->_db->ExecSQL($sql);
        return $res->GetRows();
    }
    
    /**
     * Verifica quais classes podem ser criadas abaixo de determinado objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Lista de classes que podem ser criadas
     */
    function ListaDeClassesPermitidasNoObjeto(&$_page, $cod_objeto)
    {
        $out=array();
        $sql = "select classe.cod_classe,classe.nome, classe.descricao,classe.prefixo from classexobjeto
                        inner join classe on classe.cod_classe=classexobjeto.cod_classe
                        where classexobjeto.cod_objeto=$cod_objeto order by classe.nome";
        $rs = $_page->_db->ExecSQL($sql);
        return $rs->GetRows();
    }

    /**
     * Envia objeto para pilha do usuario
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto para ir para pilha
     */
    function CopiarObjetoParaPilha(&$_page, $cod_objeto)
    {
        $sql = "insert into pilha (cod_objeto,cod_usuario) values($cod_objeto,".$_SESSION['usuario']['cod_usuario'].")";
        $_page->_db->ExecSQL($sql);
    }

    /**
     * Remove objeto da pilha do usuario
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto que deve sair da pilha
     */
    function RemoveObjetoDaPilha(&$_page, $cod_objeto, $user=1)
    {
        $sql = "delete from pilha where ".($user==1?"cod_usuario=".$_SESSION['usuario']['cod_usuario']." and ":"")."cod_objeto=$cod_objeto";
        $_page->_db->ExecSQL($sql);
    }

    /**
     * Limpa pilha do usuário
     * @param object $_page - Referência de objeto da classe Pagina
     */
    function LimparPilha(&$_page)
    {
        $sql = "delete from pilha where cod_usuario=".$_SESSION['usuario']['cod_usuario'];
        $_page->_db->ExecSQL($sql);
    }

    /**
     * Pega pilha do usuario logado
     * @param object $_page - Referência de objeto da classe Pagina
     * @return array - lista de objetos na pilha
     */
    function PegaPilha(&$_page)
    {
        $result=array();
        $this->ContadorPilha=0;
        $sql = "select pilha.cod_objeto as codigo, objeto.titulo as texto from pilha
                        left join objeto on objeto.cod_objeto=pilha.cod_objeto
                        where pilha.cod_usuario=".$_SESSION['usuario']['cod_usuario'];
        $rs = $_page->_db->ExecSQL($sql);
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
     * @param object $_page - Referência de objeto da classe Pagina
     * @return int - Numero de objetos na pilha
     */
    function TemPilha(&$_page)
    {
        if (!$this->ContadorPilha)
        {
            $sql = "select count(*) as contador from pilha where cod_usuario=".$_SESSION['usuario']['cod_usuario'];
            $rs = $_page->_db->ExecSQL($sql);
            $this->ContadorPilha = $rs->fields["contador"];
        }
        return $this->ContadorPilha;
    }

    /**
     * Busca objetos da pilha e envia resultado para metodo que monta dropdown
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $selecionado - codigo do parametro que deve vir selecionado no <select>
     * @param bool $branco - indica se deve ter um <option> com value em branco
     * @return string - Lista de <option> para o <select>
     */
    function DropDownPilha(&$_page, $selecionado='', $branco=false)
    {
        $lista = $this->PegaPilha($_page);
        return $this->CriaDropDown($lista, $selecionado, $branco);
    }
    
    /**
     * Monta relacionamento entre classes e objetos, identificando os objetos onde a classe pode ser criada
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe que será relacionada
     * @param array $relacao - Lista de códigos de objetos
     * @param array $relacaourl - Lista de urls amigáveis de objetos
     */
    function MontaRelacionamentoClassesObjetos(&$_page, $cod_classe, $relacao, $relacaourl)
    {
        // Atualiza lista de objetos onde pode ser criada
        $sql = "DELETE FROM classexobjeto WHERE cod_classe=" . $cod_classe;
        $_page->_db->ExecSQL($sql);
        
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
                    $sql = "SELECT cod_objeto FROM objeto WHERE cod_objeto=".$cod;
                    $rs = $_page->_db->ExecSQL($sql);
                    if ($rs->_numOfRows > 0)
                    {
                        $objs[] = $cod;
                    }
                }
                else
                {
                    if ($url != "")
                    {
                        $sql = "SELECT cod_objeto FROM objeto WHERE url_amigavel='".$url."'";
                        $rs = $_page->_db->ExecSQL($sql);
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
                $sql = "INSERT INTO classexobjeto "
                        . "(cod_classe, "
                        . "cod_objeto) "
                        . "VALUES (".$cod_classe.", "
                        . "".$obj.")";
                $_page->_db->ExecSQL($sql);
            }
        }
    }
    
    /**
     * Apaga e refaz relacionamento entre classes
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe principal que esta tendo o relacionamento remontado
     * @param array $relacao - Codigos das classes que compoem o relacionamento
     * @param int $tipo - Informa tipo de relação: 1=contem, 2=está contido
     */
    function MontaRelacionamentoClasses(&$_page, $cod_classe, $relacao, $tipo)
    {
        // Apagando relação existente
        $sql = "DELETE FROM classexfilhos WHERE ";
        if ($tipo == "1") $sql .= "cod_classe = " . $cod_classe;
        else $sql .= "cod_classe_filho = " . $cod_classe;
        $_page->_db->ExecSQL($sql);
        
        if (is_array($relacao) && count($relacao) > 0)
        {
            foreach ($relacao as $rel)
            {
                $cod = (int)htmlspecialchars($rel, ENT_QUOTES, "UTF-8");

                $sql = "INSERT INTO classexfilhos (cod_classe, cod_classe_filho) ";
                if ($tipo=="1") $sql .= "VALUES (" . $cod_classe . ", " . $cod . ")";
                else $sql .= "VALUES (" . $cod . ", " . $cod_classe . ")";
                $_page->_db->ExecSQL($sql);
            }
        }
    }

    /**
     * Busca informações de determinada classe
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe
     * @return array - lista com informações da classe
     */
    function PegaInfoDaClasse(&$_page, $cod_classe)
    {
        $sql = "select * from classe where cod_classe=$cod_classe order by classe.nome";
//        xd($sql);
        $rs = $_page->_db->ExecSQL($sql);
        $result['classe'] = $rs->fields;

        $sql = "select cod_classe,nome from classe order by nome";
        $rs = $_page->_db->ExecSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result['todas'][$row[$i]['cod_classe']]=$row[$i];
        }

        $sql = "select cod_classe_filho from classexfilhos where cod_classe=$cod_classe";
        $rs = $_page->_db->ExecSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result['todas'][$row[$i]['cod_classe_filho']]['permitido']=true;
        }

        $sql = "select cod_classe from classexfilhos where cod_classe_filho=$cod_classe";
        $rs = $_page->_db->ExecSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result['todas'][$row[$i]['cod_classe']]['criadoem']=true;
        }

        $prop = $this->PegaPropriedadesDaClasse($_page, $cod_classe);
        $count=1;
        $result['prop']=array();
        if (is_array($prop))
        {
            foreach($prop as $value)
            {
                $result['prop'][$value['nome']]=$value;
            }

        }

        $sql = "select count(cod_objeto) as cnt from objeto where cod_classe=$cod_classe";
        $rs = $_page->_db->ExecSQL($sql);
        $result['obj_conta'] = $rs->fields["cnt"];

        $sql = "select objeto.cod_objeto, objeto.titulo, objeto.url_amigavel from classexobjeto "
                . "inner join objeto on classexobjeto.cod_objeto=objeto.cod_objeto "
                . "where classexobjeto.cod_classe=$cod_classe";
        $res = $_page->_db->ExecSQL($sql);
        $row = $res->GetRows();
        for ($k=0; $k<sizeof($row); $k++)
        {
            $result['objetos'][]=$row[$k];
        }
        return $result;
    }

    /**
     * Busca tipos de dado existentes e envia valores para metodo que monta dropdown
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $selecionado - Valor que devera vir selecionado no dropdown
     * @param bool $branco - indica se devera ter <option> com value em branco
     * @return string - Lista de <option> para o <select>
     */
    function DropDownTipoDado(&$_page, $selecionado, $branco=false)
    {
        $lista=$this->PegaListaDeTipoDado($_page);
        return $this->CriaDropDown($lista, $selecionado, $branco);
    }

    /**
     * Pega lista de tipos de dados no banco de dados
     * @param object $_page - Referência de objeto da classe Pagina
     * @return array - lista com tipos de dados
     */
    function PegaListaDeTipoDado(&$_page)
    {
        $sql = "select cod_tipodado as codigo, nome as texto from tipodado order by nome";
        $rs = $_page->_db->ExecSQL($sql);
        return $rs->GetRows();
    }

    /**
     * Busca classes e envia valores para metodo que monta dropdown
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $selecionado - Valor que devera vir selecionado no dropdown
     * @param bool $branco - indica se devera ter <option> com value em branco
     * @return string - Lista de <option> para o <select>
     */
    function DropDownClasses(&$_page, $selecionado, $branco=false)
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
                            && substr($arquivo, 0, 5) == "view_") $default[] = $arquivo;
                }
            }
            closedir($dir);
            sort($default);
        }
        $retorno["default"] = $default;
        
        // buscando views dentro das peles
        $peles = $this->PegaListaDePeles($_page);
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
        $this->CarregaClasses($_page);
        
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
        $rs = $_page->_db->ExecSQL($sql);
        $row = $rs->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result[]=$row[$i]['prefixo'];
        }
        return $result;
    }
    
    /**
     * Atualiza informações da classe
     * @param object $_page - Referência de objeto da classe Pagina
     * @param integer $cod_classe - Código da classe
     * @param array $dados - Dados da classe
     */
    function AtualizarClasse(&$_page, $cod_classe, $dados)
    {
         $sql = "UPDATE classe "
                . "SET nome='" . $dados["nome"] . "', "
                . "prefixo='" . $dados["prefixo"] . "', "
                . "descricao='" . $dados["descricao"] . "', "
                . "temfilhos='" . $dados["temfilhos"] . "', "
                . "indexar='" . $dados["index"] . "' "
                . "WHERE cod_classe=" . $cod_classe;
        $_page->_db->ExecSQL($sql);
    }
    
    /**
     * Cria classe no banco de dados
     * @param object $_page - Referência de objeto da classe Pagina
     * @param array $dados - Dados da classe
     * @return integer
     */
    function CriarClasse(&$_page, $dados)
    {
        $cod_classe = 0;
        
        $sql = "INSERT INTO classe "
                . "(nome, "
                . "prefixo, "
                . "descricao, "
                . "temfilhos, "
                . "indexar) "
                . "VALUES ('" . $dados["nome"] . "', "
                . "'" . $dados["prefixo"] . "', "
                . "'" . $dados["descricao"] . "', "
                . "'" . $dados["temfilhos"] . "', "
                . "'" . $dados["index"] . "')";
        $_page->_db->ExecSQL($sql);
        
        $sql = "SELECT max(cod_classe) as cod FROM classe";
        $rs = $_page->_db->ExecSQL($sql);
        while ($row = $rs->FetchRow())
        {
            $cod_classe = $row["cod"];
        }
        
        return $cod_classe;
    }

    /**
     * Apaga propriedade
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe
     * @param string $nome - Nome da propriedade a ser apagada
     */
    function ApagarPropriedadeDaClasse(&$_page, $cod_propriedade)
    {
        $sql = "SELECT tipodado.tabela "
                . "FROM propriedade "
                . "LEFT JOIN tipodado ON tipodado.cod_tipodado = propriedade.cod_tipodado "
                . "WHERE propriedade.cod_propriedade=".$cod_propriedade.";";
        $rs = $_page->_db->ExecSQL($sql);
        $row = $rs->fields;

        if (isset($row['tabela']) && $row['tabela']!="")
        {
            if ($row['tabela']=="tbl_blob")
            {
                $sql = "SELECT cod_blob, "
                        . "arquivo "
                        . "FROM tbl_blob "
                        . "WHERE cod_propriedade=".$cod_propriedade;
                $rs2 = $_page->_db->ExecSQL($sql);

                while ($row2 = $rs2->FetchRow())
                {
                    $file_ext = Blob::PegaExtensaoArquivo($row2['arquivo']);
                    if (file_exists(_BLOBDIR."/".Blob::identificaPasta($row2['cod_blob'])."/".$row2['cod_blob'].'.'.$file_ext))
                    {
                        $checkDelete = unlink(_BLOBDIR."/".Blob::identificaPasta($row2['cod_blob'])."/".$row2['cod_blob'].'.'.$file_ext);
                    }
                    if (defined ("_THUMBDIR"))
                    {
                        if (file_exists(_THUMBDIR.$row2['cod_blob'].'.'.$file_ext)) unlink(_THUMBDIR.$row2['cod_blob'].'.'.$file_ext);
                    }
                }
            }
            
            $sql = "DELETE FROM ".$row['tabela']." WHERE cod_propriedade=".$cod_propriedade;
            $_page->_db->ExecSQL($sql);
        }
        
        $sql = "DELETE FROM propriedade WHERE cod_propriedade=".$cod_propriedade;
        $_page->_db->ExecSQL($sql);
    }

    /**
     * Renomeia propriedade
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe
     * @param string $nomeatual - Nome atual da propriedade
     * @param string $nome - novo nome da propriedade
     */
    function RenomearPropriedadeDaClasse(&$_page, $cod_classe, $nomeatual, $nome)
    {
        $sql = "update propriedade set nome='$nome' where nome='$nomeatual' and cod_classe=$cod_classe";
        $_page->_db->ExecSQL($sql);
    }

    

    /**
     * Atualiza dados de propriedade ao criar ou alterar classe
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_propriedade - Codigo da propriedade
     * @param array $dados - dados da proprieadde
     */
    function AtualizarDadosDaPropriedade(&$_page, $cod_propriedade, $dados)
    {
        $sql = "UPDATE propriedade SET ";
//        if(isset($dados["codrefclasse"]) && $dados["codrefclasse"]>0) 
//        {
            $sql .= "cod_referencia_classe=".(!isset($dados["codrefclasse"]) || $dados["codrefclasse"]==0?"NULL":$dados["codrefclasse"]).", "
                . "campo_ref='".$dados["camporef"]."', ";
//        }
        $sql .= "nome='".$dados['nome']."', "
                . "posicao=".$dados['posicao'].", "
                . "descricao='".$dados['descricao']."', "
                . "rotulo='".$dados['rotulo']."', "
                . "rot1booleano='".$dados['rot1booleano']."', "
                . "rot2booleano='".$dados['rot2booleano']."', "
                . "obrigatorio=".$dados['obrigatorio'].", "
                . "seguranca=".$dados['seguranca'].", "
                . "valorpadrao='".$dados['valorpadrao']."' "
                . "WHERE cod_propriedade=".$cod_propriedade;
//        xd($sql);
        $_page->_db->ExecSQL($sql);
    }

    /**
     * Busca lista de seções de usuários e envia retorno para funcao que monta dropdown
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $selecionado - Campo que deverá estar seleiconado
     * @param bool $branco - indica se deverá conter elemento <option> com value em branco
     * @return string - lista de <options> para popular <select>
     */
    function DropDownUsuarioSecao(&$_page, $selecionado=0, $branco=false){
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
        $rs = $_page->_db->ExecSQL($sql);
        return $rs->GetRows();
    }

    /**
     * Busca lista de usuarios e envia retorno para funcao que monta dropdown
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $selecionado - Campo que deverá estar seleiconado
     * @param bool $branco - indica se deverá conter elemento <option> com value em branco
     * @param string $secao - seção para buscar usuários
     * @return string - lista de <options> para popular <select>
     */
    function DropDownUsuarios(&$_page, $selecionado, $branco=false, $secao=NULL)
    {
        $lista = $_page->_usuario->PegaListaDeUsuarios($_page, $secao);
        return $this->CriaDropDown($lista,$selecionado,$branco,20);
    }

    /**
     * Apaga classe do banco de dados e objetos que pertencam a ela
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe a ser apagada
     */
    function ApagarClasse(&$_page, $cod_classe)
    {
        // apagando objetos da classe
//        $sql = "select cod_objeto from objeto where cod_classe=".$cod_classe;
//        $rs = $_page->_db->ExecSQL($sql);
//        while ($row = $rs->FetchRow())
//        {
//            $this->ApagarObjeto($_page, $row['cod_objeto'],true);
//        }

        // apagando propriedades da classe
//        $sql = "delete from propriedade where cod_classe=".$cod_classe;
//        $_page->_db->ExecSQL($sql);

        // apagando relacionamentos entre classes
//$sql = "delete from classexfilhos where cod_classe=".$cod_classe;
//        $_page->_db->ExecSQL($sql);
//        $sql = "delete from classexfilhos where cod_classe_filho=".$cod_classe;
//        $_page->_db->ExecSQL($sql);

        // apagando relacionamentos entre classes e objetos
//        $sql = "delete from classexobjeto where cod_classe=".$cod_classe;
//        $_page->_db->ExecSQL($sql);

        // apagando a classe
        $sql  = "delete from classe where cod_classe=".$cod_classe;
        $_page->_db->ExecSQL($sql);
    }
    
    /**
     * Cria view automaticamente para a classe, caso não exista na pasta de template do portal
     * @param type $_page
     * @param type $cod_classe
     */
    function CriarTemplateClasse(&$_page, $cod_classe)
    {
        $dados = $this->PegaInfoDaClasse($_page, $cod_classe);
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
        . "<script>window.location='/content/view/<@= #cod_pai@>.html';</script>\r\n";

        if (!file_exists($pasta.$nome_arquivo))
        {
            $fp = fopen($pasta.$nome_arquivo, "w");
            fwrite($fp, $str);
            fclose($fp);
        }
    }

    /**
     * Busca perfil de usuário no objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_usuario - Codigo do usuario
     * @param int $cod_objeto - Codigo do objeto
     * @return boolean
     */
    function PegaPerfilDoUsuarioNoObjeto(&$_page, $cod_usuario, $cod_objeto)
    {
        if (empty($cod_usuario)) return false;
        $perfil = $_page->_usuario->PegaDireitosDoUsuario($_page, $cod_usuario);
        $caminho = explode(",", $_page->_adminobjeto->PegaCaminhoObjeto($_page, $cod_objeto));
        foreach ($perfil as $objeto => $cod_perfil)
        {
            if ((in_array($objeto, $caminho))) return $cod_perfil;
        }
        return false;
    }



    /**
     * Busca lista de objetos apagados logicamente
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $start - dados para paginação da busca
     * @param int $limit - dados para paginação da busca
     * @return array - Lista de objetos apagados
     */
    function PegaListaDeApagados(&$_page, $start=-1, $limit=-1)
    {
        $out=array();
        $sql = "select cod_objeto,data_exclusao,titulo,cod_usuario,classe.nome as classe from objeto
                        left join classe on classe.cod_classe=objeto.cod_classe
                        where apagado=1 order by data_exclusao desc";
        if ($limit!=-1 && $start!=-1)
        {
            $rs = $_page->_db->ExecSQL($sql, $start, $limit);

        }
        else
        {
            $rs = $_page->_db->ExecSQL($sql);
        }
        $row = $rs->GetRows();
        for ($l=0; $l<sizeof($row); $l++)
        {
                $row[$l]['exibir']="/index.php/content/view/".$row[$l]['cod_objeto'].".html";
                $out[]=$row[$l];
        }
        return $out;
    }
    
    /**
     * Consulta número de objetos vencidos
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Objeto pai da busca
     * @return int
     */
    function PegaTotalDeVencidos(&$_page, $cod_objeto)
    {
        $sql = "SELECT count(t1.cod_objeto) AS total "
                . "FROM objeto t1 "
                . "INNER JOIN classe t2 ON t2.cod_classe=t1.cod_classe "
                . "INNER JOIN parentesco t3 ON t1.cod_objeto=t3.cod_objeto "
                . "WHERE t3.cod_pai=".$cod_objeto." "
                . "AND t1.data_validade < ".date("Ymd")."000000 "
                . "AND t1.apagado=0";
        $rs = $_page->_db->ExecSQL($sql);
        return $rs->fields["total"];
    }

    /**
     * Busca lista de objetos vencidos
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $ord1 - Metadado para ordenação da lista
     * @param string $ord2 - asc ou desc
     * @param int $inicio - dados para paginação da busca
     * @param int $limite - dados para paginação da busca
     * @param int $cod_objeto - Objeto pai da busca
     * @return array - Lista de objetos vencidos
     */
    function PegaListaDeVencidos(&$_page, $ord1="titulo", $ord2="asc", $inicio=-1, $limite=-1, $cod_objeto=1)
    {
        $out=array();
        $sql = "SELECT t1.cod_objeto, "
                . "t1.titulo, "
                . "t1.cod_usuario, "
                . "t1.data_validade, "
                . "t2.nome as classe "
                . "FROM objeto t1 "
                . "INNER JOIN classe t2 ON t2.cod_classe=t1.cod_classe "
                . "INNER JOIN parentesco t3 ON t1.cod_objeto=t3.cod_objeto "
                . "WHERE t3.cod_pai=".$cod_objeto." "
                . "AND t1.data_validade < ".date("Ymd")."000000 "
                . "AND t1.apagado=0 ORDER BY t1.".$ord1." ".$ord2;
        $rs = $_page->_db->ExecSQL($sql, $inicio, $limite);
        $row = $rs->GetRows();

        for ($i=0; $i<sizeof($row); $i++)
        {
            $row[$i]['exibir']="/index.php?action=/content/view&cod_objeto=".$row[$i]['cod_objeto'];
            $out[]=$row[$i];
        }

        return $out;
    }
    
    /**
     * Apaga registros de log do objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     */
    function ApagarLogObjeto(&$_page, $cod_objeto)
    {
        $sql = "delete from logobjeto where cod_objeto=".$cod_objeto;
        $_page->_db->ExecSQL($sql);
    }
    
    /**
     * Apaga registros de log do objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     */
    function ApagarLogWorkflow(&$_page, $cod_objeto)
    {
        $sql = "delete from logworkflow where cod_objeto=".$cod_objeto;
        $_page->_db->ExecSQL($sql);
    }

    /**
     * Apaga objeto em definitivo - fisicamente
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a ser apagado
     */
    function ApagarEmDefinitivo(&$_page, $cod_objeto)
    {
        $sql = "select cod_objeto from parentesco where cod_pai=$cod_objeto";
        $res=$_page->_db->ExecSQL($sql);
        $row = $res->GetRows();

        for ($c=0; $c<sizeof($row); $c++)
        {
//            $this->ApagarLogObjeto($_page, $row[$c]["cod_objeto"]);
//            $this->ApagarLogWorkflow($_page, $row[$c]["cod_objeto"]);
//            $this->ApagarTags($_page, $row[$c]["cod_objeto"]);
//            $this->RemoveObjetoDaPilha($_page, $row[$c]["cod_objeto"], 0);
//            $this->ApagarParentesco($_page, $row[$c]["cod_objeto"]);
//            $this->ApagarPropriedades($_page, $row[$c]["cod_objeto"]);
//            $_page->_db->ExecSQL("delete from classexobjeto where cod_objeto=".$row[$c]["cod_objeto"]);
            $_page->_db->ExecSQL("delete from objeto where cod_objeto=".$row[$c]["cod_objeto"]);
        }

//        $this->ApagarLogObjeto($_page, $cod_objeto);
//        $this->ApagarLogWorkflow($_page, $cod_objeto);
//        $this->ApagarTags($_page, $cod_objeto);
//        $this->RemoveObjetoDaPilha($_page, $cod_objeto, 0);
//        $this->ApagarParentesco($_page, $cod_objeto);
//        $this->ApagarPropriedades($_page, $cod_objeto);
//        $_page->_db->ExecSQL("delete from classexobjeto where cod_objeto=".$cod_objeto);
        $_page->_db->ExecSQL("delete from objeto where cod_objeto=$cod_objeto");
    }

    /**
     * Recupera objeto apagado logicamente
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a ser recuperado
     */
    function RecuperarObjeto(&$_page, $cod_objeto)
    {
        $sql = "select parentesco.cod_objeto, 
        cod_status, 
        cod_classe 
        from parentesco 
        inner join objeto on parentesco.cod_objeto=objeto.cod_objeto 
        where parentesco.cod_pai=$cod_objeto 
        or parentesco.cod_objeto=$cod_objeto";
        $res=$_page->_db->ExecSQL($sql);
        $row = $res->GetRows();

        for ($i=0; $i<sizeof($row); $i++)
        {
            $sql = "update objeto set apagado=0 ";
            $sql .= " where cod_objeto=".$row[$i]['cod_objeto'];
            $_page->_db->ExecSQL($sql);
        }
        $_page->_log->IncluirLogObjeto($_page, $cod_objeto, _OPERACAO_OBJETO_RECUPERAR);
    }

    /**
     * Verifica se propriedade tem preenchimento obrigatorio
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_classe - Codigo da classe que propriedade pertence
     * @param array $propriedades - Lista de propriedades
     * @return boolean
     */
    function ValidarPropriedades(&$_page, $cod_classe, $propriedades)
    {
        $lista = $this->PegaPropriedadesDaClasse($_page, $cod_classe);
        foreach ($lista as $prop)
        {
            if (($prop['obrigatorio']) && (!strlen($propriedades['prop:'.$prop['nome']]))) return false;
        }
        return true;	
    }

}
