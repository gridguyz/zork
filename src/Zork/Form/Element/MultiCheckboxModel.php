<?php

namespace Zork\Form\Element;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * MultiCheckboxModel element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MultiCheckboxModel extends MultiCheckbox
                      implements ServiceLocatorAwareInterface
{

    use ModelOptionsTrait;

}
