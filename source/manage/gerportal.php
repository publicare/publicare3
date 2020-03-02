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