<?php

namespace Zork\Data\Export;

use Traversable;

/**
 * Csv
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Csv extends TabularAbstract
{

    /**
     * @const string
     */
    const DEFAULT_MIMETYPE  = 'text/csv; charset=utf-8';

    /**
     * @const string
     */
    const DEFAULT_EOL       = PHP_EOL;

    /**
     * @const string
     */
    const DEFAULT_SEPARATOR = ',';

    /**
     * @var string
     */
    protected $eol = self::DEFAULT_EOL;

    /**
     * @var string
     */
    protected $separator = self::DEFAULT_SEPARATOR;

    /**
     * @var array
     */
    protected static $eolAliases = array(
        'windows'   => "\n\r",
        'linux'     => "\n",
        'macos'     => "\r",
    );

    /**
     * @var array
     */
    protected static $separatorAliases = array(
        'comma'     => ',',
        'semicolon' => ';',
        'tab'       => "\t",
    );

    /**
     * @return string
     */
    public function getEol()
    {
        return $this->eol;
    }

    /**
     * @param   string  $value
     * @return  \Zork\Data\File\Csv
     */
    public function setEol( $value )
    {
        if ( isset( static::$eolAliases[$value] ) )
        {
            $value = static::$eolAliases[$value];
        }

        $this->eol = ( (string) $value ) ?: static::DEFAULT_EOL;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * @param   string  $value
     * @return  \Zork\Data\File\Csv
     */
    public function setSeparator( $value )
    {
        if ( isset( static::$separatorAliases[$value] ) )
        {
            $value = static::$separatorAliases[$value];
        }

        $this->separator = ( (string) $value ) ?: static::DEFAULT_SEPARATOR;
        return $this;
    }

    /**
     * Encode a single value
     *
     * @param   string|array|Traversable2   $value
     * @return  string
     */
    protected function encodeValue( $value )
    {
        if ( empty( $value ) )
        {
            return '';
        }

        if ( $value instanceof Traversable )
        {
            $value = iterator_to_array( $value );
        }

        if ( is_array( $value ) )
        {
            $value = implode( $this->eol, $value );
        }
        else
        {
            $value = (string) $value;
        }

        if ( $value !== 'ID' && // thank you, MS-links
             ! preg_match( '/[\\s\\n\\r,;"]/', $value ) &&
             false === strpos( $value, $this->separator ) &&
             false === strpos( $value, $this->eol ) )
        {
            return $value;
        }

        return '"' . str_replace( '"', '""', $value ) . '"';
    }

    /**
     * Encode a row
     *
     * @param   array|\Traversable  $row
     * @return  string
     */
    protected function encodeRow( $row )
    {
        if ( $row instanceof Traversable )
        {
            $row = iterator_to_array( $row );
        }

        return implode( $this->separator,
                        array_map( array( $this, 'encodeValue' ),
                                   (array) $row ) )
             . $this->eol;
    }

}
