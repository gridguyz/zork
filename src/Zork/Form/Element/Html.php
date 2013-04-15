<?php

namespace Zork\Form\Element;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Html extends Textarea
{

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type'          => 'textarea',
        'data-js-type'  => 'js.form.element.html',
    );

    /**
     * Theme
     *
     * @var string
     */
    protected $theme;

    /**
     * Button-set
     *
     * @var string
     */
    protected $buttonSet;

    /**
     * Get theme
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set theme
     *
     * @param string $theme
     * @return \Zork\Form\Element\Html
     */
    public function setTheme( $theme )
    {
        $this->theme = ( (string) $theme ) ?: null;
        return $this->setAttribute( 'data-js-html-theme', $this->theme )
                    ->setAttribute( 'data-js-html-plugins', $this->theme );
    }

    /**
     * Get button-set
     *
     * @return string
     */
    public function getButtonSet()
    {
        return $this->buttonSet;
    }

    /**
     * Set button-set
     *
     * @param string $set
     * @return \Zork\Form\Element\Html
     */
    public function setButtonSet( $set )
    {
        return $this->setAttribute(
            'html-button-set',
            $this->theme = ( (string) $set ) ?: null
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

        if ( isset( $this->options['html_theme'] ) )
        {
            $this->setTheme( $this->options['html_theme'] );
        }

        if ( isset( $this->options['html_buttonset'] ) )
        {
            $this->setButtonSet( $this->options['html_buttonset'] );
        }
    }

}
