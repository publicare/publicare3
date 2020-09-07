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

/**
 * Classe que cuida dos logs do publicare
 */
class Log extends Base
{
    // /**
    //  * Metodo construtor, coloca array de metadados em propriedade local
    //  * @param object $page - Referência de objeto da classe Pagina
    //  */
    // function __construct(&$page)
    // {
    //     $this->page = $page;
    // }
	
    /**
     * Registra log workflow de objeto
     * @param string $mensagem - Mensagem para gravar no log
     * @param int $cod_objeto - Codigo do objeto para gravar log
     * @param int $cod_status - Codigo do status a ser gravado no log
     */
    function registrarLogWorkflow($mensagem, $cod_objeto, $cod_status)
    {
        $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["logworkflow"]["nome"]." ("
                . "".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["cod_objeto"].", "
                . "".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["cod_usuario"].", "
                . "".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["mensagem"].", "
                . "".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["cod_status"].", "
                . "".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["estampa"].""
                . ") VALUES ("
                . "".$cod_objeto.","
                . "".$_SESSION['usuario']['cod_usuario'].","
                . "'".$mensagem."',"
                . "".$cod_status.","
                . "".$this->container["db"]->timeStamp().')';
        $this->container["db"]->execSQL($sql);
    }

    /**
     * Recupera lista de logs workflow de objeto
     * @param int $cod_objeto - Codigo do objeto a pegar o log
     * @return array - Entradas do log
     */
    function pegarLogWorkflow($cod_objeto)
    {
        $result = array();
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS usuario, "
                . " ".$this->container["config"]->bd["tabelas"]["logworkflow"]["nick"].".".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["mensagem"]." AS mensagem, "
                . " ".$this->container["config"]->bd["tabelas"]["status"]["nick"].".".$this->container["config"]->bd["tabelas"]["status"]["colunas"]["nome"]." AS status, "
                . " ".$this->container["config"]->bd["tabelas"]["logworkflow"]["nick"].".".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["estampa"]." AS estampa "
                . " FROM ".$this->container["config"]->bd["tabelas"]["logworkflow"]["nome"]." ".$this->container["config"]->bd["tabelas"]["logworkflow"]["nick"]." "
                . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = ".$this->container["config"]->bd["tabelas"]["logworkflow"]["nick"].".".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["cod_usuario"].""
                . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["status"]["nome"]." ".$this->container["config"]->bd["tabelas"]["status"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["status"]["nick"].".".$this->container["config"]->bd["tabelas"]["status"]["colunas"]["cod_status"]." = ".$this->container["config"]->bd["tabelas"]["logworkflow"]["nick"].".".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["cod_status"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["logworkflow"]["nick"].".".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["cod_objeto"]." = ".$cod_objeto." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["logworkflow"]["nick"].".".$this->container["config"]->bd["tabelas"]["logworkflow"]["colunas"]["estampa"]." DESC";
//            mensagem, 
//                        status.nome as status, estampa from logworkflow 
//                        left join usuario on usuario.cod_usuario=logworkflow.cod_usuario
//                        left join status on status.cod_status=logworkflow.cod_status
//                        where cod_objeto=".$cod_objeto." order by estampa desc";
        $res = $this->container["db"]->execSQL($sql);
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
    function infoObjeto($cod_objeto)
    {
        $result = array();
        
        $log = $this->pegarLogWorkflow($cod_objeto);
//        xd($log);
        
        if (count($log) > 0)
        {
            $result = $log[0];
            $result['estampa'] = ConverteData($result['estampa'], 1);
        }
        
        return $result;
    }
	
    /**
     * Grava log de alterações do objeto
     * @param int $cod_objeto - Codigo do objeto
     * @param int $operacao - Operação realizada a ser gravada
     */
    function incluirLogObjeto($cod_objeto, $operacao)
    {
        $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["logobjeto"]["nome"]." ("
                . " ".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["cod_objeto"].", "
                . " ".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["cod_usuario"].", "
                . " ".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["cod_operacao"].", "
                . " ".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["estampa"]." "
                . " ) VALUES ( "
                . " ".$cod_objeto.", "
                . " ".$_SESSION['usuario']['cod_usuario'].", "
                . " ".$operacao.", "
                . " ".$this->container["db"]->timeStamp().')';
        $this->container["db"]->execSQL($sql);
        
        if ($operacao == _OPERACAO_OBJETO_REMOVER || $operacao == _OPERACAO_OBJETO_RECUPERAR)
        {
            $sql = "INSERT INTO ".$this->container["config"]->bd["tabelas"]["logobjeto"]["nome"]." ("
                    . " ".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["cod_objeto"].", "
                    . " ".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["cod_usuario"].", "
                    . " ".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["cod_operacao"].", "
                    . " ".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["estampa"].") "
                    . " SELECT ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_objeto"].", "
                    . " ".$_SESSION['usuario']['cod_usuario'].", "
                    . " ".$operacao.", "
                    . " ".$this->container["db"]->timeStamp().''
                    . " FROM ".$this->container["config"]->bd["tabelas"]["parentesco"]["nome"]." "
                    . " WHERE ".$this->container["config"]->bd["tabelas"]["parentesco"]["colunas"]["cod_pai"]." = ".$cod_objeto;
            $this->container["db"]->execSQL($sql);
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
        $sql = "SELECT ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["nome"]." AS usuario, "
                . " ".$this->container["config"]->bd["tabelas"]["logobjeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["cod_operacao"]." AS cod_operacao, "
                . " ".$this->container["config"]->bd["tabelas"]["logobjeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["estampa"]." AS estampa "
                . " FROM ".$this->container["config"]->bd["tabelas"]["logobjeto"]["nome"]." ".$this->container["config"]->bd["tabelas"]["logobjeto"]["nick"]." "
                . " LEFT JOIN ".$this->container["config"]->bd["tabelas"]["usuario"]["nome"]." ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"]." "
                    . " ON ".$this->container["config"]->bd["tabelas"]["usuario"]["nick"].".".$this->container["config"]->bd["tabelas"]["usuario"]["colunas"]["cod_usuario"]." = ".$this->container["config"]->bd["tabelas"]["logobjeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["cod_usuario"]." "
                . " WHERE ".$this->container["config"]->bd["tabelas"]["logobjeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["cod_objeto"]." = ".$cod_objeto." "
                . " ORDER BY ".$this->container["config"]->bd["tabelas"]["logobjeto"]["nick"].".".$this->container["config"]->bd["tabelas"]["logobjeto"]["colunas"]["estampa"]." DESC";
        $res = $this->container["db"]->execSQL($sql);
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

