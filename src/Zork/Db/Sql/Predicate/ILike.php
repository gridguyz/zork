<?php

namespace Zork\Db\Sql\Predicate;

use Zend\Db\Sql\Predicate\Like;

/**
 * ILike (case insensitive like)
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ILike extends Like
{

    /**
     * @var string
     */
    protected $specification = '%1$s ILIKE %2$s';

}
