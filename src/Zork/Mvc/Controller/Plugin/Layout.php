<?php

namespace Zork\Mvc\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\Layout as ZendLayout;

/**
 * Layout controller-plugin
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Layout extends ZendLayout
{

    /**
     * Set middle-layout
     *
     * @param string|\Zork\Mvc\Controller\Plugin\MiddleLayoutInterface\ $template
     * @param array|\Traversable $variables
     * @return \Zork\Mvc\Controller\Plugin\Layout
     */
    public function setMiddleLayout( $template, $variables = array() )
    {
        if ( ! $template instanceof MiddleLayoutInterface )
        {
            $template = new MiddleLayout( array(
                'template'  => $template,
                'variables' => $variables,
            ) );
        }

        $this->getViewModel()
             ->setVariable( 'middleLayout', $template );

        return $this;
    }

    /**
     * Get middle-layout
     *
     * @return null|\Zork\Mvc\Controller\Plugin\MiddleLayoutInterface
     */
    public function getMiddleLayout()
    {
        return $this->getViewModel()
                    ->getVariable( 'middleLayout' );
    }

}
