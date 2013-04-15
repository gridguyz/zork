<?php

namespace Zork\Model;

/**
 * LocaleTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface LocaleAwareInterface
{

    /**
     * Get language parameter for model
     *
     * @return string
     */
    public function getDefaultLocale();

    /**
     * Get language parameter for model
     *
     * @return string
     */
    public function getPrimaryLanguage();

    /**
     * Get locale parameter for model
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set locale parameter for model
     *
     * @param string $locale
     * @return self
     */
    public function setLocale( $locale = null );

}
