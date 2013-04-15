<?php

namespace Zork\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Element\Radio;
use Zend\Form\View\Helper\FormRadio as ZendFormRadio;

/**
 * FormRadio
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormRadio extends ZendFormRadio
{

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
        if ( $element instanceof Radio )
        {
            $options = $element->getValueOptions();

            if ( empty( $options ) )
            {
                if ( ( $translator = $this->getTranslator() ) !== null )
                {
                    return '<i>'
                         . $translator->translate( 'default.empty', 'default' )
                         . '</i>';
                }
                else
                {
                    return '';
                }
            }
        }

        return parent::render( $element );
    }

}
