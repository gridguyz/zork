<?php

namespace Zork\Form\View\Helper;

use Zend\Form\Exception;
use Zend\Form\ElementInterface;
use Zork\Form\Element\RadioGroup;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * FormRadioGroup
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormRadioGroup extends AbstractHelper
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
    protected $validRadioGroupAttributes = array(
    );

    /**
     * @var array
     */
    protected $validRadioAttributes = array(
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
     * @var array
     */
    protected $pluginCache = array();

    /**
     * Try to load a plugin by name
     *
     * @param string $name
     * @return \Zend\Form\View\Helper\AbstractHelper
     */
    protected function tryLoadPlugin( $name )
    {
        if ( ! method_exists( $this->view, 'plugin' ) )
        {
            return null;
        }

        if ( empty( $this->pluginCache[$name] ) )
        {
            try
            {
                $this->pluginCache[$name] = $this->view->plugin( $name );
            }
            catch ( ServiceNotFoundException $ex )
            {
                return null;
            }

            if ( $this->hasTranslator() &&
                 method_exists( $this->pluginCache[$name], 'setTranslator' ) )
            {
                $this->pluginCache[$name]
                     ->setTranslator(
                           $this->getTranslator(),
                           $this->getTranslatorTextDomain()
                       );
            }
        }

        return $this->pluginCache[$name];
    }

    /**
     * Render a form radio-group element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     * @return string
     */
    public function render( ElementInterface $element )
    {
        if ( ! $element instanceof RadioGroup )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s requires that the element is of type Zork\Form\Element\RadioGroup',
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
        $additionalAttribures = array(
            'type'      => 'radio',
            'name'      => $name,
            'required'  => empty( $attributes['required'] ) ? null : 'required',
        );

        $this->validTagAttributes = $this->validRadioGroupAttributes;
        return sprintf(
            '<div %s>%s</div>',
            $this->createAttributesString( $attributes ),
            $this->renderRadios(
                $options,
                $value,
                $additionalAttribures,
                $element->getOption( 'option_attribute_filters' )
            )
        );
    }

    /**
     * Render an array of radios
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
     * @param  array $optionAttributeFilters
     * @return string
     */
    public function renderRadios( array $options,
                                  $selectedValue                = null,
                                  array $additionalAttribures   = array(),
                                  array $optionAttributeFilters = null)
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
                    $additionalAttribures,
                    $optionAttributeFilters
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

            if ( $value == $selectedValue )
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
                     ( ! empty( $this->validRadioAttributes[$attrName] ) ||
                     substr( $attrName, 0, 5 ) === 'data-' ) )
                {
                    $attributes[$attrName] = $attrValue;
                }
            }

            if ( ! empty( $optionAttributeFilters ) )
            {
                foreach ( $optionAttributeFilters as $attrName => $pluginName )
                {
                    if ( ! empty( $attributes[$attrName] ) )
                    {
                        $plugin = $this->tryLoadPlugin( $pluginName );

                        if ( $plugin && is_callable( $plugin ) )
                        {
                            $attributes[$attrName] = $plugin( $attributes[$attrName] );
                        }
                    }
                }
            }

            $this->validTagAttributes = $this->validRadioAttributes;
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
     * @param  array $optionAttributeFilters
     * @return string
     */
    public function renderFieldset( array $fieldset,
                                    $selectedValue                  = null,
                                    array $additionalAttribures     = array(),
                                    array $optionAttributeFilters   = null )
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
            $this->renderRadios(
                $options,
                $selectedValue,
                $additionalAttribures,
                $optionAttributeFilters
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
