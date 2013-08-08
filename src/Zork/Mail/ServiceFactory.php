<?php

namespace Zork\Mail;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * ServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ServiceFactory implements FactoryInterface
{

    /**
     * Create the mail-service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Mail\Service
     */
    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        // Configure the mail
        $config = $serviceLocator->get( 'Configuration' );
        $mail   = isset( $config['mail'] ) ? $config['mail'] : array();

        try
        {
            $domain = $serviceLocator->get( 'SiteInfo' )
                                     ->getDomain();
        }
        catch ( ServiceNotFoundException $ex )
        {
            switch ( true )
            {
                case isset( $_SERVER['HTTP_HOST'] ):
                    $domain = $_SERVER['HTTP_HOST'];
                    break;

                case isset( $_SERVER['SERVER_NAME'] ):
                    $domain = $_SERVER['SERVER_NAME'];
                    break;

                default:
                    $domain = 'localhost';
                    break;
            }
        }

        if ( empty( $mail['defaultFrom']['email'] ) )
        {
            $mail['defaultFrom']['email'] = 'no-reply@' . $domain;
        }

        if ( empty( $mail['defaultFrom']['name'] ) )
        {
            $mail['defaultFrom']['name'] = $domain;
        }

        if ( empty( $mail['defaultReplyTo']['email'] ) )
        {
            $mail['defaultReplyTo']['email'] = 'no-reply@' . $domain;
        }

        if ( empty( $mail['defaultReplyTo']['name'] ) )
        {
            $mail['defaultReplyTo']['name'] = $domain;
        }

        return Service::factory( $mail );
    }

}
