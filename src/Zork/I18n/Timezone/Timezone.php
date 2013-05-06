<?php

namespace Zork\I18n\Timezone;

use Zend\I18n\Exception;
use Zend\Stdlib\ArrayUtils;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Timezone
{

    /**
     * Current timezone
     *
     * @var string
     */
    protected $current = 'UTC';

    /**
     * Get current timezone
     *
     * @return string
     */
    public function getCurrent()
    {
        return date_default_timezone_get();
    }

    /**
     * Set current timezone
     *
     * @param string $timezone
     * @return \Zork\I18n\Timezone\Timezone
     */
    public function setCurrent( $timezone )
    {
        if ( date_default_timezone_set( $timezone ) )
        {
            $this->current = $timezone;
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
                __METHOD__,
                is_object( $options ) ? get_class( $options ) : gettype( $options )
            ) );
        }

        $service = new static();

        if ( isset( $options['id'] ) )
        {
            $service->setCurrent( $options['id'] );
        }

        return $service;
    }

}
