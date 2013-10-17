<?php

namespace Zork\Session;

use Zend\Session\Container;
use Zend\Session\ManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * SessionAwareTrait
 *
 * Classes should implement
 * <code>Zend\ServiceManager\ServiceLocatorAwareInterface</code>
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait ContainerAwareTrait
{

    /**
     * @var ManagerInterface
     */
    protected $sessionManager;

    /**
     * @var Container[]
     */
    protected $sessionContainers = array();

    /**
     * Set the session manager
     *
     * @param   ManagerInterface    $manager
     * @return  ContainerAwareTrait
     */
    public function setSessionManager( ManagerInterface $manager )
    {
        if ( $this->sessionManager !== $manager )
        {
            $this->sessionManager    = $manager;
            $this->sessionContainers = array();
        }

        return $this;
    }

    /**
     * Retrieve the session manager
     *
     * If none composed, lazy-loads a SessionManager instance
     *
     * @return  ManagerInterface
     */
    public function getSessionManager()
    {
        if ( ! $this->sessionManager instanceof ManagerInterface &&
               $this instanceof ServiceLocatorAwareInterface )
        {
            $this->setSessionManager(
                $this->getServiceLocator()
                     ->get( 'Zend\Session\ManagerInterface' )
            );
        }

        return $this->sessionManager;
    }

    /**
     * Get a session container
     *
     * @return  Container
     */
    protected function getSessionContainer( $name = null )
    {
        if ( null === $name )
        {
            $name = get_called_class();
        }

        if ( ! isset( $this->sessionContainers[$name] ) )
        {
            $this->sessionContainers[$name] = new Container(
                $name, $this->getSessionManager()
            );
        }

        return $this->sessionContainers[$name];
    }

}
