<?php

namespace Zork\Log;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerServiceFactory implements FactoryInterface
{

    /**
     * Create the locale-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Log\LogManager
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        return LoggerManager::factory( $serviceLocator );
    }

}
