<?php

namespace Zork\View\Helper;

use Locale as IntlLocale;
use Zork\I18n\Locale\Locale as LocaleService;
use Zend\View\Helper\AbstractHelper;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Locale extends AbstractHelper
{

    /**
     * @var \Zork\I18n\Locale\Locale
     */
    protected $localeService;

    /**
     * @return \Zork\I18n\Locale\Locale
     */
    public function getLocaleService()
    {
        return $this->localeService;
    }

    /**
     * @param \Zork\I18n\Locale\Locale $localeService
     * @return \Zork\View\Helper\Locale
     */
    public function setLocaleService( LocaleService $localeService )
    {
        $this->localeService = $localeService;
        return $this;
    }

    /**
     * @param \Zork\I18n\Locale\Locale $localeService
     */
    public function __construct( LocaleService $localeService )
    {
        $this->setLocaleService( $localeService );
    }

    /**
     * Invokable helper
     *
     * @return \Zork\View\Helper\Locale
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * Get current locale
     *
     * @return string
     */
    public function getCurrent()
    {
        return $this->getLocaleService()
                    ->getCurrent();
    }

    /**
     * Get default locale
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->getLocaleService()
                    ->getDefault();
    }

    /**
     * Get available locales
     *
     * @param bool $group
     * @return array
     */
    public function getAvailableLocales( $group = false )
    {
        return $this->getLocaleService()
                    ->getAvailableLocales( $group );
    }

    /**
     * Get available flags of locales
     *
     * @param bool $group
     * @return array
     */
    public function getAvailableFlags()
    {
        return $this->getLocaleService()
                    ->getAvailableFlags();
    }

    /**
     * Get current locale
     *
     * @return String
     */
    public function __toString()
    {
        return (string) $this->getCurrent();
    }

    /**
     * Get current locale's primary language
     *
     * @return string
     */
    public function getPrimaryLanguage()
    {
        return IntlLocale::getPrimaryLanguage( $this->getCurrent() );
    }

    /**
     * Get current locale's region
     *
     * @return string
     */
    public function getRegion()
    {
        return IntlLocale::getRegion( $this->getCurrent() );
    }

    /**
     * Returns the ISO representation of the locale
     * ("lang-REGION" as opposed to its canonical "lang_REGION")
     *
     * @return string
     */
    public function toIso()
    {
        $locale = IntlLocale::parseLocale( $this->getCurrent() );
        $result = $locale['language'];

        if ( ! empty( $locale['region'] ) )
        {
            $result .= '-' . $locale['region'];
        }

        return $result;
    }

}
