<?php

namespace Zork\Mvc\View\Http;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * InjectHeadDefaultsServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class InjectHeadDefaultsServiceFactory implements FactoryInterface
{

    /**
     * Create the InjectHeadDefaults-service
     *
     * @param   \Zend\ServiceManager\ServiceLocatorInterface    $serviceLocator
     * @return  \Zork\Mvc\View\Http\InjectHeadDefaults
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        $config     = $serviceLocator->get( 'Configuration' );
        $srvConfig  = isset( $config['view_manager']['head_defaults'] )
                    ? $config['view_manager']['head_defaults']
                    : array();

        return new InjectHeadDefaults( $srvConfig );
    }

}
