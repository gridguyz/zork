<?php

namespace Zork\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\View\Helper\FormMultiCheckbox as ZendFormMultiCheckbox;

/**
 * FormMultiCheckboxGroup
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormMultiCheckbox extends ZendFormMultiCheckbox
{

    /**
     * @var string
     */
    protected $separator = '<br />';

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
        if ( $element instanceof MultiCheckbox )
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

        return '<div class="multi_checkbox">' . parent::render( $element ) . '</div>';
    }

}
