<?php

namespace Zork\View\Helper;

use Zend\View\Helper\Layout as ZendLayout;
use Zork\Mvc\Controller\Plugin\MiddleLayout;
use Zork\Mvc\Controller\Plugin\MiddleLayoutInterface;

/**
 * Layout
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

        $this->getRoot()
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
        return $this->getRoot()
                    ->getVariable( 'middleLayout' );
    }

}
