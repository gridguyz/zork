<?php

namespace Zork\Stdlib;

use Zend\Math\Rand;
use Zend\Filter\Word;

/**
 * String
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class String
{

    /**
     * @var string
     */
    const DEFAULT_RANDOM_LENGTH = 12;

    /**
     * Default charset for the constructor
     *
     * @var string
     */
    const DEFAULT_ENCODING  = 'UTF-8';

    /**
     * @var string
     */
    const DEFAULT_RANDOM_CHARS  = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    /**
     * @var string
     */
    const DEFAULT_SEPARATOR     = '-';

    /**
     * Generate random string
     *
     * @param int $length
     * @param string $chars
     * @param bool $strong
     * @return string
     */
    public static function generateRandom( $length  = null,
                                           $chars   = null,
                                           $strong  = false )
    {
        return Rand::getString(
            $length ?: self::DEFAULT_RANDOM_LENGTH,
            $chars  ?: self::DEFAULT_RANDOM_CHARS,
            $strong
        );
    }

    /**
     * Camelize string
     *
     * @param  string   $value
     * @param  string   $separator          default: "-"
     * @param  bool     $firstCharIsLower   default: null
     *
     * @return string
     */
    public static function camelize( $value, $separator = null,
            $firstCharIsLower = null )
    {
        static $filter = null;

        if ( null === $filter )
        {
            $filter = new Word\SeparatorToCamelCase();
        }

        if ( null === $firstCharIsLower )
        {
            $firstCharIsLower = lcfirst( $value ) === $value;
        }

        $filtered = $filter->setSeparator( $separator ?: self::DEFAULT_SEPARATOR )
                           ->filter( $value );

        if ( true === $firstCharIsLower )
        {
            $filtered = lcfirst( $filtered );
        }
        else if ( false === $firstCharIsLower )
        {
            $filtered = ucfirst( $filtered );
        }

        return $filtered;
    }

    /**
     * Decamelize string
     *
     * @param  string   $value
     * @param  string   $separator    default: "-"
     *
     * @return string
     */
    public static function decamelize( $value, $separator = null )
    {
        static $filter = null;

        if ( null === $filter )
        {
            $filter = new Word\CamelCaseToSeparator();
        }

        $filtered = $filter->setSeparator( $separator ?: self::DEFAULT_SEPARATOR )
                           ->filter( $value );

        return mb_strtolower( $filtered, self::DEFAULT_ENCODING );
    }

    /**
     * Template a text
     *
     * @param   string              $text
     * @param   array|\Traversable  $variables
     * @param   string|callable     $key
     * @return  string
     */
    public static function template( $text, $variables, $key = '[%s]' )
    {
        if ( ! is_callable( $key ) )
        {
            $keyString = (string) $key;
            $key = function ( $str ) use ( $keyString ) {
                return sprintf( $keyString, $str );
            };
        }

        foreach ( $variables as $item => $value )
        {
            $text = str_ireplace( $key( $item ), $value, $text );
        }

        return $text;
    }

    /**
     * Strip html from text
     *
     * @param string $html
     * @param string $decodeEncoding
     * @return string
     */
    public static function stripHtml( $html,
                                      $decodeEncoding = self::DEFAULT_ENCODING )
    {
        static $blockElements = array(
            'p', 'li', 'div', 'pre', 'form', 'embed', 'table', 'thead', 'tbody',
            'tfoot', 't[dh]', 'd[td]', 'h[1-6]', '[uod]l', 'object', 'fieldset',
            'noscript', 'blockquote',
        );

        $result = preg_replace(
            array( '#<(script|style)(\s[^>]+)?>.*?</\\1>#',
                   '#<br(\s[^>]*)?>#', '#<hr(\s[^>]*)?>#',
                   '#</(' . implode( '|', $blockElements ) . ')>#',
                   '#<[^>]*>#', "#\s*\n\s*#", '#^\s+#', '#\s+$#', "#[^\S\n]+#" ),
            array( '', "\n", "\n-----\n", "\n", ' ', "\n", '', '', ' ' ),
            $html
        );

        return html_entity_decode( $result, ENT_QUOTES, $decodeEncoding );
    }

}
