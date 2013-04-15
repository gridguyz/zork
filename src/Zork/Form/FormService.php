<?php

namespace Zork\Form;

use Zork\Form\Factory as Factory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * FormService
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormService implements FormFactoryAwareInterface,
                             ServiceLocatorInterface
{

    /**
     * Zend factory
     *
     * @var \Zend\Form\Factory
     */
    protected $formFactory;

    /**
     * Form-definitions
     *
     * @var array
     */
    protected $definitions;

    /**
     * Form-instance cache
     *
     * @var array
     */
    protected $cache = array();

    /**
     * Get the form factory
     *
     * @return \Zork\Form\Factory
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * Compose a form factory into the object
     *
     * @param \Zork\Form\Factory $factory
     * @return \Zork\Form\FormService
     */
    public function setFormFactory( Factory $formFactory )
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    /**
     * Constructor
     *
     * @param \Zork\Form\Factory $formFactory
     * @param array $definitions
     */
    public function __construct( Factory $formFactory, array $definitions )
    {
        $this->setFormFactory( $formFactory );
        $this->definitions = $definitions;
    }

    /**
     * Has a form registered?
     *
     * @param string $name
     * @return bool
     */
    public function has( $name )
    {
        return ! empty( $this->definitions[$name] );
    }

    /**
     * Create a new form
     *
     * @param string $name
     * @param null|array|\Traversable $data
     * @return \Zend\Form\FormInterface
     */
    public function create( $name, $data = null )
    {
        return $this->formFactory
                    ->createForm( $this->definitions[$name] )
                    ->setData( empty( $data ) ? array() : $data );
    }

    /**
     * Get a form
     *
     * @param string $name
     * @return \Zend\Form\FormInterface
     */
    public function get( $name )
    {
        if ( ! isset( $this->cache[$name] ) )
        {
            $this->cache[$name] = $this->create( $name );
        }

        return $this->cache[$name];
    }

}
