<?php

namespace Zork\Db\Sql\Predicate;

use Zend\Db\Sql\Predicate\Like;

/**
 * NotLike
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class NotLike extends Like
{

    /**
     * @var string
     */
    protected $specification = '%1$s NOT LIKE %2$s';

}
