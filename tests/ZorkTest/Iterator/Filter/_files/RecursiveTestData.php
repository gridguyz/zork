<?php

namespace ZorkTest\Iterator\Filter;

use RecursiveArrayIterator;

/**
 * RecursiveTestData
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RecursiveTestData extends RecursiveArrayIterator
{

    /**
     * Returns whether current entry is an array or an object.
     *
     * @return bool <b>TRUE</b> if the current entry is an array or an object,
     * otherwise <b>FALSE</b> is returned.
     */
    public function hasChildren()
    {
        $current = $this->current();
        return ! empty( $current['children'] );
    }

    /**
     * Returns an iterator for the current entry if it is an array or an object.
     *
     * @return RecursiveArrayIterator An iterator for the current entry, if it is an array or object.
     */
    public function getChildren()
    {
        $current = $this->current();
        return new RecursiveTestData( $current['children'] );
    }

}
