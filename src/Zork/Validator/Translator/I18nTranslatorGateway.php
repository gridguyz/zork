<?php

namespace Zork\Validator\Translator;

use Zend\I18n\Translator\Translator;
use Zend\Validator\Translator\TranslatorInterface;

/**
 * I18nTranslatorGateway
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class I18nTranslatorGateway implements TranslatorInterface
{

    /**
     * @var Translator
     */
    protected $translator;

    /**
     *
     * @return  Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param   Translator  $translator
     * @return  I18nTranslatorGateway
     */
    public function setTranslator( I18nTranslator $translator )
    {
        $this->translator = $translator;
    }

    /**
     * @param   Translator  $translator
     */
    public function __construct( Translator $translator )
    {
        $this->setTranslator( $translator );
    }

    /**
     * @param   string  $message
     * @param   string  $textDomain
     * @param   string  $locale
     * @return  string
     */
    public function translate( $message, $textDomain = 'default', $locale = null )
    {
        return $this->getTranslator()
                    ->translate( $message, $textDomain, $locale );
    }

}
