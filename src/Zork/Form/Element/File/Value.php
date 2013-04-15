<?php

namespace Zork\Form\Element\File;

use ArrayObject;

/**
 * Value
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Value extends ArrayObject
{

    /**
     * String representation of uploaded file-value
     */
    public function __toString()
    {
        return isset( $this['name'] ) ? $this['name'] : '';
    }

}
