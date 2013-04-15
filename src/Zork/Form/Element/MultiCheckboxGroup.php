<?php

namespace Zork\Form\Element;

use Zork\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Exception\InvalidArgumentException;
use Zend\Validator\InArray as InArrayValidator;
use Zend\Validator\Explode as ExplodeValidator;
use Zend\Validator\ValidatorInterface;
use Zork\Form\TranslatorSettingsAwareInterface;

/**
 * MultiCheckboxGroup
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MultiCheckboxGroup extends Element
                         implements TranslatorSettingsAwareInterface
{

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'multi_checkbox_group',
    );

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Create an empty option (option with label but no value). If set to null, no option is created
     *
     * @var bool
     */
    protected $emptyOption = null;

    /**
     * @var array
     */
    protected $valueOptions = array();

    /**
     * @return array
     */
    public function getValueOptions()
    {
        return $this->valueOptions;
    }

    /**
     * @param  array $options
     * @return RadioGroup
     */
    public function setValueOptions( array $options )
    {
        $this->valueOptions = $options;

        // Update InArrayValidator validator haystack
        if ( $this->validator instanceof InArrayValidator )
        {
            $this->validator
                 ->setHaystack( $this->getValueOptionsValues() );
        }

        return $this;
    }

    /**
     * Set options for an element. Accepted options are:
     * - label: label to associate with the element
     * - label_attributes: attributes to use when the label is rendered
     * - value_options: list of values and labels for the select options
     * - empty_option: should an empty option be prepended to the options ?
     *
     * @param  array|\Traversable $options
     * @return RadioGroup|ElementInterface
     * @throws InvalidArgumentException
     */
    public function setOptions($options)
    {
        parent::setOptions( $options );

        if ( isset( $this->options['value_options'] ) )
        {
            $this->setValueOptions( $this->options['value_options'] );
        }

        // Alias for 'value_options'
        if ( isset( $this->options['options'] ) )
        {
            $this->setValueOptions( $this->options['options'] );
        }

        if ( isset( $this->options['empty_option'] ) )
        {
            $this->setEmptyOption( $this->options['empty_option'] );
        }

        return $this;
    }

    /**
     * Set a single element attribute
     *
     * @param  string $key
     * @param  mixed  $value
     * @return RadioGroup|ElementInterface
     */
    public function setAttribute( $key, $value )
    {
        // Do not include the options in the list of attributes
        // TODO: Deprecate this
        if ( $key === 'options' )
        {
            $this->setValueOptions( $value );
            return $this;
        }

        return parent::setAttribute( $key, $value );
    }

    /**
     * Set the string for an empty option (can be empty string).
     * If set to null, no option will be added.
     *
     * @param  string|null $emptyOption
     * @return RadioGroup
     */
    public function setEmptyOption( $emptyOption )
    {
        $this->emptyOption = $emptyOption;
        return $this;
    }

    /**
     * Return the string for the empty option (null if none)
     *
     * @return string|null
     */
    public function getEmptyOption()
    {
        return $this->emptyOption;
    }

    /**
     * Get validator
     *
     * @return ValidatorInterface
     */
    protected function getValidator()
    {
        if ( null === $this->validator )
        {
            $inArrayValidator = new InArrayValidator( array(
                'haystack' => $this->getValueOptionsValues(),
                'strict'   => false,
            ) );
            $this->validator = new ExplodeValidator( array(
                'validator'      => $inArrayValidator,
                'valueDelimiter' => null, // skip explode if only one value
            ) );
        }

        return $this->validator;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches the InArray validator.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $spec = parent::getInputSpecification();
        $spec['validators'][] = $this->getValidator();
        return $spec;
    }

    /**
     * Get only the values from the options attribute
     *
     * @return array
     */
    protected function getValueOptionsValues()
    {
        $values  = array();
        $options = $this->getValueOptions();

        foreach ( $options as $key => $optionSpec )
        {
            if ( is_array( $optionSpec ) &&
                 array_key_exists( 'options', $optionSpec ) )
            {
                foreach ( $optionSpec['options'] as $nestedKey => $nestedOptionSpec )
                {
                    $values[] = $this->getOptionValue( $nestedKey, $nestedOptionSpec );
                }

                continue;
            }

            $values[] = $this->getOptionValue( $key, $optionSpec );
        }

        return $values;
    }

    /**
     * @param string $key
     * @param string|array $optionSpec
     * @return string
     */
    protected function getOptionValue( $key, $optionSpec )
    {
        return is_array( $optionSpec ) ? $optionSpec['value'] : $key;
    }

}
