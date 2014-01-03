<?php

namespace Zork\Session\ContainerAwareTraitTest;

use Zork\Session\ContainerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class ContainerAwareSample implements ServiceLocatorAwareInterface
{

    use ContainerAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * @param   string|null $name
     * @return  \Zend\Session\AbstractContainer
     */
    public function container( $name = null )
    {
        return $this->getSessionContainer( ( (string) $name ) ?: null );
    }

}
