<?php

namespace Zork\Form\View\Helper;

use Zend\Form\FormInterface;
use Zend\Form\FieldsetInterface;
use Zend\Form\ElementInterface;
use Zend\Form\Element\Collection;
use Zork\Form\Element\HashCollection;
use Zend\Form\View\Helper\Form as BaseHelper;
use Zend\Form\View\Helper\FormLabel;
use Zend\Form\View\Helper\FormElementErrors;
use Zend\I18n\Translator\Translator;
use Zork\Form\View\HelperAwareInterface;
use Zork\Form\TranslatorSettingsAwareInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Form
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Form extends BaseHelper
{

    /**
     * @var string
     */
    protected $formOpen         = '<dl class="%s">';

    /**
     * @var string
     */
    protected $formClose        = '</dl>';

    /**
     * @var string
     */
    protected $labelOpen        = '<dt class="%s">';

    /**
     * @var string
     */
    protected $labelClose       = '</dt>';

    /**
     * @var string
     */
    protected $inputOpen        = '<dd class="%s">';

    /**
     * @var string
     */
    protected $inputClose       = '</dd>';

    /**
     * @var string
     */
    protected $descriptionOpen  = '<p%s>';

    /**
     * @var string
     */
    protected $descriptionClose = '</p>';

    /**
     * @var string
     */
    protected $elementErrorClass    = 'error';

    /**
     * @var string
     */
    protected $elementRequiredClass = 'required';

    /**
     * @var string
     */
    protected $elementOptionalClass = 'optional';

    /**
     * @var string
     */
    protected $formValidatedClass   = 'validated';

    /**
     * @var FormLabel
     */
    protected $labelHelper;

    /**
     * @var FormElementErrors
     */
    protected $elementErrorsHelper;

    /**
     * @var array
     */
    protected $pluginCache = array();

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
     * Retrieve the FormElementErrors helper
     *
     * @return FormElementErrors
     */
    protected function getElementErrorsHelper()
    {
        if ( $this->elementErrorsHelper )
        {
            return $this->elementErrorsHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->elementErrorsHelper = $this->view
                                              ->plugin( 'form_element_errors' );
        }

        if ( ! $this->elementErrorsHelper instanceof FormElementErrors )
        {
            $this->elementErrorsHelper = new FormElementErrors();
        }

        if ( $this->hasTranslator() )
        {
            $this->elementErrorsHelper
                 ->setTranslator(
                        $this->getTranslator(),
                        $this->getTranslatorTextDomain()
                    );
        }

        return $this->elementErrorsHelper;
    }

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
     * Render an element
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @param  \Zend\Form\ElementInterface $element
     * @return string
     */
    public function renderElement( ElementInterface $element )
    {
        if ( ! method_exists( $this->view, 'plugin' ) )
        {
            // Bail early if renderer is not pluggable
            return '';
        }

        if ( $element instanceof HelperAwareInterface &&
             $plugin = $element->getRendererHelperName() )
        {
            $plugin = $this->tryLoadPlugin( $plugin );
        }

        if ( empty( $plugin ) )
        {
            $class  = explode( '\\', get_class( $element ) );
            $plugin = $this->tryLoadPlugin( 'form_' . end( $class ) );
        }

        if ( empty( $plugin ) )
        {
            $plugin = $this->tryLoadPlugin( 'form_' .
                                            $element->getAttribute( 'type' ) );
        }

        if ( empty( $plugin ) )
        {
            $plugin = $this->tryLoadPlugin( 'form_input' );
        }

        $enabled    = $this->isTranslatorEnabled();
        $textDomain = $this->getTranslatorTextDomain();

        if ( $element instanceof TranslatorSettingsAwareInterface )
        {
            $enabled = $element->isTranslatorEnabled();

            if ( null !== $element->getTranslatorTextDomain() )
            {
                $textDomain = $element->getTranslatorTextDomain();
            }
        }

        if ( method_exists( $plugin, 'setTranslatorEnabled' ) )
        {
            $plugin->setTranslatorEnabled( $enabled );
        }

        if ( $enabled )
        {
            if ( method_exists( $plugin, 'setTranslatorTextDomain' ) )
            {
                $plugin->setTranslatorTextDomain( $textDomain );
            }
        }

        return $plugin( $element );
    }

    /**
     * Create fieldset attributes
     *
     * @param \Zend\Form\FieldsetInterface $fieldset
     * @return string
     */
    protected function createFieldsetAttributes( FieldsetInterface $fieldset )
    {
        $markup = '';
        $escape = $this->getEscapeHtmlAttrHelper();

        foreach ( $fieldset->getAttributes() as $key => $value )
        {
            if ( $key == 'disabled' )
            {
                if ( $value )
                {
                    $markup .= ' disabled="disabled"';
                }
            }
            else
            {
                $markup .= ' ' . $key . '="' . $escape( $value ) . '"';
            }
        }

        return $markup;
    }

    /**
     * Render description
     *
     * @param \Zend\Form\ElementInterface $element
     * @return string
     */
    public function renderDescription( ElementInterface $element )
    {
        $markup      = '';
        $description = $element->getOption( 'description' );

        if ( ! empty( $description ) )
        {
            if ( is_object( $description ) )
            {
                $description = (array) $description;
            }

            if ( is_array( $description ) )
            {
                if ( empty( $description['attributes']['class'] ) )
                {
                    $description['attributes']['class'] = 'description';
                }
                else
                {
                    $description['attributes']['class'] .= ' description';
                }
            }
            else
            {
                $description = array(
                    'label'         => $description,
                    'translatable'  => true,
                    'attributes'    => array(
                        'class'     => 'description',
                    ),
                );
            }

            if ( $this->isTranslatorEnabled() &&
                 ! empty( $description['translatable'] ) )
            {
                $description['label'] = $this->getTranslator()
                                             ->translate(
                                                 $description['label'],
                                                 empty( $description['textDomain'] )
                                                     ? ( $this->getTranslatorTextDomain() ?: 'default' )
                                                     : $description['textDomain']
                                             );
            }

            $attributes = '';
            $escape     = $this->getEscapeHtmlAttrHelper();

            foreach ( $description['attributes'] as $attr => $value )
            {
                $attributes .= sprintf( ' %s="%s"', $attr, $escape( $value ) );
            }

            $markup .= sprintf( $this->descriptionOpen, $attributes );
            $markup .= str_replace( '%locale%',
                                    $this->view->locale(),
                                    $description['label'] );
            $markup .= $this->descriptionClose;
            $markup .= PHP_EOL;
        }

        return $markup;
    }

    /**
     * Render a fieldset-item
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @param  \Zend\Form\ElementInterface $element
     * @return string
     */
    public function renderFieldsetItem( ElementInterface $element )
    {
        $markup                 = '';
        $label                  = $element->getLabel();
        $labelHelper            = $this->getLabelHelper();
        $elementErrorsHelper    = $this->getElementErrorsHelper();

        if ( $this->isTranslatorEnabled() )
        {
            $labelHelper->setTranslatorEnabled( true )
                        ->setTranslatorTextDomain(
                            $this->getTranslatorTextDomain()
                        );
        }
        else
        {
            $labelHelper->setTranslatorEnabled( false );
        }

        if ( $element instanceof FieldsetInterface &&
             ! $element instanceof Collection      &&
             ! $element instanceof HashCollection   )
        {
            $attrs = $this->createFieldsetAttributes( $element );

            if ( $label && $this->isTranslatorEnabled() )
            {
                $label = $this->getTranslator()
                              ->translate(
                                    $label,
                                    $this->getTranslatorTextDomain() ?: 'default'
                                );
            }

            $markup .= sprintf( $this->inputOpen, 'fieldset' );
            $markup .= PHP_EOL;
            $markup .= sprintf( '<fieldset%s>', $attrs );
            $markup .= PHP_EOL;

            if ( $label )
            {
                $markup .= sprintf( '<legend>%s</legend>', $label );
                $markup .= PHP_EOL;
            }

            $markup .= $this->renderFieldset( $element, 'fieldset' );
            $markup .= PHP_EOL;
            $markup .= '</fieldset>';
            $markup .= PHP_EOL;
            $markup .= $this->inputClose;
        }
        else
        {
            $type    = $element->getAttribute( 'type' );
            $req     = $element->getAttribute( 'required' );
            $errors  = $elementErrorsHelper->render( $element );
            $class   = $type . ( $req ? ' ' . $this->elementRequiredClass
                                      : ' ' . $this->elementOptionalClass )
                             . ( $errors ? ' ' . $this->elementErrorClass : '' );

            if ( ! empty( $label ) )
            {
                $markup .= sprintf( $this->labelOpen, 'label label-' . $class );
                $markup .= PHP_EOL;
                $markup .= $labelHelper( $element );
                $markup .= PHP_EOL;
                $markup .= $this->labelClose;
                $markup .= PHP_EOL;
            }

            if ( $type != 'hidden' || $errors )
            {
                $markup .= sprintf( $this->inputOpen, 'input input-' . $class );
                $markup .= PHP_EOL;
            }

            $markup .= $this->renderElement( $element );
            $markup .= $this->renderDescription( $element );

            if ( $errors )
            {
                $markup .= PHP_EOL;
                $markup .= $errors;
            }

            if ( $type != 'hidden' || $errors )
            {
                $markup .= PHP_EOL;
                $markup .= $this->inputClose;
            }
        }

        $markup .= PHP_EOL;
        return $markup;
    }

    /**
     * Render a fieldset
     *
     * @param \Zend\Form\FieldsetInterface $fieldset
     * @param string $class
     * @return string
     */
    public function renderFieldset( FieldsetInterface $fieldset,
                                    $class = '' )
    {
        $markup     = '';
        $elements   = array();
        $markup    .= $this->renderDescription( $fieldset );
        $markup    .= sprintf( $this->formOpen, $class );
        $markup    .= PHP_EOL;

        /* @var $element \Zend\Form\ElementInterface */
        foreach ( $fieldset as $element )
        {
            $group = $element->getOption( 'display_group' );

            if ( empty( $group ) )
            {
                $elements[] = $element;
            }
            else
            {
                $elements[$group][] = $element;
            }
        }

        foreach ( $elements as $key => $item )
        {
            if ( is_array( $item ) )
            {
                if ( $this->isTranslatorEnabled() )
                {
                    $key = $this->getTranslator()
                                ->translate( $key,
                                             $this->getTranslatorTextDomain() );
                }

                $markup .= sprintf( $this->inputOpen, 'display-group' );
                $markup .= PHP_EOL;
                $markup .= '<fieldset class="display-group">';
                $markup .= PHP_EOL;
                $markup .= sprintf( '<legend>%s</legend>', $key );
                $markup .= PHP_EOL;
                $markup .= sprintf( $this->formOpen, 'display-group-items' );
                $markup .= PHP_EOL;

                foreach ( $item as $element )
                {
                    $markup .= $this->renderFieldsetItem( $element );
                }

                $markup .= PHP_EOL;
                $markup .= $this->formClose;
                $markup .= PHP_EOL;
                $markup .= '</fieldset>';
                $markup .= PHP_EOL;
                $markup .= $this->inputClose;
            }
            else
            {
                $markup .= $this->renderFieldsetItem( $item );
            }
        }

        $markup .= $this->formClose;
        return $markup;
    }

    /**
     * Render form
     *
     * @return \Zend\Form\FormInterface
     */
    public function render( FormInterface $form )
    {
        if ( method_exists( $form, 'prepare' ) )
        {
            $form->prepare();
        }

        $lang       = $form->getAttribute( 'lang' );
        $translator = $this->getTranslator();

        if ( empty( $lang ) && ! empty( $translator ) )
        {
            $form->setAttribute( 'lang', $translator->getLocale() );
        }

        $markup  = '';
        $markup .= $this->openTag( $form );
        $markup .= PHP_EOL;
        $markup .= $this->renderFieldset( $form,
                                          $form->hasValidated()
                                              ? $this->formValidatedClass
                                              : '' );
        $markup .= PHP_EOL;
        $markup .= $this->closeTag();
        return $markup;
    }

    /**
     * Render a form from the provided $form,
     *
     * @param  ElementInterface             $element
     * @param  null|bool|string|\Translator $translator
     * @param  null|string                  $textDomain
     * @throws Exception\DomainException
     * @return string
     */
    public function __invoke( FormInterface $form = null,
                              $translator = null,
                              $textDomain = null )
    {
        if ( ! empty( $translator ) )
        {
            if ( $translator instanceof Translator )
            {
                $this->setTranslator( $translator );

                if ( ! empty( $textDomain ) )
                {
                    $this->setTranslatorTextDomain( (string) $textDomain );
                }
            }
            else if ( is_bool( $translator ) )
            {
                $this->setTranslatorEnabled( $translator );
            }
            else
            {
                $this->setTranslatorTextDomain( (string) $translator );
            }
        }

        return parent::__invoke( $form );
    }

}
