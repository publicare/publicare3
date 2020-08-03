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
global $_page, $PORTAL_EMAIL, $_DBSERVERTYPE, $_DBHOST, $_DB;
?>
<!-- === Menu === -->
<ul class="nav nav-tabs">
          <li class="active"><a href="do/indexportal/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Informações do Publicare</a></li>
          <li><a href="do/gerusuario/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Gerenciar usuários</a></li>
          <li><a href="do/classes/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Gerenciar classes</a></li>
          <li><a href="do/peles/<?php echo($_page->_objeto->Valor('cod_objeto')) ?>.html">Gerenciar Peles</a></li>
</ul>
<!-- === FInal === Menu === -->

<!-- === Informações do Publicare === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Informações do Publicare</b></h3></div>
    <div class="panel-body">
        
        <div id="list-conter-classe"> 
            <ul>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>Nome do Site:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo $_page->config["portal"]["nome"] . " [<i>" . $_page->config["portal"]["linguagem"] . "</i>]"; ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>E-Mail Administrador:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo $PORTAL_EMAIL; ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>URL Principal:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo $_page->config["portal"]["url"]; ?></div>
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
                        <div class="col-md-9 col-sm-8"><?php echo $_page->config["portal"]["uploadpath"]; ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>Tipo de Banco de Dados:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo($_page->_db->config["bd"]["tipo"]); ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>Banco utilizado & Porta:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo $_page->_db->config["bd"]["host"] . ":" . $_page->_db->config["bd"]["porta"] . " [<i>" . $_page->_db->config["bd"]["nome"] . "</i>]"; ?></div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- === Final === Informações do Publicare === -->