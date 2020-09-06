<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo
 * @copyright MIT © 2020
 * @package Pbl/SeviceManager
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

namespace Pbl\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Pbl\Core\Administracao\Administracao;
use Pbl\Core\AdminObjeto\AdminObjeto;
use Pbl\Core\Blob\Blob;
use Pbl\Core\Log\Log;
use Pbl\Core\Objeto\Objeto;
use Pbl\Core\Pagina\Pagina;
use Pbl\Core\Parse\Parse;
use Pbl\Core\Usuario\Usuario;

class GeralProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['administracao'] = function ($container) {
            return new Administracao($container);
        };
        
        $container['adminobjeto'] = function ($container) {
            return new AdminObjeto($container);
        };

        $container['blob'] = function ($container) {
            return new Blob($container);
        };

        $container['log'] = function ($container) {
            return new Log($container);
        };
        
        $container['objeto'] = function ($container) {
            return new Objeto($container);
        };

        $container['page'] = function ($container) {
            return new Pagina($container);
        };

        $container['parse'] = function ($container) {
            return new Parse($container);
        };

        $container['usuario'] = function ($container) {
            return new Usuario($container);
        };

        return $container;
    }
}