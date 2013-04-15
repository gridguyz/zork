<?php

namespace Zork\Cache;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Cache
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CacheServiceFactory implements FactoryInterface
{

    /**
     * Create the locale-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Cache\CacheManager
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the locale
        $config     = $serviceLocator->get( 'Configuration' );
        $srvConfig  = isset( $config['cache'] ) ? $config['cache'] : array();
        return CacheManager::factory( $srvConfig );
    }

}
