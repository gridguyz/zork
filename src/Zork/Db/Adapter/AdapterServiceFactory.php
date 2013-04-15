<?php

namespace Zork\Db\Adapter;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\Db\Adapter\AdapterServiceFactory as ZendAdapterServiceFactory;

/**
 * AdapterServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdapterServiceFactory extends ZendAdapterServiceFactory
{

    /**
     * Get db config from a ServiceLocatorInterface
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return array
     */
    protected function getConfig( ServiceLocatorInterface $serviceLocator )
    {
        try
        {
            $config = $serviceLocator->get( 'Configuration' );
        }
        catch ( ServiceNotFoundException $ex )
        {
            $config = $serviceLocator->get( 'ApplicationConfig' );
        }

        if ( empty( $config['db'] ) )
        {
            return array();
        }

        return $config['db'];
    }

    /**
     * Create db adapter service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Db\Adapter\Adapter
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        $config = $this->getConfig( $serviceLocator );
        return $serviceLocator->get( 'Zork\Db\SiteConfigurationInterface' )
                              ->configure( new Adapter( $config ) );
    }

}
