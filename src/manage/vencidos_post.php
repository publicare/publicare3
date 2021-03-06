<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo vencidos_post.php é responsável pela execução de ações em objetos vencidos
 * @copyright MIT © 2020
 * @package publicare/manage
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

foreach($_POST['objlist'] as $obj)
{
    if ($_SESSION['usuario']['perfil']==_PERFIL_ADMINISTRADOR) $this->container["administracao"]->apagarEmDefinitivo($obj);
    elseif ($_SESSION['usuario']['perfil']==_PERFIL_EDITOR) $this->container["administracao"]->apagarObjeto($obj); 
}
	
header("Location:" . $this->container["config"]->portal["url"] . "/do/vencidos/".$this->container["objeto"]->valor('cod_objeto').'.html');

