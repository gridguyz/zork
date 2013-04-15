<?php

namespace Zork\Mvc\Controller\Plugin;

use Locale as IntlLocale;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Http\Header\AcceptLanguage;
use Zend\Http\Header\Accept\FieldValuePart\LanguageFieldValuePart;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Locale extends AbstractPlugin
{

    /**
     * Get locale
     *
     * @return string
     */
    protected function getCurrent()
    {
        return IntlLocale::getDefault();
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getCurrent();
    }

    /**
     * Parse header
     *
     * @param null|\Zend\Http\Header\AcceptLanguage $header
     * @return array
     */
    public function parseHeader( $header )
    {
        $locale = array();

        if ( $header instanceof AcceptLanguage )
        {
            foreach ( $header->getPrioritized() as $part )
            {
                if ( $part instanceof LanguageFieldValuePart )
                {
                    $locale = IntlLocale::parseLocale( $part->getLanguage() );
                    $key    = $locale['language'];

                    if ( isset( $locale['region'] ) )
                    {
                        $key .= '_' . $locale['region'];
                    }

                    $locales[$key] = $part->getPriority();
                }
            }
        }

        return $locales;
    }

    /**
     * Invoke as a functor
     *
     * @return \Zork\Mvc\Controller\Plugin\Locale
     */
    public function __invoke()
    {
        return $this;
    }

}
