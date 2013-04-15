<?php

namespace Zork\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Form
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormServiceFactory implements FactoryInterface
{

    /**
     * Create the form-service
     *
     * @param   \Zend\ServiceManager\ServiceLocatorInterface    $serviceLocator
     * @return  \Zork\I18n\Locale\Locale
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the locale
        $config     = $serviceLocator->get( 'Configuration' );
        $srvConfig  = isset( $config['form'] ) ? $config['form'] : array();
        $factory    = $serviceLocator->get( 'Zork\Form\Factory' );
        $factory->setServiceLocator( $serviceLocator );
        return new FormService( $factory, $srvConfig );
    }

}
