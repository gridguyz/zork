<?php

namespace Zork\I18n\Timezone;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TimezoneServiceFactory implements FactoryInterface
{

    /**
     * Create the timezone-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\I18n\Locale\Locale
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the locale
        $config     = $serviceLocator->get( 'Configuration' );
        $srvConfig  = isset( $config['timezone'] ) ? $config['timezone'] : array();
        return Timezone::factory( $srvConfig );
    }

}
