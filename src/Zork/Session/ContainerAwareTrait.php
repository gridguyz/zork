<?php

namespace Zork\Session;

use Zend\Session\Container;
use Zend\Session\ManagerInterface;
use Zend\Session\SessionManager;

/**
 * SessionAwareTrait
 *
 * expects only a <code>getServiceLocator()</code>
 * method to be on <code>$this</code>
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait ContainerAwareTrait
{

    /**
     * @var \Zend\Session\ManagerInterface
     */
    protected $sessionManager;

    /**
     * @var \Zend\Session\Container[]
     */
    protected $sessionContainers = array();

    /**
     * Set the session manager
     *
     * @param  \Zend\Session\ManagerInterface $manager
     * @return \Zork\Mvc\Controller\Plugin\Messenger
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
     * @return \Zend\Session\ManagerInterface
     */
    public function getSessionManager()
    {
        if ( ! $this->sessionManager instanceof ManagerInterface )
        {
            $this->setSessionManager( new SessionManager() );
        }

        return $this->sessionManager;
    }

    /**
     * Get a session container
     *
     * @return \Zend\Session\Container
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
