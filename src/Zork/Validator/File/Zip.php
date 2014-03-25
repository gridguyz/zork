<?php

namespace Zork\Validator\File;

use ZipArchive;
use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Zip
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Zip extends AbstractValidator
{

    /**
     * @const string
     */
    const CANNOT_BE_OPENED = 'zipCannotBeOpened';

    /**
     * @const string
     */
    const ENTRY_NOT_FOUND  = 'zipEntryNotFound';

    /**
     * @var array Error message templates
     */
    protected $messageTemplates = array(
        self::CANNOT_BE_OPENED  => 'validate.file.zip.cannotBeOpened',
        self::ENTRY_NOT_FOUND   => 'validate.file.zip.entryNotFound.%entry%',
    );

    /**
     * @var array
     */
    protected $options = array(
        'entry'     => null,
        'entries'   => array(),
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'entry' => array(
            'options' => 'entry',
        ),
    );

    /**
     * Sets validator options
     *
     * @param  array|Traversable $entries
     */
    public function __construct( $entries = null )
    {
        if ( $entries instanceof Traversable )
        {
            $entries = ArrayUtils::iteratorToArray( $entries );
        }
        else if ( 1 < func_num_args() )
        {
            $entries = func_get_args();
        }
        else
        {
            $entries = (array) $entries;
        }

        if ( ! array_key_exists( 'entries', $entries ) )
        {
            $entries = array( 'entries' => $entries );
        }

        parent::__construct( $entries );
    }

    /**
     * Returns the (last-tested) entry
     *
     * @return  string
     */
    public function getEntry()
    {
        return $this->options['entry'];
    }

    /**
     * Returns entries
     *
     * @return  string[]
     */
    public function getEntries()
    {
        return $this->options['entries'];
    }

    /**
     * Sets the entries to test
     *
     * @param   array|Traversable    $entries
     * @return  Zip
     */
    public function setEntries( $entries )
    {
        if ( $entries instanceof Traversable )
        {
            $entries = ArrayUtils::iteratorToArray( $entries );
        }
        else
        {
            $entries = (array) $entries;
        }

        $this->options['entries'] = $entries;
        return $this;
    }

    /**
     * Normalize entry
     *
     * @param   string  $entry
     * @return  string
     */
    protected function normalizeEntry( $entry )
    {
        $matches = array();
        $entry   = str_replace( '\\', '/', $entry );

        while ( $entry && preg_match( '#^\.{0,2}/+#', $entry, $matches ) )
        {
            $entry = substr( $entry, strlen( $matches[0] ) );
        }

        return $entry;
    }

    /**
     * Returns true if and only if the file extension of $value is included in the
     * set extension list
     *
     * @param  string|array $value Real file to check for extension
     * @param  array        $file  File data from \Zend\File\Transfer\Transfer (optional)
     * @return bool
     */
    public function isValid( $value, $file = null )
    {
        if ( is_string( $value ) && is_array( $file ) )
        {
            // Legacy Zend\Transfer API support
            $filename = $file['name'];
            $file     = $file['tmp_name'];
        }
        elseif ( is_array( $value ) )
        {
            if ( ! isset( $value['tmp_name'] ) || ! isset( $value['name'] ) )
            {
                throw new Exception\InvalidArgumentException(
                    'Value array must be in $_FILES format'
                );
            }

            $file     = $value['tmp_name'];
            $filename = $value['name'];
        }
        else
        {
            $file     = $value;
            $filename = basename( $file );
        }

        $this->setValue( $filename );

        if ( false === stream_resolve_include_path( $file ) )
        {
            $this->error( self::CANNOT_BE_OPENED );
            return false;
        }

        $zip = new ZipArchive;

        if ( true !== $zip->open( $file, ZipArchive::CHECKCONS ) )
        {
            $this->error( self::CANNOT_BE_OPENED );
            return false;
        }

        foreach ( $this->getEntries() as $entry => $flags )
        {
            if ( is_numeric( $entry ) )
            {
                $entry  = $flags;
                $flags  = ZipArchive::FL_NOCASE;
            }

            $entry  = $this->normalizeEntry( $entry );
            $stats  = $zip->statName( $entry, $flags );

            if ( ! isset( $stats['index'] ) )
            {
                $this->options['entry'] = $entry;
                $this->error( self::ENTRY_NOT_FOUND );
                return false;
            }
        }

        return true;
    }
}
