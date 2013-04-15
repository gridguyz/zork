<?php

namespace Zork\Http\Client;

use Zend\Http\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ServiceFactory implements FactoryInterface
{

    /**
     * Create the http-client-adapter-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zend\Http\Client
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the adapter
        $config     = $serviceLocator->get( 'Configuration' );
        $options    = array();

        if ( isset( $config['http']['options'] ) )
        {
            $options = $config['http']['options'];
        }

        $result = new Client( null, $options );
        $result->setAdapter( $serviceLocator->get(
            'Zend\Http\Client\Adapter\AdapterInterface'
        ) );

        return $result;
    }

}
