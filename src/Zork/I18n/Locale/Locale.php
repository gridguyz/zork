<?php

namespace Zork\I18n\Locale;

use Locale as IntlLocale;
use Zend\Stdlib\ArrayUtils;
use Zend\I18n\Exception;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Locale
{

    /**
     * Default locale
     *
     * @var string
     */
    protected $default = 'en';

    /**
     * Fallback locale (for translator)
     *
     * @var string
     */
    protected $fallback = 'en';

    /**
     * Available locales
     *
     * @var array
     */
    protected $available = array();

    /**
     * Get default locale
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set default locale
     *
     * @param string $locale
     * @return \Core\Service\Locale
     */
    public function setDefault( $locale )
    {
        $this->default = static::normalizeLocale( (string) $locale );
        return $this;
    }

    /**
     * Get fallback locale (for translator)
     *
     * @return string
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * Set fallback locale (for translator)
     *
     * @param string $locale
     * @return \Core\Service\Locale
     */
    public function setFallback( $locale )
    {
        $this->fallback = static::normalizeLocale( (string) $locale );
        return $this;
    }

    /**
     * Get available locales
     *
     * @return array
     */
    public function getAvailableLocales( $group = false )
    {
        $avail = array_keys( array_filter(
            empty( $this->available )
                ? array( $this->default => true )
                : $this->available
        ) );

        if ( $group )
        {
            $group = array();

            foreach ( $avail as $locale )
            {
                $group[IntlLocale::getPrimaryLanguage( $locale )][] = $locale;
            }

            $avail = $group;
        }

        return $avail;
    }

    /**
     * Get available flags of locales
     *
     * @return array
     */
    public function getAvailableFlags()
    {
        return $this->available;
    }

    /**
     * Get available locales
     *
     * @param array $locales
     * @return \Core\Service\Locale
     */
    public function setAvailable( $locales )
    {
        $this->available = array();

        foreach ( $locales as $locale => $enabled )
        {
            if ( is_numeric( $locale ) )
            {
                $locale     = $enabled;
                $enabled    = true;
            }

            $this->available[static::normalizeLocale($locale)] = $enabled;
        }

        if ( empty( $this->available[$this->default] ) )
        {
            $this->available[$this->default] = true;
        }

        return $this;
    }

    /**
     * Normalize locale
     *
     * @param string $locale
     * @return string
     */
    public static function normalizeLocale( $locale )
    {
        $parsed = IntlLocale::parseLocale( $locale );

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

    /**
     * Get current locale
     *
     * @return string
     */
    public function getCurrent()
    {
        return IntlLocale::getDefault();
    }

    /**
     * Set current locale
     *
     * @param string $locale
     * @return \Core\Service\Locale
     */
    public function setCurrent( $locale )
    {
        $locale = static::normalizeLocale( (string) $locale );
        $avail  = $this->getAvailableFlags();

        if ( ! empty( $avail[$locale] ) ||
             ! empty( $avail[$locale = IntlLocale::getPrimaryLanguage( $locale )] ) )
        {
            IntlLocale::setDefault( $locale );
        }

        return $this;
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
     * Get the most suitable locale from HTTP Accept-Language header
     *
     * @param string $header
     * @param array $available
     * @return string
     */
    public function acceptFromHttp( $header, array $available = null )
    {
        $normalized = null;

        if ( empty( $available ) )
        {
            $available = $this->getAvailableLocales();
        }

        while ( $header && ! $normalized )
        {
            $locale = IntlLocale::acceptFromHttp( $header );

            if ( $locale )
            {
                $normalized = static::normalizeLocale( $locale );

                if ( in_array( $normalized, $available ) )
                {
                    return $normalized;
                }

                $primary = IntlLocale::getPrimaryLanguage( $normalized );

                if ( in_array( $primary, $available ) )
                {
                    return $primary;
                }

                $header = preg_replace(
                    '/\s*(' . preg_quote( $locale, '/' ) . '|'
                            . preg_quote( $primary, '/' ) . ')'
                            . '\s*(;\s*q\s*=[^,]+\s*)?\s*,?\s*/',
                    '', $header
                );

                if ( preg_match( '/^\s*\*\s*(;\s*q\s*=[^,]+\s*)?$/', $header ) )
                {
                    $header = '';
                }
            }
            else
            {
                $normalized = null;
            }
        }

        return $this->getDefault();
    }

    /**
     * Factory method
     *
     * @param array|\Traversable $options
     * @throws \InvalidArgumentException
     */
    public static function factory( $options )
    {
        if ( $options instanceof \Traversable )
        {
            $options = ArrayUtils::iteratorToArray( $options );
        }
        elseif ( ! is_array( $options ) )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s expects an array or Traversable object; received "%s"',
                __METHOD__, (
                    is_object( $options )
                        ? get_class( $options )
                        : gettype( $options )
                )
            ) );
        }

        $locale = new static();

        if ( isset( $options['default'] ) )
        {
            $locale->setDefault( $options['default'] );
        }

        if ( isset( $options['fallback'] ) )
        {
            $locale->setFallback( $options['fallback'] );
        }

        if ( ! empty( $options['available'] ) )
        {
            $locale->setAvailable( $options['available'] );
        }

        if ( isset( $options['current'] ) )
        {
            $locale->setCurrent( $options['current'] );
        }

        return $locale;
    }

}
