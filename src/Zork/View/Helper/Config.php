<?php

namespace Zork\View\Helper;

use Zend\Config\Config as ZendConfig;
use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface;

/**
 * HeadDefaults
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Config extends ZendConfig
          implements HelperInterface
{

    /**
     * View object
     *
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $view = null;

    /**
     * Set the View object
     *
     * @param \Zend\View\Renderer\RendererInterface $view
     * @return \Zork\View\Helper\Config
     */
    public function setView( RendererInterface $view )
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get the view object
     *
     * @return null|\Zend\View\Renderer\RendererInterface
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Invokable helper
     *
     * @param null|string $module
     * @return \Zork\View\Helper\Config
     */
    public function __invoke( $module = null )
    {
        if ( $module !== null )
        {
            if ( empty( $this->modules ) ||
                 ! $this->modules instanceof ZendConfig )
            {
                return null;
            }

            return $this->modules->get( ucfirst( $module ) );
        }

        return $this;
    }

}
