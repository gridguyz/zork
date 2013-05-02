<?php

namespace Zork\Data;

use Iterator;
use ArrayIterator;
use OuterIterator;
use IteratorAggregate;
use Zork\Stdlib\OptionsTrait;

/**
 * File
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FileData implements Iterator,
                          OuterIterator,
                          FileInterface
{

    use OptionsTrait;

    /**
     * @const string
     */
    const DEFAULT_MIMETYPE = 'application/octet-stream';

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var Iterator
     */
    protected $iterator;

    /**
     * Constructor
     *
     * @param   array|Traversable   $iterator
     * @param   array|null          $options
     */
    public function __construct( $iterator, $options = null )
    {
        if ( is_array( $iterator ) )
        {
            $iterator = new ArrayIterator( $iterator );
        }

        while ( $iterator instanceof IteratorAggregate )
        {
            $iterator = $iterator->getIterator();
        }

        if ( ! $iterator instanceof Iterator )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s: $iterator must be an array, or an instance of \Traversable',
                __METHOD__
            ) );
        }

        $this->iterator = $iterator;
        $this->mimeType = static::DEFAULT_MIMETYPE;

        if ( $options !== null )
        {
            $this->setOptions( $options );
        }
    }

    /**
     * Get mime-type of the file
     *
     * @return  string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set mime-type of the file
     *
     * @param   string  $value
     * @return  \Zork\Data\FileData
     */
    public function setMimeType( $value )
    {
        $this->mimeType = ( (string) $value ) ?: static::DEFAULT_MIMETYPE;
        return $this;
    }

    /**
     * Get data-iterator
     *
     * @return Iterator
     */
    public function getInnerIterator()
    {
        return $this->iterator;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        return $this->iterator->rewind();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Return the key of the current element
     *
     * @return scalar scalar on success, or <b>NULL</b> on failure.
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        return $this->iterator->next();
    }

}
