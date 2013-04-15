<?php

namespace Zork\Model\Structure;

use Iterator;
use Countable;
use ReflectionObject;
use Zork\Model\Structure\StructureAbstract;

/**
 * StuctureIterator
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class StructureIterator implements Iterator,
                                   Countable
{

    /**
     * What structure iterates
     *
     * @var \Zork\Model\Structure\StructureAbstract
     */
    protected $structure;

    /**
     * All properties exists on structure
     *
     * @var array
     */
    protected $properties = array();

    /**
     * State index
     *
     * @var int
     */
    protected $stateIndex = 0;

    /**
     * Constructor
     *
     * @param \Zork\Model\Structure\StructureAbstract $structure
     */
    public function __construct( StructureAbstract & $structure )
    {
        $this->structure = & $structure;
        $reflection      = new ReflectionObject( $structure );

        /* @var $property \ReflectionProperty */
        foreach ( $reflection->getProperties() as $property )
        {
            $name = $property->getName();

            if ( '_' != $name[0] &&
                 ! $property->isStatic() &&
                 ( ! $property->isPrivate() ||
                   is_callable( array( $structure, 'get' . ucfirst( $name ) ) ) ) )
            {
                $this->properties[] = $name;
            }
        }
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return \Zork\Model\Structure\StructureIterator
     */
    public function rewind()
    {
        $this->stateIndex = 0;
        return $this;
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset( $this->properties[ $this->stateIndex ] );
    }

    /**
     * Return the key of the current element
     *
     * @return string
     */
    public function key()
    {
        return $this->properties[ $this->stateIndex ];
    }

    /**
     * Return the current element
     *
     * @return mixed
     */
    public function current()
    {
        $key = $this->key();
        return $this->structure->$key;
    }

    /**
     * Move forward to next element
     *
     * @return \Zork\Model\Structure\StructureIterator
     */
    public function next()
    {
        $this->stateIndex++;
        return $this;
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return count( $this->properties );
    }

}
