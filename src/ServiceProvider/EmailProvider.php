<?php
namespace Pbl\ServiceProvider;

use Pbl\Core\Email\Client as EmailClient;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Mail Provider
 *
 * @package Pbl\ServiceProvider
 */
class EmailProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['emailClient'] = function ($container) {
            $mailer = new EmailClient($container);
            $mailer->setTransport('smtp', '\Pbl\Core\Email\Transport\Smtp');
            $mailer->setTransport('sendmail', '\Pbl\Core\Email\Transport\Sendmail');
            $mailer->setTransport('mail', '\Pbl\Core\Email\Transport\Mail');
            return $mailer;
        };

        return $container;
    }
}
