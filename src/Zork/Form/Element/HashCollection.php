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
class HashCollection extends ElementBase
                  implements InputProviderInterface,
                             TranslatorSettingsAwareInterface
{

    use InputProviderTrait;

    /**
     * Default template placeholder
     */
    const DEFAULT_TEMPLATE_PLACEHOLDER = '__hash__';

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'hash_collection',
    );

    /**
     * Initial count of target element
     *
     * @var int
     */
    protected $count = 0;

    /**
     * Placeholder used in template content for
     * making your life easier with JavaScript
     *
     * @var string
     */
    protected $templatePlaceholder = self::DEFAULT_TEMPLATE_PLACEHOLDER;

}
