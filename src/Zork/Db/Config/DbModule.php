<?php

namespace Zork\Db\Config;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * DbModule
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DbModule implements ConfigProviderInterface
{

    /**
     * @var array
     */
    protected $config = array();

    /**
     * Construct
     *
     * @param array $config
     */
    public function __construct( array $config )
    {
        $this->config = $config;
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return $this->config;
    }

}
