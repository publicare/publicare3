<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @file 
 * @description 
 * @copyright MIT © 2020
 * @package Pbl/Core/Config
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
use Exception;

/**
 * Classe para abstração de dados
 * Esta classe utiliza o ADODB.sf.net
 */
class Config extends Base
{
    private $dados = array();

    public function __get($var)
    {
        // se array de dados estiver vazio carrega arquivo
        if (count($this->dados) == 0) $this->carrega();

        if (isset($this->dados[$var])) return $this->dados[$var];
        return null;
    }

    private function carrega()
    {
        $dadosArquivo = $this->carregaArquivo();
        $dadosPadrao = $this->container["config_padrao"]->getDados();
        $this->dados  = array_merge($dadosPadrao,$dadosArquivo);
        $this->dados["bd"]["tabelas"]  = array_merge($dadosPadrao["bd"]["tabelas"], isset($this->dados["bd"]["tabelas"])?$this->dados["bd"]["tabelas"]:array());
        // $this->dados["bd"]["tabelas"]  = array_merge($dadosPadrao["bd"]["tabelas"], $this->dados["bd"]["tabelas"]);
    }
    
    /**
     * Carrega arquivo de configurações
     */
    private function carregaArquivo()
    {
        $path = getenv('DOCUMENT_ROOT')."/../config/global.php";
        if (!file_exists($path))
        {
            throw new Exception("ARQUIVO NAO ENCONTRADO: ".$path);
        }
        return require(getenv('DOCUMENT_ROOT')."/../config/global.php");
    }

    public function getDados()
    {
        if (count($this->dados) == 0) $this->carrega();
        return $this->dados;
    }

} 

