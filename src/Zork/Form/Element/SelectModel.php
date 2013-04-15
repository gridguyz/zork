<?php

namespace Zork\Form\Element;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * SelectModel element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SelectModel extends Select
               implements ServiceLocatorAwareInterface
{

    use ModelOptionsTrait;

}
