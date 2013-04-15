<?php

namespace Zork\I18n\Locale;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class LocaleServiceFactory implements FactoryInterface
{

    /**
     * Create the locale-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\I18n\Locale\Locale
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the locale
        $config     = $serviceLocator->get( 'Configuration' );
        $srvConfig  = isset( $config['locale'] ) ? $config['locale'] : array();
        return Locale::factory( $srvConfig );
    }

}
