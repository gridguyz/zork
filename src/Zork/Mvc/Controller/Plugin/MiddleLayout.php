<?php

namespace Zork\Mvc\Controller\Plugin;

use Zork\Stdlib\OptionsTrait;

/**
 * MiddleLayout
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MiddleLayout implements MiddleLayoutInterface
{

    use OptionsTrait;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $variables;

    /**
     * Constructor
     *
     * @param array|\traversable $options
     */
    public function __construct( $options = null )
    {
        if ( null !== $options )
        {
            $this->setOptions( $options );
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param string $template
     * @return \Zork\Mvc\Controller\Plugin\MiddleLayout
     */
    public function setTemplate( $template )
    {
        $this->template = (string) $template;
        return $this;
    }

    /**
     * @param array|\Traversable $variables
     * @return \Zork\Mvc\Controller\Plugin\MiddleLayout
     */
    public function setVariables( $variables )
    {
        if ( is_array( $variables ) )
        {
            $this->variables = $variables;
        }
        elseif ( $variables instanceof \Traversable )
        {
            $this->variables = array();

            foreach ( $variables as $key => $value )
            {
                $this->variables[$key] = $value;
            }
        }
        else
        {
            $this->variables = (array) $variables;
        }

        return $this;
    }

}
