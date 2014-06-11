<?php

namespace Zork\Mvc\Controller\Plugin;

use Locale as IntlLocale;
use Zend\Http\Header\AcceptLanguage;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
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
     * @param   null|\Zend\Http\Header\AcceptLanguage   $header
     * @return  array
     */
    public function parseHeader( $header )
    {
        $locales    = array();
        $controller = $this->getController();

        if ( $controller instanceof ServiceLocatorAwareInterface )
        {
            $availables = $controller->getServiceLocator()
                                     ->get( 'Locale' )
                                     ->getAvailableLocales();
        }
        else
        {
            $availables = array( IntlLocale::getDefault() );
        }

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

                    if ( $availables )
                    {
                        $key = IntlLocale::lookup( $availables, $key, false, '' );
                    }

                    if ( $key )
                    {
                        $locales[$key] = max(
                            $part->getPriority(),
                            empty( $locales[$key] ) ? 0 : $locales[$key]
                        );
                    }
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
