<?php

namespace Zork\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Traversable
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Traversable implements HydratorInterface
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

        if ( ! empty( $object ) )
        {
            foreach ( $object as $key => $value )
            {
                $data[$key] = $value;
            }
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
        if ( null === $object )
        {
            $object = array();
        }

        if ( is_array( $object ) )
        {
            foreach ( $data as $key => $value )
            {
                $object[$key] = $value;
            }
        }
        else
        {
            foreach ( $data as $key => $value )
            {
                $object->$key = $value;
            }
        }

        return $object;
    }

}
