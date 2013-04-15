<?php

namespace Zork\ServiceManager;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ServiceLocatorTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @implements \Zend\ServiceManager\ServiceLocatorAwareInterface
 */
trait ServiceLocatorAwareTrait
{

    protected $serviceLocator;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

}
