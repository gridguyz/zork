<?php

namespace Zork\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zork\Form\Element\Checkbox;
use Zend\Form\View\Helper\FormLabel;
use Zend\Form\View\Helper\FormCheckbox as ZendFormCheckbox;

/**
 * FormRadio
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormCheckbox extends ZendFormCheckbox
{

    /**
     * @var FormLabel
     */
    protected $labelHelper;

    /**
     * Retrieve the FormLabel helper
     *
     * @return FormLabel
     */
    protected function getLabelHelper()
    {
        if ( $this->labelHelper )
        {
            return $this->labelHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->labelHelper = $this->view->plugin( 'form_label' );
        }

        if ( ! $this->labelHelper instanceof FormLabel )
        {
            $this->labelHelper = new FormLabel();
        }

        if ( $this->hasTranslator() )
        {
            $this->labelHelper
                 ->setTranslator(
                        $this->getTranslator(),
                        $this->getTranslatorTextDomain()
                    );
        }

        return $this->labelHelper;
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     * @return string
     */
    public function render( ElementInterface $element )
    {
        $markup = parent::render( $element );

        if ( $element instanceof Checkbox &&
             ( $labelEnable = $element->getLabelEnable() ) )
        {
            if ( $this->hasTranslator() )
            {
                $labelEnable = $this->getTranslator()
                                    ->translate(
                                        $labelEnable,
                                        strstr( $labelEnable, '.', true )
                                    );
            }

            $helper = $this->getLabelHelper();
            $markup = $helper->openTag( $element->getLabelEnableAttributes() )
                    . $markup . $labelEnable
                    . $helper->closeTag();
        }

        return $markup;
    }

}
