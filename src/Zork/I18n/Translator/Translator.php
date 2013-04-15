<?php

namespace Zork\I18n\Translator;

use Zend\I18n\Translator\Translator as ZendTranslator;


/**
 * Translator
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Translator extends ZendTranslator
{

    /**
     * Load messages for a given language and domain.
     *
     * @param  string                     $textDomain
     * @param  string                     $locale
     * @throws Exception\RuntimeException
     * @return void
     */
    protected function loadMessages( $textDomain, $locale )
    {
        $result = parent::loadMessages( $textDomain, $locale );

        if ( ! isset( $this->messages[$textDomain][$locale] ) )
        {
            $this->messages[$textDomain][$locale] = array();
        }

        return $result;
    }

    /**
     * Translate a message.
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function translate( $message, $textDomain = 'default', $locale = null )
    {
        return parent::translate(
            $message,
            strstr( $message, '.', true ) ?: $textDomain,
            (string) $locale ?: null
        );
    }

    /**
     * Translate a plural message.
     *
     * @param  string                         $singular
     * @param  string                         $plural
     * @param  int                            $number
     * @param  string                         $textDomain
     * @param  string|null                    $locale
     * @return string
     * @throws Exception\OutOfBoundsException
     */
    public function translatePlural( $singular, $plural, $number, $textDomain = 'default', $locale = null )
    {
        return parent::translatePlural(
            $singular,
            $plural,
            $number,
            strstr( $singular, '.', true ) ?: strstr( $plural, '.', true ) ?: $textDomain,
            (string) $locale ?: null
        );
    }

}
