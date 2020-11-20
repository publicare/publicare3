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
use \Exception;
use \Error;

class Conecta extends Base
{

    private $con = null;

    public function getCon()
    {
        if ($this->con === null) $this->conecta();
        return $this->con;
    }

    private function tipoConexao()
    {
        switch ($this->container["config"]->bd["tipo"])
        {
            case "pdo_oci":
            case "oracle":
            case "oracle11":
            case "oci8":
                return "oci8";
                break;
            case "pdo_pgsql":
            case "pgsql":
            case "postgres":
            case "postgresql":
                return "pgsql";
                break;
            case "mysql":
            case "mysqli":
            case "pdo_mysql":
                return "mysqli";
                break;
        }
        return false;
    }

    private function conecta()
    {
        try {

            $config = $this->container["config"];
            $tipo = $this->tipoConexao();
            if (!$tipo)
            {
                throw new Exception("Tipo de conexao nao reconhecido: ".$config->bd["tipo"]);
            }
            
            // configuracoes pre-conexao
            switch ($tipo)
            {
                case "oci8":
                    define('ADODB_ASSOC_CASE', 0);
                    putenv("NLS_COMP=LINGUISTIC");
                    putenv("NLS_LANG=AMERICAN_AMERICA.AL32UTF8");
                    putenv("NLS_SORT=BINARY_CI");
                break;
            }

            // inicia objeto adodb com a conexao
            $this->con = ADONewConnection($tipo);
            // conectando
            $this->con->Connect($this->container["config"]->bd["host"].":".$this->container["config"]->bd["porta"], $this->container["config"]->bd["usuario"], $this->container["config"]->bd["senha"], $this->container["config"]->bd["nome"]);
            // debug
            $this->con->debug = $this->container["config"]->bd["debug"];

            if ($this->container["config"]->bd["cache"] === true
                && $this->container["config"]->bd["cachetipo"] == "memoria" )
            {
                $this->con->memCache = true;
                $this->con->memCacheHost = preg_split("[,]", $this->container["config"]->bd["cachehost"]);
                $this->con->memCachePort = $this->container["config"]->bd["cacheporta"];
                $this->con->memCacheCompress = $this->container["config"]->bd["cachecompress"];
            }

            // $saveErrorFn = $this->con->raiseErrorFn;
            // $this->con->raiseErrorFn = 'ignoreErrorHandler';
            // set_error_handler('ignoreErrorHandler');

            // pos conexao
            switch ($tipo)
            {
                case "pgsql":
                    $this->con->Execute("SET CLIENT_ENCODING TO 'UTF8'");
                break;
                case "mysqli":
                    $this->con->Execute("set names utf8");
                break;
            }

            $this->con->SetFetchMode(ADODB_FETCH_ASSOC);
            // restore_error_handler();
            // $this->con->raiseErrorFn = $saveErrorFn;

            if ($this->con->errorMsg() && $this->con->errorMsg()!="")
            {
                throw new Exception($this->con->errorMsg());
            }
        }
        catch (Exception $e)
        {
            echo("Erro ao conectar banco de dados: ".$e->getMessage());
            exit();
        }

    }

}