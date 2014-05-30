<?php

namespace Zork\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormCollection as BaseHelper;

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
    protected $defaultElementHelper = 'formFieldsetElement';

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
                    ->render( $element );
    }

    /**
     * Render a collection by iterating through all fieldsets and elements
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render( ElementInterface $element )
    {
        return sprintf(
            '<div %s>%s</div>',
            $this->createAttributesString( $element->getAttributes() ),
            parent::render( $element )
        );
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

        return $this->getElementHelper();
    }

}
