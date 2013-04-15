<?php

namespace Zork\Form\Element;

use Zend\Form\Element\MultiCheckbox as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MultiCheckbox extends ElementBase
                 implements InputProviderInterface,
                            TranslatorSettingsAwareInterface
{

    use InputProviderTrait;

    /**
     * @var bool
     */
    protected $useHiddenElement = true;

}
