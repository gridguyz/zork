<?php

namespace Zork\View\Helper;

use ArrayObject;
use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * HeadDefaultsTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\View\Helper\HeadDefaults
 */
class HeadDefaultsTest extends TestCase
{

    /**
     * @var string
     */
    protected static $rendererClass = 'Zend\View\Renderer\PhpRenderer';

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\View\Helper\HeadDefaults';

    /**
     * @var array
     */
    protected static $helperCtorArgs = array(
        array(
            'headTitle' => array(
                'content'           => 'Title',
                'separator'         => '/',
                'translatorEnabled' => false,
                'offsetX'           => null,
            ),
            'headMeta'  => array(
                'keywords'  => array(
                    'content'       => 'keywords content',
                ),
                'foo'       => array(
                    'name'          => 'custom',
                    'content'       => 'custom content',
                ),
                'bar'       => array(
                    'http-equiv'    => 'Content-Type',
                    'content'       => 'text/html',
                ),
            ),
            'headScript'    => array(
                'file'      => array(
                    'src'       => 'script.js',
                ),
                'source'    => array(
                    'script'    => 'alert("Boo");',
                    'type'      => 'text/x-myscript',
                ),
            ),
            'headStyle'     => array(
                'custom' => array(
                    'content'   => 'p{color:red}',
                    'type'      => 'text/css',
                ),
            ),
            'headLink'      => array(
                'css'       => array(
                    'href'  => 'style.css',
                    'type'  => 'text/css',
                ),
                'icon'      => array(
                    'href'  => 'icon.png',
                    'rel'   => array( 'icon', 'image_src' ),
                ),
            ),
            'headCustom'    => array(
                'foo'       => array(
                    'bar'   => 'baz',
                ),
            ),
        ),
    );

    /**
     * Create plugin
     *
     * @param   string  $name
     * @param   array   $options
     * @return  \Zend\View\Helper\HelperInterface
     */
    public function plugin( $name, array $options = null )
    {
        switch ( strtolower( $name ) )
        {
            case 'headtitle':
                $plugin = $this->getMockBuilder( 'Zork\View\Helper\HeadTitle' )
                               ->disableOriginalConstructor()
                               ->getMock();

                $plugin->expects( $this->once() )
                       ->method( '__invoke' )
                       ->with( $this->equalTo( 'Title' ),
                               $this->anything() )
                       ->will( $this->returnSelf() );

                $plugin->expects( $this->once() )
                       ->method( '__call' )
                       ->with( $this->equalTo( 'setSeparator' ),
                               $this->logicalAnd( $this->countEquals( 1 ),
                                                  $this->every( $this->matchesRegularExpression( '#^\s*/\s*$#' ) ) ) )
                       ->will( $this->returnSelf() );

                $plugin->expects( $this->once() )
                       ->method( 'setTranslatorEnabled' )
                       ->with( $this->isFalse() )
                       ->will( $this->returnSelf() );

                $plugin->expects( $this->once() )
                       ->method( '__set' )
                       ->with( $this->equalTo( 'offsetX' ), $this->isNull() )
                       ->will( $this->returnSelf() );
                break;

            case 'headmeta':
                $plugin = $this->getMockBuilder( 'Zend\View\Helper\HeadMeta' )
                               ->disableOriginalConstructor()
                               ->getMock();

                $plugin->expects( $this->once() )
                       ->method( 'getContainer' )
                       ->will( $this->returnValue( new ArrayObject( array(
                           (object) array(
                               'type'       => 'name',
                               'name'       => 'keywords',
                               'content'    => 'original',
                           ),
                       ) ) ) );

                $plugin->expects( $this->at( 0 ) )
                       ->method( '__invoke' )
                       ->with( $this->equalTo( 'text/html' ),
                               $this->equalTo( 'Content-Type' ),
                               $this->equalTo( 'http-equiv' ),
                               $this->equalTo( array() ),
                               $this->anything() )
                       ->will( $this->returnSelf() );

                $plugin->expects( $this->at( 1 ) )
                       ->method( '__invoke' )
                       ->with( $this->equalTo( 'custom content' ),
                               $this->equalTo( 'custom' ),
                               $this->equalTo( 'name' ),
                               $this->equalTo( array() ),
                               $this->anything() )
                       ->will( $this->returnSelf() );

                $plugin->expects( $this->once() )
                       ->method( '__call' )
                       ->with( $this->equalTo( 'setName' ),
                               $this->equalTo( array( 'keywords',
                                                      'keywords content, original',
                                                      array() ) ) )
                       ->will( $this->returnSelf() );
                break;

            case 'headmeta':
                $plugin = $this->getMockBuilder( 'Zend\View\Helper\HeadScript' )
                               ->disableOriginalConstructor()
                               ->getMock();

                $plugin->expects( $this->at( 0 ) )
                       ->method( '__invoke' )
                       ->with( $this->equalTo( \Zend\View\Helper\HeadScript::SCRIPT ),
                               $this->equalTo( 'alert("Boo");' ),
                               $this->anything(),
                               $this->equalTo( array() ),
                               $this->equalTo( 'text/x-myscript' ) )
                       ->will( $this->returnSelf() );

                $plugin->expects( $this->at( 1 ) )
                       ->method( '__invoke' )
                       ->with( $this->equalTo( \Zend\View\Helper\HeadScript::FILE ),
                               $this->equalTo( 'script.js' ),
                               $this->anything(),
                               $this->equalTo( array() ),
                               $this->equalTo( 'text/javascript' ) )
                       ->will( $this->returnSelf() );
                break;

            case 'headstyle':
                $plugin = $this->getMockBuilder( 'Zend\View\Helper\HeadStyle' )
                               ->disableOriginalConstructor()
                               ->getMock();

                $plugin->expects( $this->once() )
                       ->method( '__invoke' )
                       ->with( $this->equalTo( 'p{color:red}' ),
                               $this->anything(),
                               $this->equalTo( array( 'type' => 'text/css' ) ) )
                       ->will( $this->returnSelf() );
                break;

            case 'headlink':
                $plugin = $this->getMockBuilder( 'Zend\View\Helper\HeadLink' )
                               ->disableOriginalConstructor()
                               ->getMock();

                $plugin->expects( $this->at( 0 ) )
                       ->method( '__invoke' )
                       ->with( $this->equalTo( array(
                                   'href' => 'icon.png',
                                   'rel'  => 'icon',
                               ) ),
                               $this->anything() )
                       ->will( $this->returnSelf() );

                $plugin->expects( $this->at( 1 ) )
                       ->method( '__invoke' )
                       ->with( $this->equalTo( array(
                                   'href' => 'icon.png',
                                   'rel'  => 'image_src',
                               ) ),
                               $this->anything() )
                       ->will( $this->returnSelf() );

                $plugin->expects( $this->at( 2 ) )
                       ->method( '__invoke' )
                       ->with( $this->equalTo( array(
                                   'href' => 'style.css',
                                   'type' => 'text/css',
                                   'rel'  => 'stylesheet',
                               ) ),
                               $this->anything() )
                       ->will( $this->returnSelf() );
                break;

            case 'headcustom':
                $plugin = $this->getMockBuilder( 'Zend\View\Helper\HeadLink' )
                               ->disableOriginalConstructor()
                               ->getMock();

                $plugin->expects( $this->once() )
                       ->method( '__invoke' )
                       ->with( $this->equalTo( array( 'bar' => 'baz' ) ),
                               $this->anything() )
                       ->will( $this->returnSelf() );
                break;

            default:
                $plugin = parent::plugin( $name, $options );
                break;
        }

        return $plugin;
    }

}
