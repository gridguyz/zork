<?php

namespace Zork\Mvc\View\Http;

use Traversable;
use Zend\View\ViewEvent;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Helper\HeadScript;
use Zend\View\Renderer\PhpRenderer;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\View\Helper\Placeholder\Container\AbstractContainer;

/**
 * InjectHeadDefaults
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class InjectHeadDefaults extends AbstractListenerAggregate
{

    /**
     * @const string
     */
    const DEFAULT_TITLE = ' ';

    /**
     * @var array
     */
    protected $definitions;

    /**
     * Already injected to this views
     *
     * @var array
     */
    protected $injected = array();

    /**
     * @var array
     */
    protected $appendableHeadMetaNames = array(
        'keywords'      => array( '/[\s,;\.]+$/', '', ', ', ', ' ),
        'description'   => array( '/[\s,;\.]+$/', '', '. ', ' ' ),
    );

    /**
     * Constructor
     *
     * @param   array   $definitions
     */
    public function __construct( $definitions = array() )
    {
        $this->setDefinitions( $definitions );
    }

    /**
     * Set definitions
     *
     * @param   array|\Traversable  $definitions
     * @return  array
     */
    public function setDefinitions( $definitions )
    {
        if ( $definitions instanceof Traversable )
        {
            $definitions = ArrayUtils::iteratorToArray( $definitions );
        }
        else
        {
            $definitions = (array) $definitions;
        }

        $this->definitions = $definitions;
        return $this;
    }

    /**
     * Get definitions
     *
     * @return  array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * {@inheritDoc}
     */
    public function attach( EventManagerInterface $events )
    {
        $this->listeners[] = $events->attach(
            ViewEvent::EVENT_RENDERER_POST,
            array( $this, 'injectDefaults' ),
            100
        );
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
     * Inject default values for multiple view plugins
     *
     * @param   \Zend\View\ViewEvent    $event
     * @return  void
     */
    public function injectDefaults( ViewEvent $event )
    {
        $view = $event->getRenderer();

        if ( $view instanceof PhpRenderer )
        {
            $hash = spl_object_hash( $view );

            if ( ! empty( $this->injected[$hash] ) )
            {
                return;
            }

            $this->injected[$hash] = true;

            foreach ( $this->definitions as $helper => $data )
            {
                $plugin = $view->plugin( $helper );

                if ( $data instanceof Traversable )
                {
                    $data = ArrayUtils::iteratorToArray( $data );
                }
                else
                {
                    $data = (array) $data;
                }

                switch ( strtolower( $helper ) )
                {
                    case 'headtitle':

                        $contentSet = false;

                        if ( isset( $data['content'] ) )
                        {
                            foreach ( array_reverse( (array) $data['content'] )
                                      as $content )
                            {
                                if ( $content )
                                {
                                    $contentSet = true;
                                    $plugin( $content, AbstractContainer::PREPEND );
                                }
                            }

                            unset( $data['content'] );
                        }

                        if ( ! $contentSet )
                        {
                            $plugin( static::DEFAULT_TITLE, AbstractContainer::PREPEND );
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
                            $method = 'set' . ucfirst( $key );

                            if ( method_exists( $plugin, $method ) )
                            {
                                $plugin->$method( $value );
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

                                    $content .= rtrim(
                                        $this->appendableHeadMetaNames[$keyValue][2] .
                                            preg_replace(
                                                $this->appendableHeadMetaNames[$keyValue][0],
                                                $this->appendableHeadMetaNames[$keyValue][1],
                                                $this->getMetaByName( $metas, $keyValue )
                                            ),
                                        $this->appendableHeadMetaNames[$keyValue][3]
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

                        if ( is_callable( $plugin ) )
                        {
                            foreach ( array_reverse( $data ) as $spec )
                            {
                                $plugin(
                                    (array) $spec,
                                    AbstractContainer::PREPEND
                                );
                            }
                        }

                        break;
                }
            }
        }
    }

}
