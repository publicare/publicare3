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
 * Classe que cuida dos logs do publicare
 */
class ClasseLog
{
    public $_page;
    
    /**
     * Metodo construtor, coloca array de metadados em propriedade local
     * @param object $_page - Referência de objeto da classe Pagina
     */
    function __construct(&$_page)
    {
        $this->_page = $_page;
    }
	
    /**
     * Registra log workflow de objeto
     * @param string $mensagem - Mensagem para gravar no log
     * @param int $cod_objeto - Codigo do objeto para gravar log
     * @param int $cod_status - Codigo do status a ser gravado no log
     */
    function RegistraLogWorkflow($mensagem, $cod_objeto, $cod_status)
    {
        $sql = "INSERT INTO ".$this->_page->_db->tabelas["logworkflow"]["nome"]." ("
                . "".$this->_page->_db->tabelas["logworkflow"]["colunas"]["cod_objeto"].", "
                . "".$this->_page->_db->tabelas["logworkflow"]["colunas"]["cod_usuario"].", "
                . "".$this->_page->_db->tabelas["logworkflow"]["colunas"]["mensagem"].", "
                . "".$this->_page->_db->tabelas["logworkflow"]["colunas"]["cod_status"].", "
                . "".$this->_page->_db->tabelas["logworkflow"]["colunas"]["estampa"].""
                . ") VALUES ("
                . "".$cod_objeto.","
                . "".$_SESSION['usuario']['cod_usuario'].","
                . "'".$mensagem."',"
                . "".$cod_status.","
                . "".$this->_page->_db->TimeStamp().')';
        $this->_page->_db->ExecSQL($sql);
    }

    /**
     * Recupera lista de logs workflow de objeto
     * @param int $cod_objeto - Codigo do objeto a pegar o log
     * @return array - Entradas do log
     */
    function PegaLogWorkflow($cod_objeto)
    {
        $result = array();
        $sql = "SELECT ".$this->_page->_db->tabelas["usuario"]["nick"].".".$this->_page->_db->tabelas["usuario"]["colunas"]["nome"]." AS usuario, "
                . " ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["mensagem"]." AS mensagem, "
                . " ".$this->_page->_db->tabelas["status"]["nick"].".".$this->_page->_db->tabelas["status"]["colunas"]["nome"]." AS status, "
                . " ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["estampa"]." AS estampa "
                . " FROM ".$this->_page->_db->tabelas["logworkflow"]["nome"]." ".$this->_page->_db->tabelas["logworkflow"]["nick"]." "
                . " LEFT JOIN ".$this->_page->_db->tabelas["usuario"]["nome"]." ".$this->_page->_db->tabelas["usuario"]["nick"]." "
                    . " ON ".$this->_page->_db->tabelas["usuario"]["nick"].".".$this->_page->_db->tabelas["usuario"]["colunas"]["cod_usuario"]." = ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["cod_usuario"].""
                . " LEFT JOIN ".$this->_page->_db->tabelas["status"]["nome"]." ".$this->_page->_db->tabelas["status"]["nick"]." "
                    . " ON ".$this->_page->_db->tabelas["status"]["nick"].".".$this->_page->_db->tabelas["status"]["colunas"]["cod_status"]." = ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["cod_status"]." "
                . " WHERE ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["cod_objeto"]." = ".$cod_objeto." "
                . " ORDER BY ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["estampa"]." DESC";
//            mensagem, 
//                        status.nome as status, estampa from logworkflow 
//                        left join usuario on usuario.cod_usuario=logworkflow.cod_usuario
//                        left join status on status.cod_status=logworkflow.cod_status
//                        where cod_objeto=".$cod_objeto." order by estampa desc";
        $res = $this->_page->_db->ExecSQL($sql);
        $row = $res->GetRows();

        for ($i=0; $i<sizeof($row); $i++)
        {
            $row[$i]['estampa']=ConverteData($row[$i]['estampa'],1);
            $result[]=$row[$i];
        }
        return $result;
    }

    /**
     * Pega informações de objeto
     * @param int $cod_objeto - Codigo do objeto
     * @return array - dados do objeto
     */
    function InfoObjeto($cod_objeto)
    {
        $result = array();
        
        $log = $this->PegaLogWorkflow($cod_objeto);
        
        if (count($log) > 0)
        {
            $result = $log[0];
            $result['estampa'] = ConverteData($result['estampa'], 1);
        }
        
//        $sql = "select usuario.nome as usuario, mensagem, 
//                        status.nome as status, estampa from logworkflow 
//                        left join usuario on usuario.cod_usuario=logworkflow.cod_usuario
//                        left join status on status.cod_status=logworkflow.cod_status
//                        where cod_objeto=$cod_objeto order by estampa desc";
//        
//        $sql = "SELECT ".$this->_page->_db->tabelas["usuario"]["nick"].".".$this->_page->_db->tabelas["usuario"]["colunas"]["nome"]." AS usuario, "
//                . " ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["mensagem"]." AS mensagem, "
//                . " ".$this->_page->_db->tabelas["status"]["nick"].".".$this->_page->_db->tabelas["status"]["colunas"]["nome"]." AS status, "
//                . " ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["estampa"]." AS estampa "
//                . " FROM ".$this->_page->_db->tabelas["logworkflow"]["nome"]." AS ".$this->_page->_db->tabelas["logworkflow"]["nick"]." "
//                . " LEFT JOIN ".$this->_page->_db->tabelas["usuario"]["nome"]." AS ".$this->_page->_db->tabelas["usuario"]["nick"]." "
//                    . " ON ".$this->_page->_db->tabelas["usuario"]["nick"].".".$this->_page->_db->tabelas["usuario"]["colunas"]["cod_usuario"]." = ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["cod_usuario"].""
//                . " LEFT JOIN ".$this->_page->_db->tabelas["status"]["nome"]." AS ".$this->_page->_db->tabelas["status"]["nick"]." "
//                    . " ON ".$this->_page->_db->tabelas["status"]["nick"].".".$this->_page->_db->tabelas["status"]["colunas"]["cod_status"]." = ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["cod_status"]." "
//                . " WHERE ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["cod_objeto"]." = ".$cod_objeto." "
//                . " ORDER BY ".$this->_page->_db->tabelas["logworkflow"]["nick"].".".$this->_page->_db->tabelas["logworkflow"]["colunas"]["estampa"]." DESC";
//        
//        
//        $res = $this->_page->_db->ExecSQL($sql,0,1);
//        $row = $res->GetRows();
//        for ($i=0; $i<sizeof($row); $i++)
//        {
//            $result = $row[$i];
//        }
//        $result['estampa'] = ConverteData($result['estampa'],1);
        return $result;
    }
	
    /**
     * Grava log de alterações do objeto
     * @param int $cod_objeto - Codigo do objeto
     * @param int $operacao - Operação realizada a ser gravada
     */
    function IncluirLogObjeto($cod_objeto, $operacao)
    {
        $sql = "INSERT INTO ".$this->_page->_db->tabelas["logobjeto"]["nome"]." ("
                . " ".$this->_page->_db->tabelas["logobjeto"]["colunas"]["cod_objeto"].", "
                . " ".$this->_page->_db->tabelas["logobjeto"]["colunas"]["cod_usuario"].", "
                . " ".$this->_page->_db->tabelas["logobjeto"]["colunas"]["cod_operacao"].", "
                . " ".$this->_page->_db->tabelas["logobjeto"]["colunas"]["estampa"]." "
                . " ) VALUES ( "
                . " ".$cod_objeto.", "
                . " ".$_SESSION['usuario']['cod_usuario'].", "
                . " ".$operacao.", "
                . " ".$this->_page->_db->TimeStamp().')';
        $this->_page->_db->ExecSQL($sql);
        
        if ($operacao == _OPERACAO_OBJETO_REMOVER || $operacao == _OPERACAO_OBJETO_RECUPERAR)
        {
            $sql = "INSERT INTO ".$this->_page->_db->tabelas["logobjeto"]["nome"]." ("
                    . " ".$this->_page->_db->tabelas["logobjeto"]["colunas"]["cod_objeto"].", "
                    . " ".$this->_page->_db->tabelas["logobjeto"]["colunas"]["cod_usuario"].", "
                    . " ".$this->_page->_db->tabelas["logobjeto"]["colunas"]["cod_operacao"].", "
                    . " ".$this->_page->_db->tabelas["logobjeto"]["colunas"]["estampa"].") "
                    . " SELECT ".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_objeto"].", "
                    . " ".$_SESSION['usuario']['cod_usuario'].", "
                    . " ".$operacao.", "
                    . " ".$this->_page->_db->TimeStamp().''
                    . " FROM ".$this->_page->_db->tabelas["parentesco"]["nome"]." "
                    . " WHERE ".$this->_page->_db->tabelas["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto;
            $this->_page->_db->ExecSQL($sql);
        }
    }
		
    /**
     * Pega log de um objeto
     * @global array $_OPERACAO_OBJETO
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Lista com entradas do log
     */
    function PegaLogObjeto($cod_objeto)
    {
        $result = array();
        $_OPERACAO_OBJETO = array('','Criar','Editar','Apagar','Recuperar');
        $sql = "SELECT ".$this->_page->_db->tabelas["usuario"]["nick"].".".$this->_page->_db->tabelas["usuario"]["colunas"]["nome"]." AS usuario, "
                . " ".$this->_page->_db->tabelas["logobjeto"]["nick"].".".$this->_page->_db->tabelas["logobjeto"]["colunas"]["cod_operacao"]." AS cod_operacao, "
                . " ".$this->_page->_db->tabelas["logobjeto"]["nick"].".".$this->_page->_db->tabelas["logobjeto"]["colunas"]["estampa"]." AS estampa "
                . " FROM ".$this->_page->_db->tabelas["logobjeto"]["nome"]." ".$this->_page->_db->tabelas["logobjeto"]["nick"]." "
                . " LEFT JOIN ".$this->_page->_db->tabelas["usuario"]["nome"]." ".$this->_page->_db->tabelas["usuario"]["nick"]." "
                    . " ON ".$this->_page->_db->tabelas["usuario"]["nick"].".".$this->_page->_db->tabelas["usuario"]["colunas"]["cod_usuario"]." = ".$this->_page->_db->tabelas["logobjeto"]["nick"].".".$this->_page->_db->tabelas["logobjeto"]["colunas"]["cod_usuario"]." "
                . " WHERE ".$this->_page->_db->tabelas["logobjeto"]["nick"].".".$this->_page->_db->tabelas["logobjeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto." "
                . " ORDER BY ".$this->_page->_db->tabelas["logobjeto"]["nick"].".".$this->_page->_db->tabelas["logobjeto"]["colunas"]["estampa"]." DESC";
        $res = $this->_page->_db->ExecSQL($sql);
        $row = $res->GetRows();

        for ($i=0; $i<sizeof($row); $i++)
        {
            $row[$i]['estampa']=ConverteData($row[$i]['estampa'],1);
            $row[$i]['operacao']=$_OPERACAO_OBJETO[$row[$i]['cod_operacao']];
            $result[]=$row[$i];
        }
        return $result;
    }
}

