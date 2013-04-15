<?php

namespace Zork\EventManager;

use Zend\EventManager\ProvidesEvents;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * EventProviderAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class EventProviderAbstract implements EventManagerAwareInterface
{

    use ProvidesEvents;

}
