<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @file
 * @description 
 * @copyright MIT © 2020
 * @package Pbl\Core\Banco
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

namespace Pbl\Core\Banco;

use Pbl\Core\Base;

class Banco extends Base
{

    private $sqlObj = null;
    private $sqlObjSel = null;
    private $sqlObjFrom = null;

    private function iniciaSqls()
    {
        $tblClasse = $this->container["config"]->bd["tabelas"]["classe"];
        $tblObjeto = $this->container["config"]->bd["tabelas"]["objeto"];
        $tblPele = $this->container["config"]->bd["tabelas"]["pele"];
        $tblStatus = $this->container["config"]->bd["tabelas"]["status"];

        $this->sqlObjSel = " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_objeto"]." AS cod_objeto, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_pai"]." AS cod_pai, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_classe"]." AS cod_classe, "
                . " ".$tblClasse["nick"].".".$tblClasse["colunas"]["nome"]." AS classe, "
                . " ".$tblClasse["nick"].".".$tblClasse["colunas"]["temfilhos"]." AS temfilhos, "
                . " ".$tblClasse["nick"].".".$tblClasse["colunas"]["prefixo"]." AS prefixoclasse, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_usuario"]." AS cod_usuario, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_pele"]." AS cod_pele, "
                . " ".$tblPele["nick"].".".$tblPele["colunas"]["nome"]." AS pele, "
                . " ".$tblPele["nick"].".".$tblPele["colunas"]["prefixo"]." AS prefixopele, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_status"]." AS cod_status, "
                . " ".$tblStatus["nick"].".".$tblStatus["colunas"]["nome"]." AS status, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["titulo"]." AS titulo, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["descricao"]." AS descricao, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["data_publicacao"]." AS data_publicacao, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["data_validade"]." AS data_validade, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["script_exibir"]." AS script_exibir, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["apagado"]." AS apagado, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["objetosistema"]." AS objetosistema, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["peso"]." AS peso, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["url_amigavel"]." AS url_amigavel, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["versao"]." AS versao, "
                . " ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["versao_publicada"]." AS versao_publicada ";
	
	// definindo clausula from do sql geral de consulta
        $this->sqlObjFrom = " FROM ".$tblObjeto["nome"]." ".$tblObjeto["nick"]." "
                . "LEFT JOIN ".$tblClasse["nome"]." ".$tblClasse["nick"]." "
                    . "ON ".$tblClasse["nick"].".".$tblClasse["colunas"]["cod_classe"]." = ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_classe"]." "
                . "LEFT JOIN ".$tblPele["nome"]." ".$tblPele["nick"]." "
                    ."ON ".$tblPele["nick"].".".$tblPele["colunas"]["cod_pele"]." = ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_pele"]." "
                . "LEFT JOIN ".$tblStatus["nome"]." ".$tblStatus["nick"]." "
                    . "ON ".$tblStatus["nick"].".".$tblStatus["colunas"]["cod_status"]." = ".$tblObjeto["nick"].".".$tblObjeto["colunas"]["cod_status"]." ";

        $this->sqlObj = "SELECT ".$this->sqlObjSel." ".$this->sqlObjFrom;
    }

    public function getSqlObj()
    {
        if ($this->sqlObj === null) $this->iniciaSqls();
        return $this->sqlObj;
    }

    public function execSQL($sql, $start=-1, $limit=-1)
    {
        return $this->execute($sql, $start, $limit);
    }

    public function execute($sql, $start=-1, $limit=-1)
    {
        if ($this->container["config"]->bd["cache"] === true && stripos($sql, "insert into") === false)
        {
            return $this->executeCache($sql, $start, $limit);
        }

        if ($limit != -1)
        {
            if ($start == -1) $start = 0;
            if (is_array($sql)) return $this->getCon()->SelectLimit($sql[0], $limit, $start, $sql[1]);
            else return $this->container["db_con"]->getCon()->SelectLimit($sql, $limit, $start);
        }
        else
        {
            if (is_array($sql)) return $this->getCon()->Execute($sql[0], $sql[1]);
            else return $this->container["db_con"]->getCon()->Execute($sql);
        }
    }

    /**
     * Executa a consulta usando cache
     */
    public function executeCache($sql, $start=-1, $limit=-1)
    {
        GLOBAL $ADODB_CACHE_DIR;
        
        if (isset($this->container["config"]->bd["cachepath"]) 
            && $this->container["config"]->bd["cachepath"] != "") 
        {
            $ADODB_CACHE_DIR = $this->container["config"]->bd["cachepath"];
        }
    
        $tempo = 60 * 60 * 2;
        if (isset($this->container["config"]->bd["cachetempo"])
            && (int)$this->container["config"]->bd["cachetempo"] > 0)
        {
            $tempo = (int)$this->container["config"]->bd["cachetempo"];   
        }

        if ($limit != -1)
        {
            if ($start == -1) $start = 0;
            if (is_array($sql)) return $this->con->CacheSelectLimit($tempo, $sql[0], $limit, $start, $sql[1]);
            else return $this->con->CacheSelectLimit($tempo, $sql, $limit, $start); 
        }
        else
        {
            if (is_array($sql)) return $this->con->CacheExecute($tempo, $sql[0], $sql[1]);
            else return $this->con->CacheExecute($tempo, $sql);
        }
    }

}