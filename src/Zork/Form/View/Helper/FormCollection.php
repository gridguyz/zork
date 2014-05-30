<?php

namespace Zork\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\FieldsetInterface;
use Zend\Form\Element\Collection;
use Zend\Form\View\Helper\FormCollection as BaseHelper;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;

/**
 * FormCollection
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormCollection extends BaseHelper
{

    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @var bool
     */
    protected $shouldWrap = false;

    /**
     * The name of the default view helper that is used to render sub elements.
     *
     * @var string
     */
    protected $defaultElementHelper = 'formElement';

    /**
     * The name of the default view helper that is used to render sub elements.
     *
     * @var string
     */
    protected $defaultFieldsetHelper = 'formFieldset';

    /**
     * Sets the name of the view helper that should be used to render fieldsets.
     *
     * @param  string $defaultSubHelper The name of the view helper to set.
     * @return FormCollection
     */
    public function setDefaultFieldsetHelper($defaultSubHelper)
    {
        $this->defaultFieldsetHelper = $defaultSubHelper;
        return $this;
    }

    /**
     * Gets the name of the view helper that should be used to render fieldsets.
     *
     * @return string
     */
    public function getDefaultFieldsetHelper()
    {
        return $this->defaultFieldsetHelper;
    }

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @param  bool                  $wrap
     * @return string|FormCollection
     */
    public function __invoke( ElementInterface $element = null, $wrap = false )
    {
        if ( ! $element )
        {
            return $this;
        }

        return $this->setShouldWrap( $wrap )
                    ->render( $element, true );
    }

    /**
     * Render a collection by iterating through all fieldsets and elements
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render( ElementInterface $element, $wrap = false )
    {
        if ( ! $element instanceof Collection )
        {
            $helper = ( $element instanceof FieldsetInterface )
                    ? $this->getFieldsetHelper()
                    : $this->getElementHelper();

            return $helper( $element );
        }

        $markup = parent::render( $element );

        if ( $wrap )
        {
            $attrs  = $this->createAttributesString( $element->getAttributes() );
            $markup = sprintf(
                '<div%s>%s</div>',
                $attrs ? ' ' . $attrs : '',
                $markup
            );
        }

        return $markup;
    }

    /**
     * Retrieve the fieldset helper.
     *
     * @return FormFieldsetElement
     */
    protected function getFieldsetHelper()
    {
        if ( $this->fieldsetHelper )
        {
            return $this->fieldsetHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->fieldsetHelper = $this->view->plugin(
                $this->getDefaultFieldsetHelper()
            );
        }

        if ( ! $this->fieldsetHelper instanceof BaseAbstractHelper )
        {
            // @todo Ideally the helper should implement an interface.
            throw new RuntimeException(
                'Invalid element helper set in FormCollection. ' .
                'The helper must be an instance of AbstractHelper.'
            );
        }

        return $this->fieldsetHelper;
    }

}
