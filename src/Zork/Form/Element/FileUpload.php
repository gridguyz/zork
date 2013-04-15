<?php

namespace Zork\Form\Element;

/**
 * File uploader form element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FileUpload extends Text
{

    /**
     * @var string
     */
    const TYPES_ANY         = '';

    /**
     * @var string
     */
    const TYPES_IMAGE       = 'image/*';

    /**
     * @var string
     */
    const DEFAULT_TYPE      = self::TYPES_ANY;

    /**
     * @var string
     */
    const DEFAULT_PATTERN   = '~%s.%s';

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type'                      => 'text',
        'readonly'                  => true,
        'data-js-type'              => 'js.form.element.fileUpload',
        'data-js-upload-types'      => self::DEFAULT_TYPE,
        'data-js-upload-pattern'    => self::DEFAULT_PATTERN,
    );

    /**
     * Set file-name pattern
     *
     * @param string $pattern
     * @return \Zork\Form\Element\FileUpload
     */
    public function setPattern( $pattern )
    {
        return $this->setAttribute(
            'data-js-upload-pattern',
            (string) $pattern
        );
    }

    /**
     * Set enabled mime-types in listing
     *
     * @param string $types
     * @return \Zork\Form\Element\FileUpload
     */
    public function setTypes( $types )
    {
        return $this->setAttribute(
            'data-js-upload-types',
            (string) $types
        );
    }

    /**
     * Get starting directory
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->getAttribute( 'data-js-upload-pattern' );
    }

    /**
     * Get enabled mime-types in listing
     *
     * @return string
     */
    public function getTypes()
    {
        return $this->getAttribute( 'data-js-upload-types' );
    }

    /**
     * Set options for an element. Accepted options are:
     * - label: label to associate with the element
     * - label_attributes: attributes to use when the label is rendered
     * - required: set required attribute & auto-insert the validator
     * - filters: set additional filters
     * - validators: set additional validators
     *
     * @param  array|\Traversable $options
     * @return \Zork\Form\Element
     * @throws \Zend\Form\Exception\InvalidArgumentException
     */
    public function setOptions( $options )
    {
        parent::setOptions( $options );

        if ( isset( $this->options['types'] ) )
        {
            $this->setTypes( $this->options['types'] );
        }

        if ( isset( $this->options['pattern'] ) )
        {
            $this->setPattern( $this->options['pattern'] );
        }
    }

}
