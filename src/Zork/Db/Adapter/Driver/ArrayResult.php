<?php

namespace Zork\Db\Adapter\Driver;

use ArrayIterator;
use IteratorIterator;
use Zend\Db\Adapter\Driver\ResultInterface;

/**
 * ArrayResult
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ArrayResult extends IteratorIterator
               implements ResultInterface
{

    /**
     * Create an iterator from anything that is traversable or array
     *
     * @param array|\Traversable  $iterator
     */
    public function __construct( $iterator )
    {
        if ( is_array( $iterator ) )
        {
            $iterator = new ArrayIterator( $iterator );
        }

        parent::__construct( $iterator );
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int
     */
    public function count()
    {
        return count( $this->getInnerIterator() );
    }

    /**
     * Force buffering
     *
     * @return void
     */
    public function buffer()
    {
        // noop
    }

    /**
     * Check if is buffered
     *
     * @return bool|null
     */
    public function isBuffered()
    {
        return true;
    }

    /**
     * Is query result?
     *
     * @return bool
     */
    public function isQueryResult()
    {
        return $this->count() > 0;
    }

    /**
     * Get affected rows
     *
     * @return integer
     */
    public function getAffectedRows()
    {
        return $this->count();
    }

    /**
     * Get generated value
     *
     * @return mixed|null
     */
    public function getGeneratedValue()
    {
        return null;
    }

    /**
     * Get the resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return null;
    }

    /**
     * Get field count
     *
     * @return integer
     */
    public function getFieldCount()
    {
        $inner = $this->getInnerIterator();

        foreach ( $inner as $entry )
        {
            return count( $entry );
        }

        return null;
    }

}
