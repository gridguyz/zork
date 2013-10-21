<?php

namespace Zork\Form\Element;

use Zend\Captcha\AdapterInterface;
use Zend\Form\Element\Captcha as ElementBase;
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
class Captcha extends ElementBase
           implements InputProviderInterface,
                      ServiceLocatorAwareInterface,
                      TranslatorSettingsAwareInterface
{

    use InputProviderTrait,
        ServiceLocatorAwareTrait;

    protected $attributes = array(
        'type' => 'captcha',
    );

    /**
     * @var array|\Traversable|\Zend\Captcha\AdapterInterface
     */
    protected $defaultCaptcha = array(
        'class' => 'Zork\Captcha\Regeneratable',
    );

    /**
     * @return array|\Traversable|\Zend\Captcha\AdapterInterface|null
     */
    public function getDefaultCaptcha()
    {
        return $this->defaultCaptcha;
    }

    /**
     * @param array|\Traversable|\Zend\Captcha\AdapterInterface $defaultCaptcha
     * @return \Zork\Form\Element\Captcha
     */
    public function setDefaultCaptcha( $defaultCaptcha )
    {
        $this->defaultCaptcha = $defaultCaptcha;
        return $this;
    }

    /**
     * Retrieve captcha (if any)
     *
     * @return null|ZendCaptcha\AdapterInterface
     */
    public function getCaptcha()
    {
        if ( null === $this->captcha )
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

            if ( $this->defaultCaptcha instanceof AdapterInterface )
            {
                $captcha = clone $this->defaultCaptcha;
            }
            else
            {
                $captcha = $this->defaultCaptcha;
            }

            $this->setCaptcha( $captcha );
        }

        return parent::getCaptcha();
    }

}
