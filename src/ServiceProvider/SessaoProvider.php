<?php
namespace Pbl\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Pbl\Core\Sessao\SessaoManager;
use Pbl\Core\Sessao\FlashMessage;

/**
 * Sessao Provider
 *
 * @package Pbl\ServiceProvider
 */
class SessaoProvider implements ServiceProviderInterface
{
    /**
     * Register providers
     *
     * @access public
     * @param  \Pimple\Container $container
     * @return \Pimple\Container
     */
    public function register(Container $container)
    {
        $container['sessaoManager'] = function($c) {
            return new SessaoManager($c);
        };

        $container['flash'] = function($c) {
            return new FlashMessage($c);
        };

        return $container;
    }
}
