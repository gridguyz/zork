<?php

namespace Zork\Form\View\Helper\Captcha;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\Captcha\Image;

/**
 * Regeneratable
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Regeneratable extends Image
{

    /**
     * Render the captcha
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render( ElementInterface $element )
    {
        $position       = $this->getCaptchaPosition();
        $closingBracket = $this->getInlineClosingBracket();
        $image          = parent::render( $element );
        $captcha        = $element->getCaptcha();
        $separation     = '<br' . $closingBracket;
        $regenerate     = '<input data-js-type="js.captcha.regenerate'
                        . '" type="button" data-js-captcha-id="' . $captcha->getId()
                        . '" class="captcha-regenerate" value="'
                        . $captcha->getRegenerateLabel() . '"' . $closingBracket;

        $image = preg_replace( '/<img\s[^>]*>/', '$0' . $separation, $image );

        if ( $position == self::CAPTCHA_PREPEND )
        {
            return $regenerate . PHP_EOL . $image;
        }
        else
        {
            return $image . PHP_EOL . $regenerate;
        }
    }

}
