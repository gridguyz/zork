<?php

namespace Zork\View\Helper;

use Zend\Stdlib\ArrayUtils;
use Zend\View\Helper\HeadScript;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Renderer\RendererInterface;
use Zend\View\Helper\Placeholder\Container\AbstractContainer;

/**
 * HeadDefaults
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class HeadDefaults extends AbstractHelper
{

    /**
     * @var array
     */
    protected $definitions;

    /**
     * @var array
     */
    protected $appendableHeadMetaNames = array(
        'keywords'      => array( '/[\s,;\.]+$/', '', ', '  ),
        'description'   => array( '/[\s,;\.]+$/', '', '. ' ),
    );

    /**
     * @param array $definitions
     */
    public function __construct( array $definitions = array() )
    {
        $this->definitions = $definitions;
    }

    /**
     * Get meta by name
     *
     * @param array $metas
     * @param string $name
     * @return string
     */
    protected function getMetaByName( array &$metas, $name )
    {
        foreach ( $metas as $item )
        {
            if ( $item->type == 'name' && $item->name == $name )
            {
                return $item->content;
            }
        }

        return '';
    }

    /**
     * Set the View object
     *
     * @param  Renderer $view
     * @return HeadDefaults
     */
    public function setView( RendererInterface $view )
    {
        parent::setView( $view );

        if ( method_exists( $view, 'plugin' ) )
        {
            foreach ( $this->definitions as $helper => $data )
            {
                $plugin = $view->plugin( $helper );

                switch ( strtolower( $helper ) )
                {
                    case 'headtitle':

                        if ( isset( $data['content'] ) )
                        {
                            foreach ( array_reverse( (array) $data['content'] )
                                      as $content )
                            {
                                $plugin( $content, AbstractContainer::PREPEND );
                            }

                            unset( $data['content'] );
                        }

                        if ( isset( $data['separator'] ) )
                        {
                            $plugin->setSeparator(
                                ' ' . trim( $data['separator'] ) . ' '
                            );

                            unset( $data['separator'] );
                        }

                        foreach ( $data as $key => $value )
                        {
                            $method = array( $plugin, 'set' . ucfirst( $key ) );

                            if ( is_callable( $method ) )
                            {
                                $method( $value );
                            }
                            else
                            {
                                $plugin->$key = $value;
                            }
                        }

                        break;

                    case 'headmeta':

                        $metas = null;

                        foreach ( array_reverse( $data ) as $key => $spec )
                        {
                            if ( ! empty( $spec['content'] ) )
                            {
                                $content = $spec['content'];
                                unset( $spec['content'] );

                                if ( ! empty( $spec['http-equiv'] ) )
                                {
                                    $keyType    = 'http-equiv';
                                    $keyValue   = $spec['http-equiv'];
                                    unset( $spec['http-equiv'] );
                                }
                                elseif ( ! empty( $spec['name'] ) )
                                {
                                    $keyType    = 'name';
                                    $keyValue   = $spec['name'];
                                    unset( $spec['name'] );
                                }
                                else
                                {
                                    $keyType    = 'name';
                                    $keyValue   = $key;
                                }

                                if ( $keyType == 'name' &&
                                     isset( $this->appendableHeadMetaNames[$keyValue] ) )
                                {
                                    if ( null === $metas )
                                    {
                                        $metas = $plugin->getContainer()
                                                        ->getArrayCopy();
                                    }

                                    $content .= $this->appendableHeadMetaNames[$keyValue][2] .
                                            preg_replace(
                                                $this->appendableHeadMetaNames[$keyValue][0],
                                                $this->appendableHeadMetaNames[$keyValue][1],
                                                $this->getMetaByName( $metas, $keyValue )
                                            );

                                    $plugin->setName( $keyValue, $content, $spec );
                                }
                                else
                                {
                                    $plugin(
                                        $content,
                                        $keyValue,
                                        $keyType,
                                        $spec,
                                        AbstractContainer::PREPEND
                                    );
                                }
                            }
                        }

                        break;

                    case 'headscript':
                    case 'inlinescript':

                        foreach ( array_reverse( $data ) as $spec )
                        {
                            if ( ! empty( $spec['src'] ) )
                            {
                                $mode       = HeadScript::FILE;
                                $content    = $spec['src'];
                                unset( $spec['src'] );
                            }
                            elseif ( ! empty( $spec['script'] ) )
                            {
                                $mode       = HeadScript::SCRIPT;
                                $content    = $spec['script'];
                                unset( $spec['script'] );
                            }
                            else
                            {
                                continue;
                            }

                            if ( ! empty( $spec['type'] ) )
                            {
                                $type = $spec['type'];
                                unset( $spec['type'] );
                            }
                            else
                            {
                                $type = 'text/javascript';
                            }

                            $plugin(
                                $mode,
                                $content,
                                AbstractContainer::PREPEND,
                                $spec,
                                $type
                            );
                        }

                        break;

                    case 'headstyle':

                        foreach ( array_reverse( $data ) as $spec )
                        {
                            if ( ! empty( $spec['content'] ) )
                            {
                                $content = $spec['content'];
                                unset( $spec['content'] );
                                $plugin( $content, AbstractContainer::PREPEND, $spec );
                            }
                        }

                        break;

                    case 'headlink':

                        foreach ( array_reverse( $data ) as $spec )
                        {
                            if ( ! empty( $spec['href'] ) )
                            {
                                if ( empty( $spec['rel'] ) )
                                {
                                    $spec['rel'] = 'stylesheet';
                                }

                                foreach ( (array) $spec['rel'] as $rel )
                                {
                                    $plugin(
                                        ArrayUtils::merge(
                                            $spec, array( 'rel' => $rel )
                                        ),
                                        AbstractContainer::PREPEND
                                    );
                                }
                            }
                        }

                        break;

                    default:

                        foreach ( array_reverse( $data ) as $spec )
                        {
                            $plugin(
                                (array) $spec,
                                AbstractContainer::PREPEND
                            );
                        }

                        break;
                }
            }
        }

        return $this;
    }

    /**
     * Factory method
     *
     * @param array|\Traversable $options
     * @throws \InvalidArgumentException
     */
    public static function factory( $options )
    {
        if ( $options instanceof \Traversable )
        {
            $options = ArrayUtils::iteratorToArray( $options );
        }
        elseif ( ! is_array( $options ) )
        {
            throw new Exception\InvalidArgumentException( sprintf(
                '%s expects an array or Traversable object; received "%s"',
                __METHOD__, (
                    is_object( $options )
                        ? get_class( $options )
                        : gettype( $options )
                )
            ) );
        }

        return new static( $options );
    }

}
