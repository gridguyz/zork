<?php

namespace Zork\Iterator;

use Traversable;

/**
 * CallbackMapIterator
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CallbackMapIterator extends MapIterator
{

    /**
     * Constructor
     *
     * @param   Traversable $innerIterator
     * @param   callable    $callback
     * @param   int         $flags
     */
    public function __construct( Traversable $innerIterator, callable $callback, $flags = 0 )
    {
        parent::__construct( $innerIterator, $flags );
        $this->callback = $callback;
    }

    /**
     * Maps a value to a new one
     *
     * @param   mixed       $value
     * @param   int|string  $key
     * @return  mixed
     */
    public function map( $value, $key )
    {
        $callback = $this->callback;
        return $callback( $value, $key, $this );
    }

}
