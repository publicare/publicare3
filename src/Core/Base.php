<?php
namespace Pbl\Core;

use Pimple\Container;

/**
 * Base Class
 *
 * @package Pbl\Core
 *
 * @property \JsonRPC\Server                                         $api
 */
abstract class Base
{
    /**
     * Container instance
     *
     * @access protected
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Carrega dependencias automaticamente
     *
     * @access public
     * @param  string $name Class name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container[$name];
    }

    /**
     * Pega instancia do objeto
     *
     * @static
     * @access public
     * @param  Container $container
     * @return static
     */
    public static function getInstance(Container $container)
    {
        return new static($container);
    }
}
