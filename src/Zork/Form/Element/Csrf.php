<?php

namespace Zork\Form\Element;

use Zend\Form\Element\Csrf as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;
use Zend\Session\Container as SessionContainer;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Csrf extends ElementBase
        implements InputProviderInterface,
                   ServiceLocatorAwareInterface,
                   TranslatorSettingsAwareInterface
{

    use InputProviderTrait,
        ServiceLocatorAwareTrait;

    /**
     * Get CSRF validator
     *
     * @return  \Zend\Validator\Csrf
     */
    public function getCsrfValidator()
    {
        if ( null === $this->csrfValidator )
        {
            $serviceLocator = $this->getServiceLocator();

            if ( $serviceLocator &&
                 $serviceLocator->has( 'Zend\Session\ManagerInterface' ) )
            {
                $defaultManager = SessionContainer::getDefaultManager();
                $serviceManager = $serviceLocator->get( 'Zend\Session\ManagerInterface' );

                if ( $defaultManager !== $serviceManager )
                {
                    SessionContainer::setDefaultManager( $serviceManager );
                }
            }
        }

        return parent::getCsrfValidator();
    }

}
