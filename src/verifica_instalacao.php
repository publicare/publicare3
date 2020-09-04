<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @file verifica_instalacao.php
 * @description Verifica requisitos minimos para fucionamento
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

// PHP 5.5 minimo
if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    throw new Exception('O Publicare precisa do PHP na versão >= 5.5');
}

// Verificando extensões
foreach (array('gd', 'mbstring', 'json', 'hash', 'session', 'dom', 'filter', 'SimpleXML', 'xml') as $ext) {
    if (! extension_loaded($ext)) {
        throw new Exception('É necessária a extensão PHP: "'.$ext.'"');
    }
}

// Fix wrong value for arg_separator.output, used by the function http_build_query()
if (ini_get('arg_separator.output') === '&amp;') {
    ini_set('arg_separator.output', '&');
}

// Make sure we can read files with "\r", "\r\n" and "\n"
if (ini_get('auto_detect_line_endings') != 1) {
    ini_set("auto_detect_line_endings", 1);
}
