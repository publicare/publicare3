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

use Exception;

use Pbl\Core\Base;

class Schema extends Base
{

    public function verifica()
    {
        try {
            $tblClasse = $this->container["config"]->bd["tabelas"]["classe"];
            $sql = "SELECT count(*) FROM ".$tblClasse["nome"];
            if (!$rs = $this->container["db"]->execute($sql))
            {
                $this->criaBanco();
            }
        }
        catch(Exception $e)
        {
            xd("Erro ao iniciar banco de dados. ".$e->getMessage());
        }
        
    }

    private function criaBanco()
    {
        throw new \Exception("Tabelas não encontradas");

        $schema = $this->container["config"]->bd["tabelas"];
        foreach ($schema as $tbl)
        {
            x($tbl);
        }
    }

    public function criaTabela()
    {
        xd($this->container["config"]->getDados());
    }

}