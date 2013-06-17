<?php

namespace Zork\Form\View\Helper;

use Zend\Stdlib\ArrayUtils;
use Zend\Form\View\Helper\FormSelect as ZendFormSelect;

/**
 * FormSelect
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormSelect extends ZendFormSelect
{

    /**
     * Render an array of options
     *
     * Individual options should be of the form:
     *
     * <code>
     * array(
     *     'value'    => 'value',
     *     'label'    => 'label',
     *     'disabled' => $booleanFlag,
     *     'selected' => $booleanFlag,
     * )
     * </code>
     *
     * @param  array $options
     * @param  array $selectedOptions Option values that should be marked as selected
     * @return string
     */
    public function renderOptions(array $options, array $selectedOptions = array())
    {
        $template      = '<option %s>%s</option>';
        $optionStrings = array();
        $escapeHtml    = $this->getEscapeHtmlHelper();

        foreach ( $options as $key => $optionSpec )
        {
            $value    = '';
            $label    = '';
            $selected = false;
            $disabled = false;

            if ( is_scalar( $optionSpec ) )
            {
                $optionSpec = array(
                    'label' => $optionSpec,
                    'value' => $key
                );
            }

            if ( isset( $optionSpec['options'] ) &&
                 is_array( $optionSpec['options'] ) )
            {
                $optionStrings[] = $this->renderOptgroup(
                    $optionSpec,
                    $selectedOptions
                );

                continue;
            }

            if ( isset( $optionSpec['value'] ) )
            {
                $value = $optionSpec['value'];
            }

            if ( isset( $optionSpec['label'] ) )
            {
                $label = $optionSpec['label'];
                unset( $optionSpec['label'] );
            }

            if ( isset( $optionSpec['selected'] ) )
            {
                $selected = $optionSpec['selected'];
            }

            if ( isset( $optionSpec['disabled'] ) )
            {
                $disabled = $optionSpec['disabled'];
            }

            if ( ArrayUtils::inArray( $value, $selectedOptions ) )
            {
                $selected = true;
            }

            if ( null !== ( $translator = $this->getTranslator() ) )
            {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            $attributes = array_merge(
                $optionSpec,
                compact( 'value', 'selected', 'disabled' )
            );

            $this->validTagAttributes = $this->validOptionAttributes;
            $optionStrings[] = sprintf(
                $template,
                $this->createAttributesString( $attributes ),
                $escapeHtml( $label )
            );
        }

        return implode( "\n", $optionStrings );
    }

}
