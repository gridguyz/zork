<?php

namespace Zork\Iterator;

use Traversable;

/**
 * DepthList
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DepthList
{
    
    /**
     * Inner iterator
     * 
     * @var array|\Traversable
     */
    protected $iterator;
    
    /**
     * Constructor
     * 
     * @param array|\Traversable $iterator
     */
    public function __construct( $iterator )
    {
        if ( ! $iterator instanceof Traversable )
        {
            $iterator = (array) $iterator;
        }
        
        $this->iterator = & $iterator;
    }
    
    /**
     * Run-in the depth-list
     * 
     * @param callable $onOpen
     * @param callable $onClose
     * @return void
     */
    public function runin( callable $onOpen, callable $onClose )
    {
        $stack = array();
        
        foreach ( $this->iterator as $entry )
        {
            list( $depth, $node ) = $entry;
            $stackDepth = empty( $stack ) ? 0 : end( $stack )[0];
            
            if ( $depth < $stackDepth )
            {
                for ( $i = 0, $m = $stackDepth - $depth; $i < $m; $i++ )
                {
                    $onClose( array_pop( $stack )[1] );
                }
            }
            
            if ( $depth <= $stackDepth )
            {
                $onClose( array_pop( $stack )[1] );
            }
            
            $onOpen( $node, empty( $stack ) ? null : end( $stack )[1] );
            $stack[ $depth ] = $entry;
        }
        
        while ( ! empty( $stack ) )
        {
            $onClose( array_pop( $stack )[1] );
        }
    }
    
}
