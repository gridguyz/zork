<?php

namespace Zork\Mvc\View\Http;

use Zend\Mvc\View\Http\InjectTemplateListener as ZendInjectTemplateListener;

/**
 * InjectTemplateListener
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class InjectTemplateListener extends ZendInjectTemplateListener
{

    /**
     * Determine the top-level namespace of the controller
     *
     * @param   string  $controller
     * @return  string
     */
    protected function deriveModuleNamespace( $controller )
    {
        $pos = strpos( $controller, '\\Controller\\' );

        if ( false === $pos )
        {
            return parent::deriveModuleNamespace( $controller );
        }

        return str_replace( '\\', '/', substr( $controller, 0, $pos ) );
    }

    /**
     * @param $namespace
     * @return string
     */
    protected function deriveControllerSubNamespace( $namespace )
    {
        $pos = strpos( $namespace, '\\Controller\\' );

        if ( false === $pos )
        {
            return parent::deriveControllerSubNamespace( $namespace );
        }

        return str_replace( '\\', '/', substr( $namespace, $pos + 12 ) );
    }

}
