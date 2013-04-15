<?php

namespace Zork\Iterator\Filter;

use Iterator;
use FilterIterator;

/**
 * Properties
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Properties extends FilterIterator
              implements PropertiesConsts
{

    use PropertiesTrait;

    /**
     * Constructor
     *
     * @param   \Iterator       $iterator
     * @param   array|object    $properties
     */
    public function __construct( Iterator $iterator, $properties = array() )
    {
        parent::__construct( $iterator );
        $this->addProperties( $properties );
    }

}
