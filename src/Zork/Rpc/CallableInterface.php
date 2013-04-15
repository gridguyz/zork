<?php

namespace Zork\Rpc;

/**
 * InvokableInterface
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface CallableInterface
{
    
    /**
     * Invoke the rpc-function with params
     * 
     * @param string $name
     * @param array|\Traversable $params
     * @return mixed
     */
    public function call( $name, $params );
    
}
