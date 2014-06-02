<?php

namespace Zork\Form\Element;

use Zend\Form\FieldsetInterface;
use Zork\Stdlib\Hydrator\Traversable;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;
use Zend\Form\Element\Collection as ElementBase;

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

    /**
     * Set the target element
     *
     * @param   ElementInterface|array|\Traversable $elementOrFieldset
     * @return  Collection
     * @throws  \Zend\Form\Exception\InvalidArgumentException
     */
    public function setTargetElement( $elementOrFieldset )
    {
        parent::setTargetElement( $elementOrFieldset );

        if ( $this->targetElement instanceof FieldsetInterface )
        {
            $this->targetElement
                 ->setHydrator( new Traversable );
        }

        return $this;
    }

}
