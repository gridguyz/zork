<?php

namespace Zork\Stdlib;

use DateTime;

/**
 * JsValue
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class JsValue
{

    /**
     * @const int
     */
    const FLAGS_DEFAULT = 0;

    /**
     * @const int
     */
    const NUMERIC_CHECK = 1;

    /**
     * @const int
     */
    const BIGINT_AS_STRING = 2;

    /**
     * @const int
     */
    const OBJECT_AS_ARRAY = 4;

    /**
     * @const int
     */
    const ERROR_NONE = 0;

    /**
     * @const int
     */
    const ERROR_SYNTAX_ERROR = 1;

    /**
     * @const int
     */
    const ERROR_UNEXPECTED_DATA_AT_END = 2;

    /**
     * @const int
     */
    const ERROR_UNEXPECTED_END_OF_INPUT = 3;

    /**
     * @const int
     */
    const ERROR_UNKNOWN_CONSTRUCTOR = 4;

    /**
     * Last error
     *
     * @var int
     */
    protected $lastError = self::ERROR_NONE;

    /**
     * Get last error
     *
     * @return int
     */
    public function lastError()
    {
        $last = static::$lastError;
        static::$lastError = static::ERROR_NONE;
        return $last;
    }

    /**
     * Generate error
     *
     * @staticvar   array   $messages
     * @param       int     $code
     * @param       bool    $warning
     * @return      bool
     */
    protected function error( $code, $warning = false, $param = null )
    {
        static $messages = array(
            static::ERROR_SYNTAX_ERROR              => 'syntax error',
            static::ERROR_UNEXPECTED_DATA_AT_END    => 'unexpected characters at the end of input',
            static::ERROR_UNEXPECTED_END_OF_INPUT   => 'unexpeced end of input',
            static::ERROR_UNKNOWN_CONSTRUCTOR       => 'unknown constructor "%s"',
        );

        static::$lastError = $code;

        $message = __CLASS__ . ': ' . $messages[$code];

        if ( ! empty( $param ) )
        {
            $message = sprintf( $message, $param );
        }

        if ( $warning )
        {
            return trigger_error(
                $message,
                E_USER_WARNING
            );
        }

        throw new \RuntimeException( $message, $code );
    }

    /**
     * Decode JS values
     *
     * @param   string  $string
     * @return  mixed
     */
    public static function decode( $string, $flags = self::FLAGS_DEFAULT )
    {
        $string = (string) $string;
        static::consumeWhiteSpace( $string );
        $result = static::acceptValue( $string, (int) $flags );
        static::consumeWhiteSpace( $string );

        if ( $string )
        {
            static::error(
                static::ERROR_UNEXPECTED_DATA_AT_END,
                true
            );
        }

        return $result;
    }

    /**
     * Consume any whitespace
     *
     * @param   string  $string
     * @return  void
     */
    protected static function consumeWhiteSpace( &$string )
    {
        while ( true )
        {
            $string = preg_replace( '/^[\\s\\n]+/', '', $string );

            switch ( substr( $string, 0, 2 ) )
            {
                case '//':
                    $string = preg_replace( '/^\/\/.*?[\\n\\r]+/', '', $string );
                    break;

                case '/*':
                    $string = preg_replace( '/^\/\*.*?\*\//', '', $string );
                    break;

                default:
                    return;
            }
        }
    }

    /**
     * Accept any type of value
     *
     * @param   string  $string
     * @return  mixed
     */
    protected static function acceptValue( &$string, $flags )
    {
        if ( empty( $string ) )
        {
            static::error( static::ERROR_UNEXPECTED_END_OF_INPUT );
            return null;
        }

        $matches = array(); // keywords
        if ( preg_match( '/^(null|undefined|true|false|NaN|[+-]?Infinity)/', $string, $matches ) )
        {
            $string = substr( $string, strlen( $matches[0] ) );

            switch ( $matches[0] )
            {
                case 'true':
                    return true;

                case 'false':
                    return false;

                default:
                    return null;
            }
        }

        $first = $string[0];

        if ( is_numeric( $first ) || in_array( $first, array( '+', '-' ) ) )
        {
            return static::acceptNumber( $string, $flags );
        }

        if ( in_array( $first, array( '"', '\'' ) ) )
        {
            return static::acceptString( $string, $flags );
        }

        if ( '{' === $first )
        {
            return static::acceptObject( $string, $flags );
        }

        if ( '[' === $first )
        {
            return static::acceptArray( $string, $flags );
        }

        if ( '/' === $first )
        {
            return static::acceptRegExp( $string, $flags );
        }

        $matches = array();
        if ( preg_match( '/^new[\\s\\n]+(\\w+)/', $string, $matches ) )
        {
            $string = substr( $string, strlen( $matches[0] ) );
            return static::acceptConstructor( $string, $matches[1], $flags );
        }

        static::error( static::ERROR_SYNTAX_ERROR );
        return null;
    }

    /**
     * Accept a number
     *
     * @param   string  $string
     * @param   int     $flags
     * @return  int|float|null
     */
    protected static function acceptNumber( &$string, $flags )
    {
        $matches = array(); // decimal integers
        if ( preg_match( '/^[+-]?(0|[1-9][0-9]+)/', $string, $matches ) )
        {
            $string = substr( $string, strlen( $matches[0] ) );
            $int    = intval( $matches[0], 10 );

            if ( $int === PHP_INT_MAX || $int === ( ~PHP_INT_MAX ) )
            {
                if ( $flags & static::BIGINT_AS_STRING )
                {
                    return $matches[0];
                }
                else
                {
                    return floatval( $matches[0] );
                }
            }

            return $int;
        }

        $matches = array(); // hexa, octal and binary integers
        if ( preg_match( '/^([+-]?)(0[xX][0-9a-fA-F]+|0[0-7]+|0[bB][01]+)/', $string, $matches ) )
        {
            $string = substr( $string, strlen( $matches[0] ) );
            $int    = intval( $matches[2], 0 );

            if ( '-' == $matches[1] )
            {
                $int *= -1;
            }

            return $int;
        }

        $matches = array(); // floating point numbers
        if ( preg_match( '/^([+-]?)(\d+(\.\d+)?)([eE]([+-]?)(\d+))?/', $string, $matches ) )
        {
            $string = substr( $string, strlen( $matches[0] ) );
            $float  = floatval( $matches[2] );

            if ( '-' == $matches[1] )
            {
                $float *= -1;
            }

            if ( $matches[4] )
            {
                $exp = intval( $matches[6] );

                if ( '-' == $matches[5] )
                {
                    $exp *= -1;
                }

                $float *= pow( 10, $exp );
            }

            return $float;
        }

        static::error( static::ERROR_SYNTAX_ERROR );
        return null;
    }

    /**
     * Accept an escaped character sequence
     *
     * @param   string  $string
     * @param   string  $delim
     * @param   int     $flags
     * @return  string
     */
    protected static function acceptEscaped( &$string, $delim, $flags )
    {
        $result = '';

        while ( $string && $string[0] !== $delim )
        {
            if ( '\\' === $string[0] )
            {
                switch ( $string[1] )
                {
                    case 'b':
                        $result .= "\b";
                        $string  = substr( $string, 2 );
                        break;

                    case 'f':
                        $result .= "\f";
                        $string  = substr( $string, 2 );
                        break;

                    case 'n':
                        $result .= "\n";
                        $string  = substr( $string, 2 );
                        break;

                    case 'r':
                        $result .= "\r";
                        $string  = substr( $string, 2 );
                        break;

                    case 't':
                        $result .= "\t";
                        $string  = substr( $string, 2 );
                        break;

                    case 'u':

                        $matches = array();
                        if ( preg_match( '/^\\\\u([0-9a-fA-F]{4})/', $string, $matches ) )
                        {
                            $string  = substr( $string, 6 );
                            $result .= mb_convert_encoding( '&#x' . $matches[1] . ';', 'UTF-8', 'HTML-ENTITIES' );
                        }
                        else
                        {
                            static::error( static::ERROR_SYNTAX_ERROR );
                            return $string;
                        }

                        break;

                    default:
                        $result .= $string[1];
                        $string  = substr( $string, 2 );
                        break;
                }
            }
            else
            {
                $matches = array();
                if ( preg_match( '/^[^' . preg_quote( '\\' . $delim, '/' ) . ']+/', $string, $matches ) )
                {
                    $string  = substr( $string, strlen( $matches[0] ) );
                    $result .= $matches[0];
                }
            }
        }

        return $result;
    }

    /**
     * Accept a string
     *
     * @param   string  $string
     * @param   int     $flags
     * @return  string|null
     */
    protected static function acceptString( &$string, $flags )
    {
        $first = $string[0];

        if ( ! in_array( $first, array( '"', '\'' ) ) )
        {
            static::error( static::ERROR_SYNTAX_ERROR );
            return null;
        }

        $string = substr( $string, 1 );
        $result = static::acceptEscaped( $string, $first, $flags );

        if ( $first === $string[0] )
        {
            $string = substr( $string, 1 );
        }
        else
        {
            static::error( static::ERROR_SYNTAX_ERROR );
        }

        return $result;
    }

    /**
     * Accept a regexp
     *
     * @param   string  $string
     * @param   int     $flags
     * @return  object|array|null
     */
    protected static function acceptRegExp( &$string, $flags )
    {
        if ( '/' !== $string[0] || '/' === $string[1] )
        {
            static::error( static::ERROR_SYNTAX_ERROR );
            return null;
        }

        $string = substr( $string, 1 );
        $result = array(
            'pattern'   => static::acceptEscaped( $string, '/', $flags ),
            'flags'     => '',
        );

        if ( '/' === $string[0] )
        {
            $string = substr( $string, 1 );
        }
        else
        {
            static::error( static::ERROR_SYNTAX_ERROR );
            return null;
        }

        $matches = array();
        if ( preg_match( '^\w+', $string, $matches ) )
        {
            $string = substr( $string, strlen( $matches[0] ) );
            $result['flags'] = $matches[0];
        }

        if ( ! ( $flags & static::OBJECT_AS_ARRAY ) )
        {
            $result = (object) $result;
        }

        return $result;
    }

    /**
     * Accept an object
     *
     * @param   string  $string
     * @param   int     $flags
     * @return  object|array|null
     */
    protected static function acceptObject( &$string, $flags )
    {
        if ( '{' === $string[0] )
        {
            static::error( static::ERROR_SYNTAX_ERROR );
            return null;
        }

        $result = array();
        $string = substr( $string, 1 );
        static::consumeWhiteSpace( $string );

        while ( true )
        {
            switch ( $string[0] )
            {
                case '}':
                    $string = substr( $string, 1 );
                    break 2;

                case '"':
                case '\'':
                    $key = self::acceptString( $string, $flags );
                    break;

                default:

                    $matches = array();
                    if ( preg_match( '^\w+', $string, $matches ) )
                    {
                        $string = substr( $string, strlen( $matches[0] ) );
                        $key    = $matches[0];
                    }
                    else
                    {
                        static::error( static::ERROR_SYNTAX_ERROR );
                        return null;
                    }

                    break;
            }

            static::consumeWhiteSpace( $string );

            if ( ':' !== $string[0] )
            {
                static::error( static::ERROR_SYNTAX_ERROR );
                return null;
            }

            $string = substr( $string, 1 );
            static::consumeWhiteSpace( $string );
            $result[$key] = static::acceptValue( $string, $flags );
            static::consumeWhiteSpace( $string );

            switch ( $string[0] )
            {
                case ',':
                    $string = substr( $string, 1 );
                    static::consumeWhiteSpace( $string );
                    break;

                case '}':
                    break 2;

                default:
                    static::error( static::ERROR_SYNTAX_ERROR );
                    return null;
            }
        }

        if ( ! ( $flags & static::OBJECT_AS_ARRAY ) )
        {
            $result = (object) $result;
        }

        return $result;
    }

    /**
     * Accept an array
     *
     * @param   string  $string
     * @param   int     $flags
     * @return  object|array|null
     */
    protected static function acceptArray( &$string, $flags )
    {
        if ( '[' !== $string[0] )
        {
            static::error( static::ERROR_SYNTAX_ERROR );
            return null;
        }

        $value  = true;
        $result = array();
        $string = substr( $string, 1 );
        static::consumeWhiteSpace( $string );

        while ( true )
        {
            switch ( $string[0] )
            {
                case ']':
                    $string = substr( $string, 1 );
                    break 2;

                case ',':
                    if ( ! $value )
                    {
                        $result[] = null;
                    }

                    $value  = false;
                    $string = substr( $string, 1 );
                    static::consumeWhiteSpace( $string );
                    continue 2;

                default:
                    $value    = true;
                    $result[] = static::acceptValue( $string, $flags );
                    break;
            }
        }

        return $result;
    }

    /**
     * Accept a constructor
     *
     * @param   string  $string
     * @param   int     $flags
     * @return  object|array|null
     */
    protected static function acceptConstructor( &$string, $class, $flags )
    {
        $args = array();

        if ( '(' === $string[0] )
        {
            $string = substr( $string, 1 );
            static::consumeWhiteSpace( $string );

            while ( ')' !== $string[0] )
            {
                $args[] = static::acceptValue( $string, $flags );
                static::consumeWhiteSpace( $string );

                if ( ',' === $string[0] )
                {
                    $string = substr( $string, 1 );
                    static::consumeWhiteSpace( $string );
                }
                else
                {
                    break;
                }
            }

            if ( ')' === $string[0] )
            {
                $string = substr( $string, 1 );
            }
            else
            {
                static::error( static::ERROR_SYNTAX_ERROR );
                return null;
            }
        }

        switch ( $class )
        {
            case 'Number':
                return isset( $args[0] ) ? (float) $args[0] : 0;

            case 'String':
                return isset( $args[0] ) ? (string) $args[0] : '';

            case 'Boolean':
                return isset( $args[0] ) ? (bool) $args[0] : false;

            case 'Object':
                return isset( $args[0] ) ? $args[0] : null;

            case 'Array':
                return count( $args ) === 1 && is_int( $args[0] )
                    ? array_fill( 0, $args[0], null )
                    : $args;

            case 'Date':
                switch ( true )
                {
                    case empty( $args ):
                        $time = 'now';
                        break;

                    case count( $args ) === 1:
                        if ( is_int( $args[0] ) || is_float( $args[0] ) )
                        {
                            $time = '@' . ( $args[0] / 1000 );
                        }
                        else
                        {
                            $time = (string) $args[0];
                        }
                        break;

                    default:
                        $time = str_pad( (int) $args[0], 4, '0', STR_PAD_LEFT )
                            . '-' . ( empty( $args[1] ) ? '01' : str_pad( 1   + $args[1], 2, '0', STR_PAD_LEFT ) )
                            . '-' . ( empty( $args[2] ) ? '01' : str_pad( (int) $args[2], 2, '0', STR_PAD_LEFT ) )
                            . ' ' . ( empty( $args[3] ) ? '00' : str_pad( (int) $args[3], 2, '0', STR_PAD_LEFT ) )
                            . ':' . ( empty( $args[4] ) ? '00' : str_pad( (int) $args[4], 2, '0', STR_PAD_LEFT ) )
                            . ':' . ( empty( $args[5] ) ? '00' : str_pad( (int) $args[5], 2, '0', STR_PAD_LEFT ) )
                            . '.' . ( empty( $args[6] ) ? '00' : str_pad( (int) $args[6], 2, '0', STR_PAD_LEFT ) );
                }

                return new DateTime( $time );

            case 'RegExp':
                $result = array(
                    'pattern'   => isset( $args[0] ) ? $args[0] : '',
                    'flags'     => isset( $args[1] ) ? $args[1] : '',
                );
                break;

            default:
                static::error( static::ERROR_UNKNOWN_CONSTRUCTOR, false, $class );
                return null;
        }

        if ( ! ( $flags & static::OBJECT_AS_ARRAY ) )
        {
            $result = (object) $result;
        }

        return $result;
    }

}
