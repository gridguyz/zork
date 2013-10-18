<?php

namespace Zork\Form\Element;

use Zend\Captcha\AbstractWord;
use Zend\Captcha\AdapterInterface;
use Zend\Form\Element\Captcha as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;
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
            if ( $this->defaultCaptcha instanceof AdapterInterface )
            {
                $captcha = clone $this->defaultCaptcha;
            }
            else
            {
                $captcha = $this->defaultCaptcha;
            }

            $this->setCaptcha( $captcha );

            $captchaObject  = $this->getCaptcha();
            $serviceLocator = $this->getServiceLocator();

            if ( $captchaObject instanceof AbstractWord && $serviceLocator &&
                 $serviceLocator->has( 'Zend\Session\ManagerInterface' ) )
            {
                $captchaObject->getSession()
                              ->setManager( $serviceLocator->get(
                                    'Zend\Session\ManagerInterface'
                                ) );
            }
        }

        return parent::getCaptcha();
    }

}
