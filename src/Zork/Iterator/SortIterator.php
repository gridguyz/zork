<?php

namespace Zork\Iterator;

use Traversable;
use ArrayIterator;

/**
 * SortIterator
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SortIterator extends ArrayIterator
{

    /**
     * @var int
     */
    const SORT_BY_KEY           = 1;

    /**
     * @var int
     */
    const SORT_BY_VALUE         = 2;

    /**
     * @var int
     */
    const SORT_BY_DEFAULT       = self::SORT_BY_VALUE;

    /**
     * @var int
     */
    const SORT_CMP_OPERATOR     = 1;

    /**
     * @var int
     */
    const SORT_CMP_NATURAL      = 2;

    /**
     * @var int
     */
    const SORT_CMP_NATURALCASE  = 3;

    /**
     * @var int
     */
    const SORT_CMP_DEFAULT     = self::SORT_CMP_OPERATOR;

    /**
     * Constructor
     *
     * @param   array|\Traversable  $iterator
     * @param   int|callable        $sortCmp
     * @param   int                 $sortBy ignored if $sortCmp = self::SORT_CMP_NATURAL(CASE)?
     */
    public function __construct( $iterator,
                                 $sortCmp   = self::SORT_CMP_DEFAULT,
                                 $sortBy    = self::SORT_BY_DEFAULT )
    {
        if ( $iterator instanceof Traversable )
        {
            $iterator = iterator_to_array( $iterator );
        }
        else
        {
            $iterator = (array) $iterator;
        }

        parent::__construct( $iterator );
        $this->sort( $sortCmp, $sortBy );
    }

    /**
     * Sort data
     *
     * @param   int|callable    $sortCmp
     * @param   int             $sortBy
     * @return  SortIterator
     */
    public function sort( $sortCmp  = self::SORT_CMP_DEFAULT,
                          $sortBy   = self::SORT_BY_DEFAULT )
    {
        $method = array( $this );
        $args   = array();
        $key    = $sortBy == self::SORT_BY_KEY;

        if ( is_callable( $sortCmp ) )
        {
            $method[] = $key ? 'uksort' : 'uasort';
            $args[]   = $sortCmp;
        }
        else
        {
            switch ( $sortCmp )
            {
                case self::SORT_CMP_OPERATOR:
                    $method[] = $key ? 'ksort' : 'asort';
                    break;

                case self::SORT_CMP_NATURAL:

                    if ( $key )
                    {
                        $method[] = 'uksort';
                        $args[]   = 'strnatcmp';
                    }
                    else
                    {
                        $method[] = 'natsort';
                    }

                    break;

                case self::SORT_CMP_NATURALCASE:

                    if ( $key )
                    {
                        $method[] = 'uksort';
                        $args[]   = 'strnatcasecmp';
                    }
                    else
                    {
                        $method[] = 'natcasesort';
                    }

                    break;
            }
        }

        if ( is_callable( $method ) )
        {
            call_user_func_array( $method, $args );
        }

        return $this;
    }

}
