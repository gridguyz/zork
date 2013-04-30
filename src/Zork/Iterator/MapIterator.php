<?php

namespace Zork\Iterator;

use Countable;
use Traversable;
use OuterIterator;
use IteratorAggregate;

/**
 * MapIterator
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class MapIterator implements Countable, OuterIterator
{

    /**
     * @const int
     */
    const FLAG_GENERATE_KEYS = 1;

    /**
     * @var int
     */
    private $keySeq     = 0;

    /**
     * @var int
     */
    protected $flags    = 0;

    /**
     * @var \Iterator
     */
    protected $innerIterator;

    /**
     * Maps a value to a new one
     *
     * @param   mixed       $value
     * @param   int|string  $key
     * @return  mixed
     */
    abstract public function map( $value, $key );

    /**
     * Constructor
     *
     * @param   Traversable $innerIterator
     * @param   int         $flags
     */
    public function __construct( Traversable $innerIterator, $flags = 0 )
    {
        if ( $innerIterator instanceof IteratorAggregate )
        {
            $innerIterator = $innerIterator->getIterator();
        }

        $this->innerIterator = $innerIterator;
        $this->flags = (int) $flags;
    }

    /**
     * Returns the inner iterator for the current entry.
     *
     * @return Iterator The inner iterator for the current entry.
     */
    public function getInnerIterator()
    {
        return $this->innerIterator;
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->map(
            $this->innerIterator->current(),
            $this->innerIterator->key()
        );
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->keySeq++;
        return $this->innerIterator->next();
    }

    /**
     * Return the key of the current element
     *
     * @return scalar scalar on success, or <b>NULL</b> on failure.
     */
    public function key()
    {
        if ( $this->flags & self::FLAG_GENERATE_KEYS )
        {
            return $this->keySeq;
        }

        return $this->innerIterator->key();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function valid()
    {
        return $this->innerIterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->keySeq = 0;
        return $this->innerIterator->rewind();
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count( $this->innerIterator );
    }

}
