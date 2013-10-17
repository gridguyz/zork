<?php

namespace Zork\Mvc;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * AdminLocaleServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdminLocaleServiceFactory implements FactoryInterface
{

    /**
     * Create the locale-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Mvc\AdminLocale
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the admin-locale
        $request = $serviceLocator->get( 'Request' );
        $manager = $serviceLocator->get( 'Zend\Session\ManagerInterface' );
        $locale  = $request->getQuery( AdminLocale::SESSION_KEY, null );
        return new AdminLocale( $locale, $manager );
    }

}
