<?php

namespace Zork\Form\Element;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * SelectModel element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RadioModel extends Radio
              implements ServiceLocatorAwareInterface
{

    use ModelOptionsTrait;

}
