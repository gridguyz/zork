<?php

namespace Zork\Form\Element;

use ArrayObject;
use Zend\Form\Fieldset;
use Zork\Stdlib\Hydrator\ArrayAccess;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Hash extends Fieldset
        implements InputProviderInterface,
                   TranslatorSettingsAwareInterface
{

    use InputProviderTrait;

    /**
     * @var string
     */
    protected $name = HashCollection::DEFAULT_TEMPLATE_PLACEHOLDER;

    /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     * @throws Exception\InvalidArgumentException
     */
    public function __construct( $name = null, $options = array() )
    {
        parent::__construct( $name ?: $this->name, $options );

        $this->setHydrator( new ArrayAccess )
             ->setObject( new ArrayObject );
    }

}
