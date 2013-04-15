<?php

namespace Zork\Form\Element;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * MultiCheckboxGroupModel element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MultiCheckboxGroupModel extends MultiCheckboxGroup
                           implements ServiceLocatorAwareInterface
{

    use ModelOptionsTrait;

}
