<?php

namespace Zork\ServiceManager;

use Zend\ServiceManager\ServiceManager as Base;

/**
 * ServiceManager
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ServiceManager extends Base
{

    /**
     * Unregister all service instances
     *
     * @param   \Zend\ServiceManager\ServiceManager $serviceManager
     * @return  \Zend\ServiceManager\ServiceManager
     */
    protected static function unregisterServices( Base $serviceManager )
    {
        foreach ( $serviceManager->instances as &$instance )
        {
            $instance = null;
        }

        $serviceManager->instances = array();
        return $serviceManager;
    }

}
