<?php

namespace Zork\Data;

use Traversable;

/**
 * TabularInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface TabularInterface extends Traversable
{

    /**
     * Get field names
     *
     * @return array
     */
    public function getFieldNames();

}
