<?php

namespace Zork\Form\View\Helper;

use Zend\Form\Exception;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;
use Zork\Form\Element\MultiCheckboxGroup;

/**
 * FormMultiCheckboxGroup
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormMultiCheckboxGroup extends AbstractHelper
{

    /**
     * Attributes valid for the current tag
     *
     * Will vary based on whether a select, option, or optgroup is being rendered
     *
     * @var array
     */
    protected $validTagAttributes;

    /**
     * @var array
     */
    protected $validCheckboxGroupAttributes = array(
    );

    /**
     * @var array
     */
    protected $validCheckboxAttributes = array(
        'autofocus' => true,
        'disabled'  => true,
        'form'      => true,
        'required'  => true,
        'checked'   => true,
        'name'      => true,
        'label'     => true,
        'type'      => true,
        'value'     => true,
    );

    /**
     * @var array
     */
    protected $validFieldsetAttributes = array(
        'disabled'  => true,
    );

    /**
     * Render a form checkbox-group element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     * @return string
     */
    public function render( ElementInterface $element )
    {
        if ( ! $element instanceof MultiCheckboxGroup )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s requires that the element is of type Zork\Form\Element\MutiCheckboxGroup',
                __METHOD__
            ) );
        }

        $name = $element->getName();
        if ( empty( $name ) && $name !== 0 )
        {
            throw new Exception\DomainException( sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ) );
        }

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

        if ( ( $emptyOption = $element->getEmptyOption() ) !== null )
        {
            $options = array( '' => $emptyOption ) + $options;
        }

        $attributes = $element->getAttributes();
        $value      = $element->getValue();
        $addAttr    = array(
            'type'      => 'checkbox',
            'name'      => substr( $name, -2 ) == '[]' ? $name : $name . '[]',
            'required'  => empty( $attributes['required'] ) ? null : 'required',
        );

        $this->validTagAttributes = $this->validCheckboxGroupAttributes;

        if ( null !== $element->getTranslatorTextDomain() )
        {
            $this->setTranslatorTextDomain( $element->getTranslatorTextDomain() );
        }

        if ( empty( $attributes['class'] ) )
        {
            $attributes['class'] = 'multi_checkbox';
        }

        return sprintf(
            '<div %s>%s</div>',
            $this->createAttributesString( $attributes ),
            sprintf(
                '<input type="hidden" name="%s" value="" />' . PHP_EOL,
                substr( $name, -2 ) == '[]' ? substr( $name, 0, -2 ) : $name
            ) .
            $this->renderCheckboxes( $options, $value, $addAttr )
        );
    }

    /**
     * Render an array of checkboxes
     *
     * Individual radios should be of the form:
     *
     * <code>
     * array(
     *     'value'    => 'value',
     *     'label'    => 'label',
     *     'disabled' => $booleanFlag,
     *     'checked'  => $booleanFlag,
     * )
     * </code>
     *
     * @param  array $options
     * @param  array $selectedOptions Option values that should be marked as selected
     * @param  array $additionalAttribures
     * @return string
     */
    public function renderCheckboxes( array $options,
                                      $selectedValue               = null,
                                      array $additionalAttribures  = array() )
    {
        $template      = '<label><input %s />%s</label>';
        $radioStrings  = array();
        $escapeHtml    = $this->getEscapeHtmlHelper();

        foreach ( $options as $key => $optionSpec )
        {
            $value    = '';
            $label    = '';
            $checked  = false;
            $disabled = false;

            if ( is_scalar( $optionSpec ) )
            {
                $optionSpec = array(
                    'label' => $optionSpec,
                    'value' => $key,
                );
            }

            if ( isset( $optionSpec['options'] ) &&
                 is_array( $optionSpec['options'] ) )
            {
                $radioStrings[] = $this->renderFieldset(
                    $optionSpec,
                    $selectedValue,
                    $additionalAttribures
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
            }

            if ( isset( $optionSpec['checked'] ) )
            {
                $checked = $optionSpec['checked'];
            }

            if ( isset( $optionSpec['disabled'] ) )
            {
                $disabled = $optionSpec['disabled'];
            }

            if ( in_array( $value, (array) $selectedValue ) )
            {
                $checked = true;
            }

            if ( null !== ( $translator = $this->getTranslator() ) )
            {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }

            $attributes = array_merge(
                $additionalAttribures,
                compact( 'value', 'checked', 'disabled' )
            );

            foreach ( $optionSpec as $attrName => $attrValue )
            {
                if ( empty( $attributes[$attrName] ) &&
                     ( ! empty( $this->validCheckboxAttributes[$attrName] ) ||
                     substr( $attrName, 0, 5 ) === 'data-' ) )
                {
                    $attributes[$attrName] = $attrValue;
                }
            }

            $this->validTagAttributes = $this->validCheckboxAttributes;
            $radioStrings[] = sprintf(
                $template,
                $this->createAttributesString( $attributes ),
                $escapeHtml( $label )
            );
        }

        return implode( "\n", $radioStrings );
    }

    /**
     * Render a fieldset
     *
     * See {@link renderRadios()} for the radios specification. Basically,
     * a fieldset is simply an radio that has an additional "options" key
     * with an array following the specification for renderRadios().
     *
     * @param  array $fieldset
     * @param  array $selectedOptions
     * @param  array $additionalAttribures
     * @return string
     */
    public function renderFieldset( array $fieldset,
                                    $selectedValue              = null,
                                    array $additionalAttribures = array() )
    {
        $template = '<fieldset%s><legend>%s</legend>%s</fieldset>';

        $options = array();
        if ( isset( $fieldset['options'] ) &&
             is_array( $fieldset['options'] ) )
        {
            $options = $fieldset['options'];
            unset( $fieldset['options'] );
        }

        $label = '';
        if ( isset( $fieldset['label'] ) )
        {
            $label = $fieldset['label'];
            unset( $fieldset['label'] );

            if ( null !== ( $translator = $this->getTranslator() ) )
            {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }
        }

        $this->validTagAttributes = $this->validFieldsetAttributes;
        $attributes = $this->createAttributesString( $fieldset );
        $escapeHtml = $this->getEscapeHtmlHelper();

        if ( ! empty( $attributes ) )
        {
            $attributes = ' ' . $attributes;
        }

        return sprintf(
            $template,
            $attributes,
            $escapeHtml( $label ),
            $this->renderCheckboxes(
                $options,
                $selectedValue,
                $additionalAttribures
            )
        );
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormRadioGroup
     */
    public function __invoke( ElementInterface $element = null )
    {
        if ( ! $element )
        {
            return $this;
        }

        return $this->render( $element );
    }

}
