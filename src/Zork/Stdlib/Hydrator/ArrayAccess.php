<?php

namespace Zork\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * ArrayAccess
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ArrayAccess implements HydratorInterface
{

    /**
     * Extract values from an object
     *
     * @param  object|array $object
     * @return array
     */
    public function extract( $object )
    {
        $data = array();

        foreach ( $object as $key => $value )
        {
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  object|array $object
     * @return object
     */
    public function hydrate( array $data, $object )
    {
        foreach ( $data as $key => $value )
        {
            $object[$key] = $value;
        }

        return $object;
    }

}
