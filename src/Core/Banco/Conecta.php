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

class Conecta extends Base
{

    private $con = null;

    public function getCon()
    {
        if ($this->con === null) $this->conecta();
        return $this->con;
    }

    private function conecta()
    {
        try {
            switch ($this->container["config"]->bd["tipo"])
            {
                case "pdo_oci":
                case "oracle":
                case "oracle11":
                    define('ADODB_ASSOC_CASE', 0);
                    putenv("NLS_COMP=LINGUISTIC");
                    putenv("NLS_LANG=AMERICAN_AMERICA.AL32UTF8");
                    putenv("NLS_SORT=BINARY_CI");
                    $this->con = ADONewConnection("oci8");
                    $this->con->Connect($this->container["config"]->bd["host"].":".$this->container["config"]->bd["porta"], $this->container["config"]->bd["usuario"], $this->container["config"]->bd["senha"], $this->container["config"]->bd["nome"]);
                break;
                case "pdo_pgsql":
                case "pgsql":
                case "postgres":
                case "postgresql":
                    $this->con = ADONewConnection("pgsql");
                    $this->con->Connect($this->container["config"]->bd["host"].":".$this->container["config"]->bd["porta"], $this->container["config"]->bd["usuario"], $this->container["config"]->bd["senha"], $this->container["config"]->bd["nome"]);
                    $this->con->Execute("SET CLIENT_ENCODING TO 'UTF8'");
                    break;
                case "mysql":
                case "mysqli":
                case "pdo_mysql":
                    $this->con = ADONewConnection("mysqli");
                    $this->con->Connect($this->container["config"]->bd["host"].":".$this->container["config"]->bd["porta"], $this->container["config"]->bd["usuario"], $this->container["config"]->bd["senha"], $this->container["config"]->bd["nome"]);
                    $this->con->Execute("set names utf8");
                    break;
            }

            $this->con->debug = $this->container["config"]->bd["debug"];
            $this->con->SetFetchMode(ADODB_FETCH_ASSOC);
        }
        catch (Exception $e)
        {
            echo("Erro ao conectar banco de dados: ".$e->getMessage());
            exit();
        }

    }

}