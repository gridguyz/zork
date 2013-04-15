<?php

namespace Zork\Form\Element;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * SelectModel element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RadioGroupModel extends RadioGroup
                   implements ServiceLocatorAwareInterface
{

    use ModelOptionsTrait;

}
