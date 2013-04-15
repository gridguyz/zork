<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class PathSelect extends Text
{

    /**
     * @var string
     */
    const DEFAULT_BUTTON = 'default.select';

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type'          => 'text',
        'data-js-type'  => 'js.form.element.pathselect',
    );

    /**
     * Strating directory
     *
     * @var string
     */
    protected $directory;

    /**
     * Button label
     *
     * @var string
     */
    protected $button;

    /**
     * File flag
     *
     * @var bool
     */
    protected $file = true;

    /**
     * Get starting directory
     *
     * @return string|null
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Get button label
     *
     * @return string|null
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     * Get file flag
     *
     * @return bool
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set starting directory
     *
     * @param string $root
     * @return \Zork\Form\Element\PathSelect
     */
    public function setDirectory( $directory )
    {
        return $this->setAttribute(
            'data-js-pathselect-directory',
            $this->directory = ( (string) $directory ) ?: null
        );
    }

    /**
     * Set button label
     *
     * @param string $button
     * @return \Zork\Form\Element\PathSelect
     */
    public function setButton( $button = self::DEFAULT_BUTTON )
    {
        return $this->setAttribute(
            'data-js-pathselect-button',
            $this->button = ( (string) $button ) ?: null
        );
    }

    /**
     * Set file flag
     *
     * @param bool $flag
     * @return \Zork\Form\Element\PathSelect
     */
    public function setFile( $flag )
    {
        if ( null === $flag )
        {
            $this->file = true;

            return $this->setAttribute(
                'data-js-pathselect-button',
                null
            );
        }

        return $this->setAttribute(
            'data-js-pathselect-button',
            ( $this->file = (bool) $flag ) ? 'true' : 'false'
        );
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

        if ( isset( $this->options['pathselect_directory'] ) )
        {
            $this->setDirectory( $this->options['pathselect_directory'] );
        }

        if ( isset( $this->options['pathselect_button'] ) )
        {
            $this->setButton( $this->options['pathselect_button'] );
        }

        if ( isset( $this->options['pathselect_file'] ) )
        {
            $this->setFile( $this->options['pathselect_file'] );
        }
    }

}
