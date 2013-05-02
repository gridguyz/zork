<?php

namespace Zork\Data\Export\CsvTest;

use ArrayIterator;

/**
 * ArrayIteratorAware
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ArrayIteratorAware extends ArrayIterator
{

    /**
     * Return current array entry
     * @link http://php.net/manual/en/arrayiterator.current.php
     *
     * @return  mixed   The current array entry.
     */
    public function current()
    {
        $current = parent::current();

        if ( is_array( $current ) )
        {
            $current = new static( $current );
        }

        return $current;
    }

    /**
     * Get value for an offset
     * @link http://php.net/manual/en/arrayiterator.offsetget.php
     *
     * @param   string  $index  The offset to get the value from.
     * @return  mixed           The value at offset <i>index</i>.
     */
    public function offsetGet( $index )
    {
        $offset = parent::offsetGet( $index );

        if ( is_array( $offset ) )
        {
            $offset = new static( $offset );
        }

        return $offset;
    }

}
