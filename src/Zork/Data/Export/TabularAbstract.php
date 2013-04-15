<?php

namespace Zork\Data\Export;

use Traversable;
use Zork\Data\FileData;
use Zork\Data\TabularInterface;

/**
 * TabularTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class TabularAbstract extends FileData
{

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $fieldsNames = array();

    /**
     * @var bool
     */
    protected $sendHeaders = true;

    /**
     * @var bool
     */
    protected $headersSent = false;

    /**
     * @return array
     */
    public function getFieldNames()
    {
        return $this->fieldNames;
    }

    /**
     * @param   array|\Traversable  $value
     * @return  \Zork\Data\File\Csv
     */
    public function setFieldNames( $value )
    {
        if ( $value instanceof Traversable )
        {
            $value = iterator_to_array( $value );
        }

        $this->fieldNames = (array) $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function getSendHeaders()
    {
        return $this->sendHeaders;
    }

    /**
     * @param   string  $value
     * @return  \Zork\Data\File\Csv
     */
    public function setSendHeaders( $value )
    {
        $this->sendHeaders = (bool) $value;
        return $this;
    }

    /**
     * Constructor
     *
     * @param   TabularInterface    $iterator
     * @param   array|null          $options
     */
    public function __construct( TabularInterface $iterator, $options = null )
    {
        $this->fields = $iterator->getFieldNames();
        parent::__construct( $iterator, $options );
    }

    /**
     * Encode a row
     *
     * @param   array|\Traversable  $row
     * @return  string
     */
    abstract protected function encodeRow( $row );

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->headersSent = false;
        return parent::rewind();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function valid()
    {
        return parent::valid() || ( $this->sendHeaders && ! $this->headersSent );
    }

    /**
     * Return the key of the current element
     *
     * @return scalar scalar on success, or <b>NULL</b> on failure.
     */
    public function key()
    {
        if ( $this->sendHeaders && ! $this->headersSent )
        {
            return -1;
        }

        return parent::key();
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        if ( $this->sendHeaders && ! $this->headersSent )
        {
            $data = $this->fieldNames;
        }
        else
        {
            $data = parent::current();
        }

        $fields = array();

        foreach ( $this->fields as $field )
        {
            $fields[] = $data[$field];
        }

        return $this->encodeRow( $fields );
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        if ( $this->sendHeaders && ! $this->headersSent )
        {
            $this->headersSent = true;
            return;
        }

        return parent::next();
    }

}
