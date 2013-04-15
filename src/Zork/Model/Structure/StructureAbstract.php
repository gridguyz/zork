<?php

namespace Zork\Model\Structure;

use ArrayAccess;
use Serializable;
use IteratorAggregate;
use Zork\Stdlib\OptionsTrait;
use Zork\Stdlib\PropertiesTrait;

/**
 * StructureAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class StructureAbstract
    implements ArrayAccess,
               Serializable,
               IteratorAggregate
{

    use OptionsTrait,
        PropertiesTrait;

    /**
     * Constructor
     *
     * @param array|\Traversable $data
     */
    public function __construct( $data = array() )
    {
        $this->setOptions( $data );
    }

    /**
     * Get Iterator
     *
     * @return \Zork\Model\Structure\StructureIterator
     */
    public function getIterator()
    {
        return new StructureIterator( $this );
    }

    /**
     * Convert fields to array
     *
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array( $this->getIterator() );
    }

    /**
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or <b>null</b>
     */
    public function serialize()
    {
        return serialize( $this->toArray() );
    }

    /**
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized The string representation of the object.
     * @return void The return value from this method is ignored.
     */
    public function unserialize( $serialized )
    {
        foreach ( unserialize( $serialized ) as $key => $value )
        {
            $this->$key = $value;
        }
    }

}
