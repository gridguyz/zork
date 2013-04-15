<?php

namespace Zork\Form\Element;

use Zend\Form\Element\Checkbox as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Checkbox extends ElementBase
            implements InputProviderInterface,
                       TranslatorSettingsAwareInterface
{

    use InputProviderTrait;

    /**
     * @var string
     */
    protected $labelEnable = 'default.enable';

    /**
     * @var array
     */
    protected $labelEnableAttributes = array();

    /**
     * @return string
     */
    public function getLabelEnable()
    {
        return $this->labelEnable;
    }

    /**
     * @return array
     */
    public function getLabelEnableAttributes()
    {
        return $this->labelEnableAttributes;
    }

    /**
     * @param   string  $label
     * @return  \Zork\Form\Element\Checkbox
     */
    public function setLabelEnable( $label )
    {
        $this->labelEnable = ( (string) $label ) ?: null;
        return $this;
    }

    /**
     * @param   array   $attributes
     * @return  \Zork\Form\Element\Checkbox
     */
    public function setLabelEnableAttributes( array $attributes )
    {
        $this->labelEnableAttributes = $attributes;
        return $this;
    }

    /**
     * Set options for an element. Accepted options are:
     * - label: label to associate with the element
     * - label_attributes: attributes to use when the label is rendered
     * - label_enable: label-enable to associate with the element
     * - label_enable_attributes: attributes to use when the label-enable is rendered
     *
     * @param  array|\Traversable $options
     * @return Checkbox|ElementInterface
     * @throws InvalidArgumentException
     */
    public function setOptions( $options )
    {
        parent::setOptions( $options );

        if ( isset( $this->options['label_enable'] ) )
        {
            $this->setLabelEnable( $this->options['label_enable'] );
        }

        if ( isset( $this->options['label_enable_attributes'] ) )
        {
            $this->setLabelEnableAttributes( (array) $this->options['label_enable_attributes'] );
        }

        return $this;
    }

}
