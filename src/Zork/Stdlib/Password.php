<?php

namespace Zork\Stdlib;

use Zend\Math\Rand;

/**
 * Password
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Password
{

    /**
     * Default algorithm (currently: blowfish)
     *
     * @const int
     */
    const ALGO_DEFAULT = 0;

    /**
     * Blowfish algorithm
     *
     * @const int
     */
    const ALGO_BCRYPT = 1;

    /**
     * Generate random salt for an algo-type
     *
     * @param   int     $algo
     * @return  string
     * @throws  \InvalidArgumentException
     */
    public static function salt( $algo = self::ALGO_DEFAULT )
    {
        if ( empty( $algo ) )
        {
            $algo = self::ALGO_DEFAULT;
        }

        switch ( $algo )
        {
            case self::ALGO_DEFAULT:
            case self::ALGO_BCRYPT:

                return Rand::getString(
                    22,
                    './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
                    true
                );

            default:

                throw new \InvalidArgumentException( sprintf(
                    '%s: algorithm #%d not supported',
                    __METHOD__,
                    $algo
                ) );
        }
    }

    /**
     * Generate password-hash
     *
     * @param   string  $password
     * @param   int     $algo
     * @param   array   $options
     * @return  string
     * @throws  \InvalidArgumentException
     */
    public static function hash( $password,
                                 $algo          = self::ALGO_DEFAULT,
                                 array $options = array() )
    {
        if ( function_exists( 'password_hash' ) )
        {
            static $algoMask = array(
                self::ALGO_DEFAULT  => PASSWORD_DEFAULT,
                self::ALGO_BCRYPT   => PASSWORD_BCRYPT,
            );

            return password_hash(
                $password,
                array_key_exists( $algo, $algoMask )
                    ? $algoMask[$algo]
                    : PASSWORD_DEFAULT,
                $options
            );
        }

        if ( empty( $algo ) )
        {
            $algo = self::ALGO_DEFAULT;
        }

        if ( empty( $options['salt'] ) )
        {
            $options['salt'] = static::salt( $algo );
        }

        switch ( $algo )
        {
            case self::ALGO_DEFAULT:
            case self::ALGO_BCRYPT:

                if ( ! defined( 'CRYPT_BLOWFISH' ) )
                {
                    throw new \RuntimeException( sprintf(
                        '%s: CRYPT_BLOWFISH algorithm must be enabled',
                        __METHOD__
                    ) );
                }

                $cost = isset( $options['cost'] ) ? min( 31, max( 4, (int) $options['cost'] ) ) : 7;
                $salt = ( version_compare( PHP_VERSION, '5.3.7' ) >= 0 ? '$2y' : '$2a' ) . '$'
                      . str_pad( $cost, 2, '0', STR_PAD_LEFT ) . '$' . $options['salt'] . '$';

                break;

            default:

                throw new \InvalidArgumentException( sprintf(
                    '%s: algorithm #%d not supported',
                    __METHOD__,
                    $algo
                ) );
        }

        return crypt( $password, $salt );
    }

    /**
     * Verify password & its hash matches
     *
     * @param   string  $password
     * @param   string  $hash
     * @return  boolean
     */
    public static function verify( $password, $hash )
    {
        if ( function_exists( 'password_verify' ) )
        {
            return password_verify( $password, $hash );
        }

        return crypt( $password, $hash ) == $hash;
    }

    /**
     * Is the password needs re-hashing?
     *
     * @param   string  $hash
     * @param   int     $algo
     * @param   array   $options
     * @return  boolean
     * @throws  \InvalidArgumentException
     */
    public static function needsRehash( $hash,
                                        $algo          = self::ALGO_DEFAULT,
                                        array $options = array() )
    {
        if ( function_exists( 'password_needs_rehash' ) )
        {
            return password_verify( $hash, $algo, $options );
        }

        if ( empty( $algo ) )
        {
            $algo = self::ALGO_DEFAULT;
        }

        switch ( $algo )
        {
            case self::ALGO_DEFAULT:
            case self::ALGO_BCRYPT:

                if ( ! defined( 'CRYPT_BLOWFISH' ) )
                {
                    throw new \RuntimeException( sprintf(
                        '%s: CRYPT_BLOWFISH algorithm must be enabled',
                        __METHOD__
                    ) );
                }

                $type = version_compare( PHP_VERSION, '5.3.7' ) >= 0 ? '$2y$' : '$2a$';

                if ( $type != substr( $hash, 0, 4 ) )
                {
                    return true;
                }

                if ( isset( $options['cost'] ) &&
                     ( (int) $options['cost'] ) != ( (int) substr( $hash, 4, 2 ) ) )
                {
                    return true;
                }

                if ( isset( $options['salt'] ) &&
                     ( (string) $options['salt'] ) != substr( $hash, 7, 22 ) )
                {
                    return true;
                }

                break;

            default:

                throw new \InvalidArgumentException( sprintf(
                    '%s: algorithm #%d not supported',
                    __METHOD__,
                    $algo
                ) );
        }

        return false;
    }

    /**
     * Get info about a hash
     *
     * @param   string  $hash
     * @return  array
     */
    public static function getInfo( $hash )
    {
        if ( function_exists( 'password_get_info' ) )
        {
            static $algoMask = array(
                PASSWORD_DEFAULT    => self::ALGO_DEFAULT,
                PASSWORD_BCRYPT     => self::ALGO_BCRYPT,
            );

            $info = password_get_info( $hash );

            if ( isset( $algoMask[$info['algo']] ) )
            {
                $info['algo'] = $algoMask[$info['algo']];
            }

            return $info;
        }

        if ( in_array( substr( $hash, 0, 4 ), array( '$2a$', '$2x$', '$2y$' ) ) )
        {
            return array(
                'algo'      => self::ALGO_BCRYPT,
                'algoName'  => 'Blowfish',
                'options'   => array(
                    'cost'  => (int) substr( $hash, 4, 2 ),
                    'salt'  => substr( $hash, 7, 22 ),
                ),
            );
        }

        return null;
    }
}
