<?php

namespace Zork\Iterator\Filter;

use RecursiveIterator;
use RecursiveFilterIterator;

/**
 * RecursiveProperties
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RecursiveProperties extends RecursiveFilterIterator
                       implements PropertiesConsts
{

    use PropertiesTrait;

    /**
     * @const null
     */
    const CHILDREN_ARRAY = null;

    /**
     * Constructor
     *
     * @param   \RecursiveIterator  $iterator
     * @param   array|object        $properties
     */
    public function __construct( RecursiveIterator $iterator,
                                 $properties = array() )
    {
        parent::__construct( $iterator );
        $this->addProperties( $properties );
    }

    /**
     * Return the inner iterator's children contained in a RecursiveFilterIterator
     *
     * @return void a <b>RecursiveFilterIterator</b> containing the inner iterator's children.
     */
    public function getChildren()
    {
        $result = parent::getChildren();
        $result->properties = $this->properties;
        return $result;
    }

}
