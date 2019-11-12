<?php
/**
* Publicare - O CMS Público Brasileiro
* @description Classe ClasseLog - Responsável por gerenciar os logs do publicare
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
 * Classe que cuida dos logs do publicare
 */
class ClasseLog
{

    public $ArrayMetadados;
    
    /**
     * Metodo construtor, coloca array de metadados em propriedade local
     * @param object $_page - Referência de objeto da classe Pagina
     */
    function __construct(&$_page)
    {
        $this->ArrayMetadados=$_page->_db->metadados;
    }
	
    /**
     * Registra log workflow de objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param string $mensagem - Mensagem para gravar no log
     * @param int $cod_objeto - Codigo do objeto para gravar log
     * @param int $cod_status - Codigo do status a ser gravado no log
     */
    function RegistraLogWorkflow(&$_page, $mensagem, $cod_objeto, $cod_status)
    {
        $sql = "insert into logworkflow (cod_objeto,cod_usuario,mensagem,"
                . "cod_status,estampa) values (".$cod_objeto.",".$_SESSION['usuario']['cod_usuario'].","
                . "'".$mensagem."',".$cod_status.",".$_page->_db->TimeStamp().')';
        $_page->_db->ExecSQL($sql);
    }

    /**
     * Recupera lista de logs workflow de objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto a pegar o log
     * @return array - Entradas do log
     */
    function PegaLogWorkflow(&$_page, $cod_objeto)
    {
        $result = array();
        $sql = "select usuario.nome as usuario, mensagem, 
                        status.nome as status, estampa from logworkflow 
                        left join usuario on usuario.cod_usuario=logworkflow.cod_usuario
                        left join status on status.cod_status=logworkflow.cod_status
                        where cod_objeto=".$cod_objeto." order by estampa desc";
        $res = $_page->_db->ExecSQL($sql);
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
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @return array - dados do objeto
     */
    function InfoObjeto(&$_page, $cod_objeto)
    {
        $sql = "select usuario.nome as usuario, mensagem, 
                        status.nome as status, estampa from logworkflow 
                        left join usuario on usuario.cod_usuario=logworkflow.cod_usuario
                        left join status on status.cod_status=logworkflow.cod_status
                        where cod_objeto=$cod_objeto order by estampa desc";
        $res = $_page->_db->ExecSQL($sql,0,1);
        $row = $res->GetRows();
        for ($i=0; $i<sizeof($row); $i++)
        {
            $result = $row[$i];
        }
        $result['estampa'] = ConverteData($result['estampa'],1);
        return $result;
    }
	
    /**
     * Grava log de alterações do objeto
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @param int $operacao - Operação realizada a ser gravada
     */
    function IncluirLogObjeto(&$_page, $cod_objeto, $operacao)
    {
        $sql = "insert into logobjeto (cod_objeto,cod_usuario,cod_operacao,estampa) "
                . "values (".$cod_objeto.",".$_SESSION['usuario']['cod_usuario'].","
                . $operacao.",".$_page->_db->TimeStamp().')';
        $_page->_db->ExecSQL($sql);
        
        if ($operacao == _OPERACAO_OBJETO_REMOVER || $operacao == _OPERACAO_OBJETO_RECUPERAR)
        {
                $sql = "insert into logobjeto (cod_objeto,cod_usuario,cod_operacao,estampa) "
                        . "select cod_objeto,".$_SESSION['usuario']['cod_usuario'].","
                        . "".$operacao.",".$_page->_db->TimeStamp()." from parentesco "
                        . "where cod_pai=$cod_objeto";
                $_page->_db->ExecSQL($sql);
        }
    }
		
    /**
     * Pega log de um objeto
     * @global array $_OPERACAO_OBJETO
     * @param object $_page - Referência de objeto da classe Pagina
     * @param int $cod_objeto - Codigo do objeto
     * @return array - Lista com entradas do log
     */
    function PegaLogObjeto(&$_page, $cod_objeto)
    {
        $result = "";
        $_OPERACAO_OBJETO = array('','Criar','Editar','Apagar','Recuperar');
        $sql = "select usuario.nome as usuario, cod_operacao,
                        estampa from logobjeto
                        left join usuario on usuario.cod_usuario=logobjeto.cod_usuario
                        where cod_objeto=$cod_objeto order by estampa desc";
        $res = $_page->_db->ExecSQL($sql);
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

