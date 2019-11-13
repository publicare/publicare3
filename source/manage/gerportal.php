<?php
global $_page, $PORTAL_EMAIL, $_DBSERVERTYPE, $_DBHOST, $_DB;
?>
<!-- === Menu === -->
<ul class="nav nav-tabs">
          <li class="active"><a href="do/indexportal/<?php echo($_page->_objeto->Valor($_page, 'cod_objeto')) ?>.html">Informações do Publicare</a></li>
          <li><a href="do/gerusuario/<?php echo($_page->_objeto->Valor($_page, 'cod_objeto')) ?>.html">Gerenciar usuários</a></li>
          <li><a href="do/classes/<?php echo($_page->_objeto->Valor($_page, 'cod_objeto')) ?>.html">Gerenciar classes</a></li>
          <li><a href="do/peles/<?php echo($_page->_objeto->Valor($_page, 'cod_objeto')) ?>.html">Gerenciar Peles</a></li>
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
                        <div class="col-md-9 col-sm-8"><?php echo _PORTAL_NAME . " [<i>" . _LANGUAGE . "</i>]"; ?></div>
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
                        <div class="col-md-9 col-sm-8"><?php echo _URL; ?></div>
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
                        <div class="col-md-9 col-sm-8"><?php echo _BLOBDIR; ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>Tipo de Banco de Dados:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php
                        if (_DBSERVERTYPE == "odbc_mssql")
                            echo "Microsoft SQL";
                        elseif (_DBSERVERTYPE == "mysql")
                            echo "MySQL";
                        else
                            echo "PostgreSQL";
                        ?></div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-3 col-sm-4"><strong>Banco utilizado & Porta:</strong></div>
                        <div class="col-md-9 col-sm-8"><?php echo _DBHOST . ":" . _DBPORT . " [<i>" . _DBNOME . "</i>]"; ?></div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- === Final === Informações do Publicare === -->