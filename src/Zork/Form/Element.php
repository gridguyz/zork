<?php

namespace Zork\Form;

use Zend\Form\Element as ZendElement;
use Zend\InputFilter\InputProviderInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Element extends ZendElement
           implements InputProviderInterface
{

    use Element\InputProviderTrait;

}
