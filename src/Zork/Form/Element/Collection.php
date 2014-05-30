<?php

namespace Zork\Form\Element;

use Zend\Form\Element\Collection as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Collection extends ElementBase
              implements InputProviderInterface,
                         TranslatorSettingsAwareInterface
{

    use InputProviderTrait;

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'data-js-type'                  => 'js.form.element.collection',
        'data-js-collection-sortable'   => 'true',
    );

    /**
     * Initial count of target element
     *
     * @var int
     */
    protected $count = 0;

}
