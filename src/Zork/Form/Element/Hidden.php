<?php

namespace Zork\Form\Element;

use Zend\Form\Element\Hidden as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Hidden extends ElementBase
          implements InputProviderInterface,
                     TranslatorSettingsAwareInterface
{

    use InputProviderTrait;

}
