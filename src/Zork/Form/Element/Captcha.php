<?php

namespace Zork\Form\Element;

use Zend\Form\Element\Captcha as ElementBase;
use Zend\InputFilter\InputProviderInterface;
use Zork\Form\TranslatorSettingsAwareInterface;
use Zend\Captcha\AdapterInterface as CaptchaAdapterInterface;

/**
 * Element
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Captcha extends ElementBase
           implements InputProviderInterface,
                      TranslatorSettingsAwareInterface
{

    use InputProviderTrait;

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
            if ( $this->defaultCaptcha instanceof CaptchaAdapterInterface )
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
