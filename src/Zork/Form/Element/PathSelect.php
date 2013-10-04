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
    const DEFAULT_BUTTON = 'default.browse';

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
     * File flag
     *
     * @var bool
     */
    protected $file = true;

    /**
     * File flag
     *
     * @var bool
     */
    protected $click = true;

    /**
     * Button flag
     *
     * @var bool
     */
    protected $button = true;

    /**
     * Button text
     *
     * @var string
     */
    protected $buttonText = self::DEFAULT_BUTTON;

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
     * Get file flag
     *
     * @return bool
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get button flag
     *
     * @return bool
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     * Get button text
     *
     * @return string
     */
    public function getButtonText()
    {
        return $this->buttonText;
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
                'data-js-pathselect-file',
                null
            );
        }

        return $this->setAttribute(
            'data-js-pathselect-file',
            ( $this->file = (bool) $flag ) ? 'true' : 'false'
        );
    }

    /**
     * Set file flag
     *
     * @param bool $flag
     * @return \Zork\Form\Element\PathSelect
     */
    public function setClick( $flag )
    {
        if ( null === $flag )
        {
            $this->click = true;

            return $this->setAttribute(
                'data-js-pathselect-click',
                null
            );
        }

        return $this->setAttribute(
            'data-js-pathselect-click',
            ( $this->click = (bool) $flag ) ? 'true' : 'false'
        );
    }

    /**
     * Set file flag
     *
     * @param bool $flag
     * @return \Zork\Form\Element\PathSelect
     */
    public function setButton( $flag )
    {
        if ( null === $flag )
        {
            $this->button = true;

            return $this->setAttribute(
                'data-js-pathselect-button',
                null
            );
        }

        return $this->setAttribute(
            'data-js-pathselect-button',
            ( $this->button = (bool) $flag ) ? 'true' : 'false'
        );
    }

    /**
     * Set button text
     *
     * @param string $buttonText
     * @return \Zork\Form\Element\PathSelect
     */
    public function setButtonText( $buttonText = self::DEFAULT_BUTTON )
    {
        return $this->setAttribute(
            'data-js-pathselect-button-text',
            $this->buttonText = ( (string) $buttonText ) ?: null
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

        if ( isset( $this->options['pathselect_file'] ) )
        {
            $this->setFile( $this->options['pathselect_file'] );
        }

        if ( isset( $this->options['pathselect_click'] ) )
        {
            $this->setClick( $this->options['pathselect_click'] );
        }

        if ( isset( $this->options['pathselect_button'] ) )
        {
            $this->setButton( $this->options['pathselect_button'] );
        }

        if ( isset( $this->options['pathselect_buttontext'] ) )
        {
            $this->setButtonText( $this->options['pathselect_buttontext'] );
        }
    }

}
