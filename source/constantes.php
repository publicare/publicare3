<?php
/**
* Publicare - O CMS Público Brasileiro
* @description constantes.php - Contém definições de constantes da aplicação
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

/**
 * Constantes para funcionalidades de envio de emails
 */
define("EmailTextCharset", "utf-8");
define("EmailHtmlCharset", "us-ascii");
define("EmailNewLine", "\r\n");

/**
 * Constantes para operações com objetos
 */
define('_OPERACAO_OBJETO_MOVER', 5);
define('_OPERACAO_OBJETO_RECUPERAR', 4);
define('_OPERACAO_OBJETO_REMOVER', 3);
define('_OPERACAO_OBJETO_EDITAR', 2);
define('_OPERACAO_OBJETO_CRIAR', 1);

/**
 * Constantes status dos objetos
 */
define('_STATUS_PRIVADO', 1);
define('_STATUS_PUBLICADO', 2);
define('_STATUS_REJEITADO', 3); 
define('_STATUS_SUBMETIDO', 4);

/**
 * Constantes perfis de usuários
 */
define('_PERFIL_ADMINISTRADOR', 1);
define('_PERFIL_EDITOR', 2);
define('_PERFIL_AUTOR', 3);
define('_PERFIL_RESTRITO', 4);
define('_PERFIL_MILITARIZADO', 5);
define('_PERFIL_DEFAULT', 6);


//define('ADODB_ASSOC_CASE', 0);

/**
 * Versao
 */
define('_VERSIONPROG','Publicare 3.5.1 - 19/08/2020');