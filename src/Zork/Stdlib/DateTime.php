<?php

namespace Zork\Stdlib;

use ArrayAccess;
use Traversable;
use DateTimeZone;
use DateTime as Base;
use Zend\Stdlib\ArrayUtils;

/**
 * DateTime
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DateTime extends Base implements ArrayAccess
{

    /**
     * @const string
     */
    const CONVERT = 'Y-m-d\TH:i:s.uO';

    /**
     * @var string
     */
    protected $defaultFormat = self::ISO8601;

    /**
     * @return string
     */
    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }

    /**
     * Set default format
     *
     * @param   string $defaultFormat
     * @param   bool $canUseConstant
     * @return  \Zork\Stdlib\DateTime
     */
    public function setDefaultFormat( $defaultFormat, $canUseConstant = true )
    {
        $defaultFormat = (string) $defaultFormat;
        $constant      = get_class( $this ) . '::' .
                         strtoupper( $defaultFormat );

        if ( $canUseConstant && defined( $constant ) )
        {
            $defaultFormat = constant( $constant );
        }

        $this->defaultFormat = $defaultFormat;
        return $this;
    }

    /**
     * Convert to string (using default format)
     *
     * @return  string
     */
    public function __toString()
    {
        return (string) $this->format( $this->defaultFormat );
    }

    /**
     * Convert to hash
     *
     * @return string
     */
    public function toHash()
    {
        return base_convert( $this->format( 'Uu' ), 10, 36 );
    }

    /**
     * Set state
     *
     * @param   array   $array
     * @return  DateTime
     */
    public static function __set_state( $array )
    {
        $date = parent::__set_state( $array );
        return new static( $date->format( static::CONVERT ) );
    }

    /**
     * Create from format
     *
     * @param   string          $format
     * @param   string          $time
     * @param   DateTimeZone    $timezone
     * @return  DateTime
     */
    public static function createFromFormat( $format, $time, $timezone = null )
    {
        $date = $timezone
            ? parent::createFromFormat( $format, $time, $timezone )
            : parent::createFromFormat( $format, $time );

        return new static( $date->format( static::CONVERT ) );
    }

    /**
     * Create a DateTime object from various formats
     *
     * @param   DateTime|\DateTime|string|int|null  $dateTime
     * @param   bool                                $nullOnEmpty
     * @return  DateTime|null
     */
    public static function create( $dateTime, $nullOnEmpty = false )
    {
        if ( $nullOnEmpty && empty( $dateTime ) )
        {
            return null;
        }
        else if ( $dateTime instanceof \DateTime )
        {
            return new static( $dateTime->format( static::CONVERT ) );
        }
        else if ( is_numeric( $dateTime ) )
        {
            return new static( '@' . $dateTime );
        }

        return new static( (string) $dateTime );
    }

    /**
     * Whether a offset exists
     * @codeCoverageIgnore
     *
     * @param   mixed   $offset An offset to check for.
     * @return  boolean         <b>TRUE</b> on success or <b>FALSE</b> on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists( $offset )
    {
        $format = $this->__toString();
        return isset( $format[$offset] );
    }

    /**
     * Offset to retrieve
     * @codeCoverageIgnore
     *
     * @param   mixed   $offset The offset to retrieve.
     * @return  mixed           Can return all value types.
     */
    public function offsetGet( $offset )
    {
        $format = $this->__toString();
        return $format[$offset];
    }

    /**
     * Offset to set
     * @codeCoverageIgnore
     *
     * @param   mixed   $offset The offset to assign the value to.
     * @param   mixed   $value  The value to set.
     * @return  void            No value is returned.
     * @throws  \UnderflowException
     */
    public function offsetSet( $offset, $value )
    {
        throw new \UnderflowException( 'The DateTime representaion is read-only' );
    }

    /**
     * Offset to unset
     * @codeCoverageIgnore
     *
     * @param   mixed   $offset The offset to unset.
     * @return  void            No value is returned.
     * @throws  \UnderflowException
     */
    public function offsetUnset( $offset )
    {
        throw new \UnderflowException( 'The DateTime representaion is read-only' );
    }

    /**
     * Find the minimum of the dates
     *
     * @param   array|\DateTime $dates
     * @param   \DateTime       $...
     * @return  \DateTime
     */
    public static function min( $dates )
    {
        if ( $dates instanceof Traversable )
        {
            $dates = ArrayUtils::iteratorToArray( $dates );
        }

        if ( ! is_array( $dates ) )
        {
            $dates = func_get_args();
        }

        $min = null;

        foreach ( $dates as $date )
        {
            if ( empty( $date ) )
            {
                continue;
            }

            if ( is_scalar( $date ) )
            {
                $date = new static(
                    is_int( $date )
                        ? '@' . intval( $date )
                        : $date
                );
            }

            if ( $date instanceof \DateTime )
            {
                if ( null === $min || $date < $min )
                {
                    $min = $date;
                }
            }
        }

        return $min;
    }

    /**
     * Find the maximum of the dates
     *
     * @param   array|\DateTime $dates
     * @param   \DateTime       $...
     * @return  \DateTime
     */
    public static function max( $dates )
    {
        if ( $dates instanceof Traversable )
        {
            $dates = ArrayUtils::iteratorToArray( $dates );
        }

        if ( ! is_array( $dates ) )
        {
            $dates = func_get_args();
        }

        $max = null;

        foreach ( $dates as $date )
        {
            if ( empty( $date ) )
            {
                continue;
            }

            if ( is_scalar( $date ) )
            {
                $date = new static(
                    is_int( $date )
                        ? '@' . intval( $date )
                        : $date
                );
            }

            if ( $date instanceof \DateTime )
            {
                if ( null === $max || $date > $max )
                {
                    $max = $date;
                }
            }
        }

        return $max;
    }

}
