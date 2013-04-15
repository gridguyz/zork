<?php

namespace Zork\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Builder service-factory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class BuilderServiceFactory implements FactoryInterface
{

    /**
     * Create the locale-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Factory\Builder
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the locale
        $config     = $serviceLocator->get( 'Configuration' );
        $srvConfig  = isset( $config['factory'] ) ? $config['factory'] : array();
        return Builder::factory( $srvConfig );
    }

}
