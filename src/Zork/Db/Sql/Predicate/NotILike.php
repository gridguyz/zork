<?php

namespace Zork\Db\Sql\Predicate;

use Zend\Db\Sql\Predicate\Like;

/**
 * NotILike
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class NotILike extends Like
{

    /**
     * @var string
     */
    protected $specification = '%1$s NOT ILIKE %2$s';

}
