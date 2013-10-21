<?php

namespace Zork\View\Helper;

use Zend\View\Helper\Escaper\AbstractHelper;

trait EscapeTrait
{

    /**
     * Invoke this helper: escape a value
     *
     * @param   mixed   $value
     * @param   int     $recurse
     * @return  mixed
     */
    public function __invoke( $value, $recurse = AbstractHelper::RECURSE_NONE )
    {
        if ( is_array( $value ) &&
             ! ( AbstractHelper::RECURSE_ARRAY & $recurse ) )
        {
            $value = '';
        }

        return parent::__invoke( $value, $recurse );
    }

}
