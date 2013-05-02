<?php

namespace Zork\Data\TableTest;

use ArrayObject;
use ArrayIterator;
use IteratorAggregate;
use Zork\Stdlib\OptionsTrait;

/**
 * ArrayObjectAware
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ArrayAccessAware extends ArrayIterator
{

    /**
     * Return current array entry
     * @link http://php.net/manual/en/arrayiterator.current.php
     *
     * @return mixed The current array entry.
     */
    public function current()
    {
        $current = parent::current();

        if ( is_array( $current ) )
        {
            $current = new ArrayObject( $current );
        }

        return $current;
    }

}

/**
 * StdClassAware
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class StdClassAware extends ArrayIterator
{

    /**
     * Return current array entry
     * @link http://php.net/manual/en/arrayiterator.current.php
     *
     * @return mixed The current array entry.
     */
    public function current()
    {
        $current = parent::current();

        if ( is_array( $current ) )
        {
            $current = (object) $current;
        }

        return $current;
    }

}

/**
 * GetOptionAware
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class GetOptionAware implements IteratorAggregate
{

    /**
     * @var array
     */
    protected $data;

    /**
     * @param   array   $data
     */
    public function __construct( array $data )
    {
        $this->data = $data;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new GetOptionAwareIterator( $this->data );
    }

}

/**
 * GetOptionAwareIterator
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class GetOptionAwareIterator extends ArrayIterator
{

    /**
     * Return current array entry
     * @link http://php.net/manual/en/arrayiterator.current.php
     *
     * @return mixed The current array entry.
     */
    public function current()
    {
        $current = parent::current();

        if ( is_array( $current ) )
        {
            $current = new GetOptionAwareEntry( $current );
        }

        return $current;
    }

}

/**
 * GetOptionAwareEntry
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class GetOptionAwareEntry
{

    use OptionsTrait;

    /**
     * @param array $options
     */
    public function __construct( array $options )
    {
        $this->setOptions( $options );
    }

}