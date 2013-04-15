<?php

namespace Zork\Data;

use Traversable;

/**
 * FileInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface FileInterface extends Traversable
{

    /**
     * @return string
     */
    public function getMimeType();

}
