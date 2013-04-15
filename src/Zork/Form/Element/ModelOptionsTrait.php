<?php

namespace Zork\Form\Element;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ModelOptionsTrait
 *
 * @implements \Zend\ServiceManager\ServiceLocatorAwareInterface
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait ModelOptionsTrait
{

    /**
     * Container of service locator.
     *
     * @var type
     */
    protected $serviceLocator;

    /**
     * Setter of service locator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zork\Form\Element
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Getter of service locator
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @return array
     */
    public function getValueOptions()
    {
        if ( empty( $this->valueOptions ) &&
             ! empty( $this->options['model'] ) &&
             ! empty( $this->options['method'] ) )
        {
            $model  = (string) $this->options['model'];
            $method = (string) $this->options['method'];
            $args   = empty( $this->options['arguments'] ) ? array()
                    : (array) $this->options['arguments'];

            if ( ! method_exists( $this, 'getEmptyOption' ) &&
                 ! empty( $this->options['empty_option'] ) )
            {
                $emptyOption = $this->options['empty_option'];

                if ( ! empty( $this->translatorEnabled ) )
                {
                    $emptyOption = $this->getServiceLocator()
                                        ->get( 'Zend\I18n\Translator\Translator' )
                                        ->translate(
                                            $emptyOption,
                                            empty( $this->translatorTextDomain )
                                                ? 'default'
                                                : $this->translatorTextDomain
                                        );
                }

                $this->valueOptions = array(
                    '' => $emptyOption,
                );
            }
            else
            {
                $this->valueOptions = array();
            }

            $this->translatorEnabled = false;
            $this->valueOptions     += (array) call_user_func_array(
                array(
                    $this->getServiceLocator()
                         ->get( $model ),
                    $method
                ),
                $args
            );
        }

        return parent::getValueOptions();
    }

}
