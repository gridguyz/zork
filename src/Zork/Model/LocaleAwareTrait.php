<?php

namespace Zork\Model;

use Locale;

/**
 * LocaleTrait
 *
 * implements LocaleAwareInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait LocaleAwareTrait
{

    /**
     * Locale parameter for querying
     *
     * @var string
     */
    protected $locale = null;

    /**
     * Get default locale for model
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        return Locale::getDefault();
    }

    /**
     * Get language parameter for model
     *
     * @return string
     */
    public function getPrimaryLanguage()
    {
        return Locale::getPrimaryLanguage( $this->getLocale() );
    }

    /**
     * Get locale parameter for model
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale ?: $this->normalizeLocale( Locale::getDefault() );
    }

    /**
     * Set locale parameter for model
     *
     * @param string $locale
     * @return self
     */
    public function setLocale( $locale = null )
    {
        $this->locale = empty( $locale )
            ? null : $this->normalizeLocale( $locale );

        if ( $this instanceof LocaleAwaresAwareInterface )
        {
            foreach ( $this->getLocaleAwares() as $localeAware )
            {
                if ( $localeAware instanceof LocaleAwareInterface )
                {
                    $localeAware->setLocale( $this->locale );
                }
            }
        }
        else if ( $this instanceof MapperAwareInterface )
        {
            $mapper = $this->getMapper();

            if ( $mapper instanceof LocaleAwareInterface )
            {
                $mapper->setLocale( $this->locale );
            }
        }

        return $this;
    }

    /**
     * Normalize locale
     *
     * @param string $locale
     * @return string
     */
    protected function normalizeLocale( $locale )
    {
        $parsed = Locale::parseLocale( $locale );

        if ( empty( $parsed ) ||
             empty( $parsed['language'] ) )
        {
            return '';
        }

        $result = $parsed['language'];

        if ( ! empty( $parsed['region'] ) )
        {
            $result .= '_' . strtoupper( $parsed['region'] );
        }

        return $result;
    }

}
