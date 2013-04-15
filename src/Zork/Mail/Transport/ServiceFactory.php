<?php

namespace Zork\Mail\Transport;

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
     * Create the mail-transport-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Mail\Service
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the mail-transport
        $mail = $serviceLocator->get( 'Zork\Mail\Service' );
        return $mail->getTransport();
    }

}
