<?php

namespace Zork\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtmlAttr;

/**
 * HtmlTag
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class HtmlTag extends AbstractHelper
{

    /**
     * @var \Zend\View\Helper\EscapeHtmlAttr
     */
    protected $escapeHtmlAttrHelper;

    /**
     * Retrieve the EscapeHtmlAttr helper
     *
     * @return \Zend\View\Helper\EscapeHtmlAttr
     */
    protected function getEscapeHtmlAttrHelper()
    {
        if ( $this->escapeHtmlAttrHelper )
        {
            return $this->escapeHtmlAttrHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->escapeHtmlAttrHelper = $this->view
                                               ->plugin( 'escapeHtmlAttr' );
        }

        if ( ! $this->escapeHtmlAttrHelper instanceof EscapeHtmlAttr )
        {
            $this->escapeHtmlAttrHelper = new EscapeHtmlAttr();
        }

        return $this->escapeHtmlAttrHelper;
    }

    /**
     * Invokable helper
     *
     * @param string $name
     * @param null|string $content
     * @param null|array|\Trversable $attribs
     * @return string|\Zork\View\Helper\HtmlTag
     */
    public function __invoke( $name = null, $content = null, $attribs = null )
    {
        if ( null !== $name )
        {
            return $this->tag( $name, $content, $attribs );
        }

        return $this;
    }

    /**
     * Render an html-tag
     *
     * @param string $name
     * @param null|string $content
     * @param null|array|\Trversable $attribs
     * @return string
     */
    public function tag( $name, $content = null, $attribs = null )
    {
        $result = '<' . $name;

        if ( ! empty( $attribs ) )
        {
            $escape = $this->getEscapeHtmlAttrHelper();

            foreach ( $attribs as $key => $value )
            {
                $result .= ' ' . $key . '="' . $escape( $value ) . '"';
            }
        }

        if ( null === $content )
        {
            $result .= ' />';
        }
        else
        {
            $result .= '>' . $content . '</' . $name . '>';
        }

        return $result;
    }

}
