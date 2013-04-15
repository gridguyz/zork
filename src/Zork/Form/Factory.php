<?php

namespace Zork\Form;

use Zend\Form\Factory as ZendFactory;
use Zend\Form\FieldsetInterface;
use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zork\ServiceManager\ApplicationServiceLocatorAwareInterface;

/**
 * Zork Form Factory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Factory extends ZendFactory
           implements ServiceLocatorAwareInterface
{

    /**
     * Container of service locator.
     * @var type
     */
    protected $serviceLocator;

    /**
     * Container of service locator.
     * @var type
     */
    protected $inputFilterFactoryDefaultsInitialized = false;

    /**
     * Getter of service locator
     *
     * @return \Zork\Form\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Setter of service locator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Form\Factory
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get current input filter factory
     *
     * If none provided, uses an unconfigured instance.
     *
     * @return InputFilterFactory
     */
    public function getInputFilterFactory()
    {
        $inputFilterFactory = parent::getInputFilterFactory();

        if ( ! $this->inputFilterFactoryDefaultsInitialized )
        {
            $this->inputFilterFactoryDefaultsInitialized = true;

            $dfc = $inputFilterFactory->getDefaultFilterChain();
            $dvc = $inputFilterFactory->getDefaultValidatorChain();

            if ( empty( $dfc ) )
            {
                $inputFilterFactory->setDefaultFilterChain(
                    $dfc = new FilterChain()
                );
            }

            if ( empty( $dvc ) )
            {
                $inputFilterFactory->setDefaultValidatorChain(
                    $dvc = new ValidatorChain()
                );
            }

            $initializer = array(
                $this,
                'initializeApplicationServiceLocators'
            );

            $dfc->getPluginManager()
                ->addInitializer( $initializer, false );

            $dvc->getPluginManager()
                ->addInitializer( $initializer, false );
        }

        return $inputFilterFactory;
    }

    /**
     * @param \Zend\InputFilter\Factory $inputFilterFactory
     * @return \Zork\Form\Factory
     */
    public function setInputFilterFactory( InputFilterFactory $inputFilterFactory )
    {
        $this->inputFilterFactoryDefaultsInitialized = false;
        return parent::setInputFilterFactory( $inputFilterFactory );
    }

    /**
     * Create an element, fieldset, or form
     *
     * Introspects the 'type' key of the provided $spec, and determines what
     * type is being requested; if none is provided, assumes the spec
     * represents simply an element.
     *
     * @param  array|Traversable $spec
     * @return ElementInterface
     * @throws Exception\DomainException
     */
    public function create( $spec )
    {
        $element = parent::create( $spec );

        if ( $element instanceof ServiceLocatorAwareInterface )
        {
            $element->setServiceLocator( $this->getServiceLocator() );
        }

        if ( $element instanceof FieldsetInterface )
        {
            $element->setFormFactory( $this );
        }

        if ( $element instanceof PrepareElementsAwareInterface )
        {
            $element->prepareElements();
        }

        return $element;
    }

    /**
     * Create a form based on the provided specification
     *
     * Specification follows that of {@link createFieldset()}, and adds the
     * following keys:
     *
     * - input_filter: input filter instance, named input filter class, or
     *   array specification for the input filter factory
     * - hydrator: hydrator instance or named hydrator class
     *
     * @param  array|Traversable|ArrayAccess $spec
     * @return FormInterface
     * @throws Exception\InvalidArgumentException for an invalid $spec
     * @throws Exception\DomainException for an invalid form type
     */
    public function createForm( $spec )
    {
        if ( empty( $spec['type'] ) )
        {
            $spec['type'] = 'Zork\Form\Form';
        }

        return parent::createForm( $spec );
    }

    /**
     * Initialization for DefaultFilterChain &
     * DefaultValidatorChain's plugin-brokers
     *
     * @param \Zork\ServiceManager\ApplicationServiceLocatorAwareInterface|mixed $instance
     * @return \Zork\Form\Factory
     */
    public function initializeApplicationServiceLocators( $instance )
    {
        if ( $instance instanceof ApplicationServiceLocatorAwareInterface )
        {
            $instance->setServiceLocator( $this->getServiceLocator() );
        }

        return $this;
    }

}
