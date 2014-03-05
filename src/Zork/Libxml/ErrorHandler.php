<?php

namespace Zork\Libxml;

use Exception;
use LibXMLError;
use ErrorException;

/**
 * ErrorHandler
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class ErrorHandler
{

    /**
     * @const string|int
     */
    const OTHER_ERROR_LEVEL_KEY = 'other';

    /**
     * Error level transitions
     *
     * @var int[mixed]
     */
    protected static $errorLevels = array(
        LIBXML_ERR_NONE             => 0,
        LIBXML_ERR_WARNING          => E_WARNING,
        LIBXML_ERR_ERROR            => E_RECOVERABLE_ERROR,
        LIBXML_ERR_FATAL            => E_ERROR,
        self::OTHER_ERROR_LEVEL_KEY => E_USER_ERROR,
    );

    /**
     * Error message formats
     *
     * @var string[mixed]
     */
    protected static $errorMessages = array(
        LIBXML_ERR_NONE             => 'Libxml #%2$d: %1$s',
        LIBXML_ERR_WARNING          => 'Libxml warning #%2$d: %1$s',
        LIBXML_ERR_ERROR            => 'Libxml error #%2$d: %1$s',
        LIBXML_ERR_FATAL            => 'Libxml fatal error #%2$d: %1$s',
        self::OTHER_ERROR_LEVEL_KEY => 'Libxml unknown error (level %3$d) #%2$d: %1$s',
    );

    /**
     * Error stack
     *
     * @var ErrorException[]
     */
    protected static $stack = array();

    /**
     * "Internal errors" state, before the first start()
     *
     * @var bool
     */
    protected static $internalErrorsBefore = null;

    /**
     * Check if this error handler is active
     *
     * @return  bool
     */
    public static function started()
    {
        return (bool) static::getNestedLevel();
    }

    /**
     * Get the current nested level
     *
     * @return  int
     */
    public static function getNestedLevel()
    {
        return count( static::$stack );
    }

    /**
     * Starting the error handler
     *
     * @return  void
     */
    public static function start()
    {
        if ( static::$stack )
        {
            static::checkErrors();
        }
        else
        {
            static::$internalErrorsBefore = libxml_use_internal_errors( true );
        }

        static::$stack[] = null;
    }

    /**
     * Stopping the error handler
     *
     * @param   bool    $throw  Throw errors as ErrorException if any
     * @return  null|ErrorException
     * @throws  ErrorException  If an error has been catched and $throw is true
     */
    public static function stop( $throw = false )
    {
        $errorException = null;
        static::checkErrors();

        if ( static::$stack )
        {
            $errorException = array_pop( static::$stack );

            if ( ! static::$stack )
            {
                libxml_use_internal_errors( static::$internalErrorsBefore );
                static::$internalErrorsBefore = null;
            }

            if ( $errorException && $throw )
            {
                throw $errorException;
            }
        }

        return $errorException;
    }

    /**
     * Stop all active handler
     *
     * @return  void
     */
    public static function clean()
    {
        if ( static::$stack )
        {
            libxml_get_errors(); // discard all pending errors
            libxml_use_internal_errors( static::$internalErrorsBefore );
            static::$internalErrorsBefore = null;
        }

        static::$stack = array();
    }

    /**
     * Check for errors
     *
     * @return  void
     */
    public static function checkErrors()
    {
        $errors = libxml_get_errors();

        while ( $errors )
        {
            static::addError( array_shift( $errors ) );
        }
    }

    /**
     * Add an error to the stack
     *
     * @param   LibXMLError $error
     * @return  void
     */
    public static function addError( LibXMLError $error )
    {
        $stack = &static::$stack[ count( static::$stack ) - 1 ];
        $stack = static::convertToException( $error, $stack );
    }

    /**
     * Convert LibXMLError to Exception
     *
     * @param   LibXMLError $error
     * @param   Exception   $previous
     * @return  ErrorException
     */
    public static function convertToException( LibXMLError  $error,
                                               Exception    $previous = null )
    {
        return new ErrorException(
            sprintf(
                static::$errorMessages[
                    isset( static::$errorMessages[$error->level] )
                        ? $error->level
                        : static::OTHER_ERROR_LEVEL_KEY
                ],
                $error->message,
                $error->code,
                $error->level
            ),
            $error->code,
            static::$errorLevels[
                isset( static::$errorLevels[$error->level] )
                    ? $error->level
                    : static::OTHER_ERROR_LEVEL_KEY
            ],
            $error->file,
            $error->line,
            $previous
        );
    }

}
