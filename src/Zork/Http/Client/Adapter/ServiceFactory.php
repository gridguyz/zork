<?php

namespace Zork\Http\Client\Adapter;

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
     * @return \Zend\Http\Client\Adapter\AdapterInterface
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the adapter
        $config     = $serviceLocator->get( 'Configuration' );
        $class      = 'Zend\Http\Client\Adapter\Socket';
        $options    = array();

        if ( isset( $config['http']['adapter'] ) )
        {
            $class  = $config['http']['adapter'];
        }

        if ( isset( $config['http']['options'] ) )
        {
            $options = $config['http']['options'];
        }

        $result = new $class();
        $result->setOptions( $options );
        return $result;
    }

}
