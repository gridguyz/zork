<?php

namespace Zork\Authentication;

use Zend\Authentication\Storage\Session;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * AuthenticationServiceFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @deprecated use Grid\User\Authentication\AuthenticationServiceFactory instead
 */
class AuthenticationServiceFactory implements FactoryInterface
{

    /**
     * Create authentication service
     *
     * @param   ServiceLocatorInterface $services
     * @return  AuthenticationService
     */
    public function createService( ServiceLocatorInterface $services )
    {
        return new AuthenticationService( new Session(
            null,
            null,
            $services->get( 'Zend\Session\ManagerInterface' )
        ) );
    }

}
