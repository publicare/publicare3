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
?>
<!-- === Menu === -->
<ul class="nav nav-tabs">
    <li class="nav-item"><a class="nav-link active" href="do/indexportal/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Informações do Portal</a></li>
    <li class="nav-item"><a class="nav-link" href="do/gerusuario/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Gerenciar usuários</a></li>
    <li class="nav-item"><a class="nav-link" href="do/classes/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Gerenciar classes</a></li>
    <li class="nav-item"><a class="nav-link" href="do/peles/<?php echo($this->container["objeto"]->valor('cod_objeto')) ?>.html">Gerenciar Peles</a></li>
</ul>
<!-- === FInal === Menu === -->

<!-- === Informações do Publicare === -->
<div class="card">
    <div class="card-header bg-primary text-white"><h3><b>Informações do Portal</b></h3></div>
    <div class="card-body">
        
        <div id="list-conter-classe"> 
            <ul>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>Nome do Site:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo $this->container["config"]->portal["nome"] . " [<i>" . $this->container["config"]->portal["linguagem"] . "</i>]"; ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>E-Mail Administrador:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo $this->container["config"]->portal["email"]; ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>URL Principal:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo $this->container["config"]->portal["url"]; ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>Diretório Principal:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo $_SERVER['DOCUMENT_ROOT']; ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>Diretório de Blob:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo $this->container["config"]->portal["uploadpath"]; ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>Tipo de Banco de Dados:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo($this->container["config"]->bd["tipo"]); ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>Banco utilizado & Porta:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo $this->container["config"]->bd["host"] . ":" . $this->container["config"]->bd["porta"] . " [<i>" . $this->container["config"]->bd["nome"] . "</i>]"; ?></div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- === Final === Informações do Publicare === -->