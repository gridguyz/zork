<?php

namespace Zork\Mvc\Service;

use Zend\Mvc\Service\ServiceListenerFactory as ZendServiceListenerFactory;

/**
 * ServiceListenerFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ServiceListenerFactory extends ZendServiceListenerFactory
{

    /**
     * Replaces the default DependencyInjector factory
     */
    public function __construct()
    {
        $this->defaultServiceConfig['factories']['DependencyInjector'] =
                __NAMESPACE__ . '\\DiFactory';
    }

}
