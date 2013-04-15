<?php

namespace Zork\Form\Element;

use Zend\Form\Element\Image as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Image extends ElementBase
         implements InputProviderInterface,
                    TranslatorSettingsAwareInterface
{

    use InputProviderTrait;

}
