<?php

namespace Zork\Mvc\View\Http;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ForbiddenStrategyServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ForbiddenStrategyServiceFactory implements FactoryInterface
{

    /**
     * Create the ForbiddenStrategy-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Mvc\View\Http\ForbiddenStrategy
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the admin-locale
        $config     = $serviceLocator->get( 'Configuration' );
        $strategy   = new ForbiddenStrategy();

        if ( ! empty( $config['view_manager']['forbidden_template'] ) )
        {
            $strategy->setForbiddenTemplate(
                $config['view_manager']['forbidden_template']
            );
        }

        return $strategy;
    }

}
