<?php

namespace Zork\Authentication;

use Zend\Authentication\AuthenticationService;

/**
 * AuthenticationServiceAwareTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait AuthenticationServiceAwareTrait
{

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @return  AuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    /**
     * @param   AuthenticationService   $auth
     * @return  AuthenticationServiceAwareTrait
     */
    public function setAuthenticationService( AuthenticationService $auth )
    {
        $this->authenticationService = $auth;
        return $this;
    }

}
