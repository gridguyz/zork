<?php

namespace Zork\Log;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerServiceFactory implements FactoryInterface
{

    /**
     * Create the locale-service
     *
     * @param   \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return  \Zork\Log\LoggerManager
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        $config     = $serviceLocator->get( 'Configuration' );
        $srvConfig  = isset( $config['log'] ) ? $config['log'] : array();
        return new LoggerManager( $srvConfig, $serviceLocator );
    }

}
