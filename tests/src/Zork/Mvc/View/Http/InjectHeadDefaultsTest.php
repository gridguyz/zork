<?php

namespace Zork\Mvc\View\Http;

use ArrayIterator;
use Zend\View\ViewEvent;
use Zend\View\Helper\HeadScript;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Helper\Placeholder\Container\AbstractContainer;

/**
 * InjectHeadDefaultsTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class InjectHeadDefaultsTest extends TestCase
{

    /**
     * @var InjectHeadDefaults
     */
    protected $injector;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->injector = new InjectHeadDefaults;
    }

    /**
     * Test getter & setter for definitions & constructor
     */
    public function testDefinitions()
    {
        $definitions = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );

        $this->injector = new InjectHeadDefaults( $definitions );
        $this->assertEquals( $definitions, $this->injector->getDefinitions() );

        $this->injector = new InjectHeadDefaults;
        $this->injector->setDefinitions( new ArrayIterator( $definitions ) );
        $this->assertEquals( $definitions, $this->injector->getDefinitions() );

        $this->injector = new InjectHeadDefaults;
        $this->injector->setDefinitions( (object) $definitions );
        $this->assertEquals( $definitions, $this->injector->getDefinitions() );
    }

    /**
     * Test attach to event-manager
     */
    public function testAttach()
    {
        $events = $this->getMock(
            'Zend\EventManager\EventManagerInterface'
        );

        $method = array( $this->injector, 'injectDefaults' );

        $events->expects( $this->once() )
               ->method( 'attach' )
               ->with( ViewEvent::EVENT_RENDERER_POST, $method, 100 )
               ->will( $this->returnValue( null ) );

        $this->injector
             ->attach( $events );
    }

    /**
     * Test inject defaults to headTitle()
     */
    public function testInjectDefaultsToHeadTitle()
    {
        /* @var $event \Zend\View\ViewEvent */
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $headTitle \Zend\View\Helper\HeadTitle */
        $event      = $this->getMock( 'Zend\View\ViewEvent' );
        $renderer   = $this->getMock( 'Zend\View\Renderer\PhpRenderer' );
        $headTitle  = $this->getMock(
            'Zend\View\Helper\HeadTitle',
            array(),
            array(),
            '',
            false,
            false
        );

        $event->expects( $this->exactly( 2 ) )
              ->method( 'getRenderer' )
              ->will( $this->returnValue( $renderer ) );

        $renderer->expects( $this->once() )
                 ->method( 'plugin' )
                 ->with( 'headTitle' )
                 ->will( $this->returnValue( $headTitle ) );

        $headTitle->expects( $this->at( 0 ) )
                  ->method( '__invoke' )
                  ->with( 'content2', AbstractContainer::PREPEND )
                  ->will( $this->returnSelf() );

        $headTitle->expects( $this->at( 1 ) )
                  ->method( '__invoke' )
                  ->with( 'content1', AbstractContainer::PREPEND )
                  ->will( $this->returnSelf() );

        $headTitle->expects( $this->once() )
                  ->method( '__call' )
                  ->with( 'setSeparator', array( ' / ' ) )
                  ->will( $this->returnSelf() );

        $headTitle->expects( $this->once() )
                  ->method( 'setTranslatorEnabled' )
                  ->with( false )
                  ->will( $this->returnSelf() );

        $this->injector->setDefinitions( array(
            'headTitle' => array(
                'content'           => array( 'content1', 'content2' ),
                'separator'         => '/  ',
                'translatorEnabled' => false,
            ),
        ) );

        $this->injector->injectDefaults( $event );
        // check preventing for multiple calls to inject multiple times
        $this->injector->injectDefaults( $event );
    }

    /**
     * Test inject defaults to headMeta()
     */
/*  public function testInjectDefaultsToHeadMeta()
    { */
        /* @var $event \Zend\View\ViewEvent */
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $headMeta \Zend\View\Helper\HeadMeta */
/*      $event      = $this->getMock( 'Zend\View\ViewEvent' );
        $renderer   = $this->getMock( 'Zend\View\Renderer\PhpRenderer' );
        $headMeta   = $this->getMock(
            'Zend\View\Helper\HeadMeta',
            array( 'getContainer', '__invoke', '__call' ),
            array(),
            '',
            false,
            false
        );

        $event->expects( $this->once() )
              ->method( 'getRenderer' )
              ->will( $this->returnValue( $renderer ) );

        $renderer->expects( $this->once() )
                 ->method( 'plugin' )
                 ->with( 'headMeta' )
                 ->will( $this->returnValue( $headMeta ) );

        $container = new \Zend\View\Helper\Placeholder\Container;

        $container->append( (object) array(
            'type'      => 'name',
            'name'      => 'keywords',
            'content'   => 'old, keywords',
        ) );

        $container->append( (object) array(
            'type'      => 'name',
            'name'      => 'description',
            'content'   => 'Old description.',
        ) );

        $headMeta->expects( $this->once() )
                 ->method( 'getContainer' )
                 ->will( $this->returnValue( $container ) );

        $headMeta->expects( $this->at( 0 ) )
                 ->method( '__call' )
                 ->with( 'setName', array(
                     'description',
                     'New description. Old description.',
                     array()
                 ) )
                 ->will( $this->returnSelf() );

        $headMeta->expects( $this->at( 1 ) )
                 ->method( '__call' )
                 ->with( 'setName', array(
                     'keywords',
                     'new, key, words, old, keywords',
                     array()
                 ) )
                 ->will( $this->returnSelf() );

        $headMeta->expects( $this->once() )
                 ->method( '__invoke' )
                 ->with( 'text/html', 'Content-Type', 'http-equiv',
                         array(), AbstractContainer::PREPEND )
                 ->will( $this->returnSelf() );

        $this->injector->setDefinitions( array(
            'headMeta' => array(
                'test skip'     => array(
                    'no content'    => true,
                ),
                'test header'   => array(
                    'http-equiv'    => 'Content-Type',
                    'content'       => 'text/html',
                ),
                'test keywords'      => array(
                    'name'      => 'keywords',
                    'content'   => 'new, key, words',
                ),
                'test description'   => 'New description.',
            ),
        ) );

        $this->injector->injectDefaults( $event );
    } */

    /**
     * Test inject defaults to headScript()
     */
    public function testInjectDefaultsToHeadScript()
    {
        /* @var $event \Zend\View\ViewEvent */
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $headScript \Zend\View\Helper\HeadScript */
        $event      = $this->getMock( 'Zend\View\ViewEvent' );
        $renderer   = $this->getMock( 'Zend\View\Renderer\PhpRenderer' );
        $headScript = $this->getMock(
            'Zend\View\Helper\HeadScript',
            array(),
            array(),
            '',
            false,
            false
        );

        $event->expects( $this->once() )
              ->method( 'getRenderer' )
              ->will( $this->returnValue( $renderer ) );

        $renderer->expects( $this->once() )
                 ->method( 'plugin' )
                 ->with( 'headScript' )
                 ->will( $this->returnValue( $headScript ) );

        $headScript->expects( $this->at( 0 ) )
                   ->method( '__invoke' )
                   ->with( HeadScript::SCRIPT, 'alert("foo!")',
                           AbstractContainer::PREPEND, array(),
                           'text/javascript' )
                   ->will( $this->returnSelf() );

        $headScript->expects( $this->at( 1 ) )
                   ->method( '__invoke' )
                   ->with( HeadScript::FILE, '/scripts/test.myscript',
                           AbstractContainer::PREPEND, array(),
                           'text/x-my-script' )
                   ->will( $this->returnSelf() );

        $this->injector->setDefinitions( array(
            'headScript' => array(
                'test skip' => array(
                    'no src & script' => true,
                ),
                'test src'  => array(
                    'src'   => '/scripts/test.myscript',
                    'type'  => 'text/x-my-script',
                ),
                'test script'   => array(
                    'script'    => 'alert("foo!")',
                ),
            ),
        ) );

        $this->injector->injectDefaults( $event );
    }

    /**
     * Test inject defaults to inlineScript()
     */
    public function testInjectDefaultsToInlineScript()
    {
        /* @var $event \Zend\View\ViewEvent */
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $inlineScript \Zend\View\Helper\InlineScript */
        $event          = $this->getMock( 'Zend\View\ViewEvent' );
        $renderer       = $this->getMock( 'Zend\View\Renderer\PhpRenderer' );
        $inlineScript   = $this->getMock(
            'Zend\View\Helper\InlineScript',
            array(),
            array(),
            '',
            false,
            false
        );

        $event->expects( $this->once() )
              ->method( 'getRenderer' )
              ->will( $this->returnValue( $renderer ) );

        $renderer->expects( $this->once() )
                 ->method( 'plugin' )
                 ->with( 'inlineScript' )
                 ->will( $this->returnValue( $inlineScript ) );

        $inlineScript->expects( $this->at( 0 ) )
                     ->method( '__invoke' )
                     ->with( HeadScript::SCRIPT, 'alert("foo!")',
                             AbstractContainer::PREPEND, array(),
                             'text/javascript' )
                     ->will( $this->returnSelf() );

        $inlineScript->expects( $this->at( 1 ) )
                     ->method( '__invoke' )
                     ->with( HeadScript::FILE, '/scripts/test.myscript',
                             AbstractContainer::PREPEND, array(),
                             'text/x-my-script' )
                     ->will( $this->returnSelf() );

        $this->injector->setDefinitions( array(
            'inlineScript' => array(
                'test skip' => array(
                    'no src & script' => true,
                ),
                'test src'  => array(
                    'src'   => '/scripts/test.myscript',
                    'type'  => 'text/x-my-script',
                ),
                'test script'   => array(
                    'script'    => 'alert("foo!")',
                ),
            ),
        ) );

        $this->injector->injectDefaults( $event );
    }

    /**
     * Test inject defaults to headStyle()
     */
    public function testInjectDefaultsToHeadStyle()
    {
        /* @var $event \Zend\View\ViewEvent */
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $headStyle \Zend\View\Helper\HeadStyle */
        $event      = $this->getMock( 'Zend\View\ViewEvent' );
        $renderer   = $this->getMock( 'Zend\View\Renderer\PhpRenderer' );
        $headStyle  = $this->getMock(
            'Zend\View\Helper\HeadStyle',
            array(),
            array(),
            '',
            false,
            false
        );

        $event->expects( $this->once() )
              ->method( 'getRenderer' )
              ->will( $this->returnValue( $renderer ) );

        $renderer->expects( $this->once() )
                 ->method( 'plugin' )
                 ->with( 'headStyle' )
                 ->will( $this->returnValue( $headStyle ) );

        $headStyle->expects( $this->at( 0 ) )
                  ->method( '__invoke' )
                  ->with( 'body { color: red; }',
                          AbstractContainer::PREPEND, array() )
                  ->will( $this->returnSelf() );

        $headStyle->expects( $this->at( 1 ) )
                  ->method( '__invoke' )
                  ->with( 'head { display: none; }',
                          AbstractContainer::PREPEND, array() )
                  ->will( $this->returnSelf() );

        $this->injector->setDefinitions( array(
            'headStyle' => array(
                'test skip' => array(
                    'no content' => true,
                ),
                'test head'  => array(
                    'content'   => 'head { display: none; }',
                ),
                'test body'  => array(
                    'content'   => 'body { color: red; }',
                ),
            ),
        ) );

        $this->injector->injectDefaults( $event );
    }

    /**
     * Test inject defaults to headLink()
     */
    public function testInjectDefaultsToHeadLink()
    {
        /* @var $event \Zend\View\ViewEvent */
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $headLink \Zend\View\Helper\HeadLink */
        $event      = $this->getMock( 'Zend\View\ViewEvent' );
        $renderer   = $this->getMock( 'Zend\View\Renderer\PhpRenderer' );
        $headLink   = $this->getMock(
            'Zend\View\Helper\HeadLink',
            array(),
            array(),
            '',
            false,
            false
        );

        $event->expects( $this->once() )
              ->method( 'getRenderer' )
              ->will( $this->returnValue( $renderer ) );

        $renderer->expects( $this->once() )
                 ->method( 'plugin' )
                 ->with( 'headLink' )
                 ->will( $this->returnValue( $headLink ) );

        $headLink->expects( $this->at( 0 ) )
                 ->method( '__invoke' )
                 ->with( array(
                     'href' => '/styles/sample.css',
                     'rel'  => 'stylesheet',
                 ) )
                 ->will( $this->returnSelf() );

        $headLink->expects( $this->at( 1 ) )
                 ->method( '__invoke' )
                 ->with( array(
                     'href' => '/alternate',
                     'rel'  => 'alternate',
                 ) )
                 ->will( $this->returnSelf() );

        $this->injector->setDefinitions( array(
            'headLink' => array(
                'test skip' => array(
                    'no href'   => true,
                ),
                'test rel'      => array(
                    'href'      => '/alternate',
                    'rel'       => 'alternate',
                ),
                'test no rel'   => array(
                    'href'      => '/styles/sample.css',
                ),
            ),
        ) );

        $this->injector->injectDefaults( $event );
    }

    /**
     * Test inject defaults
     */
    public function testInjectDefaults()
    {
        /* @var $event \Zend\View\ViewEvent */
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $default \Zend\View\Helper\AbstractHelper */
        $event      = $this->getMock( 'Zend\View\ViewEvent' );
        $renderer   = $this->getMock( 'Zend\View\Renderer\PhpRenderer' );
        $default    = $this->getMock(
            'Zend\View\Helper\HeadLink',
            array(),
            array(),
            '',
            false,
            false
        );

        $event->expects( $this->once() )
              ->method( 'getRenderer' )
              ->will( $this->returnValue( $renderer ) );

        $renderer->expects( $this->once() )
                 ->method( 'plugin' )
                 ->with( 'default' )
                 ->will( $this->returnValue( $default ) );

        $default->expects( $this->at( 0 ) )
                ->method( '__invoke' )
                ->with( array( 'bar' => 2 ), AbstractContainer::PREPEND )
                ->will( $this->returnSelf() );

        $default->expects( $this->at( 1 ) )
                ->method( '__invoke' )
                ->with( array( 'foo' => 1 ), AbstractContainer::PREPEND )
                ->will( $this->returnSelf() );

        $this->injector->setDefinitions( array(
            'default' => array(
                'test 1' => array( 'foo' => 1 ),
                'test 2' => array( 'bar' => 2 ),
            ),
        ) );

        $this->injector->injectDefaults( $event );
    }

}
