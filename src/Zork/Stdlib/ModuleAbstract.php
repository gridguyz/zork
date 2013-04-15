<?php

namespace Zork\Stdlib;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * ModuleAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class ModuleAbstract
    implements ConfigProviderInterface
{

    /**
     * @const string
     */
    const BASE_DIR = '';

    /**
     * Config-file's path in static::BASE_DIR
     *
     * @const string
     */
    const CONFIG_FILE = 'config/module.config.php';

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return static::CONFIG_FILE
            ? include static::BASE_DIR . '/' . static::CONFIG_FILE
            : array();
    }

}
