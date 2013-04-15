<?php

namespace Zork\Form\View\Helper;

use Zend\Form\View\Helper\FormPassword as ZendFormPassword;

/**
 * FormPassword
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FormPassword extends ZendFormPassword
{

    /**
     * {@inheritDoc}
     */
    public function createAttributesString( array $attributes )
    {
        if ( isset( $attributes['value'] ) )
        {
            unset( $attributes['value'] );
        }

        return parent::createAttributesString( $attributes );
    }

}
