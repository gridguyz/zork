<?php

namespace Zork\View\Helper;

use Zork\View\Helper\OpenGraph;
use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * OpenGraphTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\View\Helper\OpenGraph
 */
class OpenGraphTest extends TestCase
{

    /**
     * @var string
     */
    protected static $rendererClass = 'Zend\View\Renderer\PhpRenderer';

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\View\Helper\OpenGraph';

    /**
     * Test invoke helper
     */
    public function testInvokeHelper()
    {
        $this->assertInstanceOf( static::$helperClass, $this->helper() );

        $this->assertInstanceOf( static::$helperClass, $this->helper( OpenGraph::TYPE_ARTICLE ) );
        $this->assertSame( OpenGraph::TYPE_ARTICLE, $this->helper->getType() );

        $this->assertInstanceOf( static::$helperClass, $this->helper( array( 'book' => OpenGraph::PREFIX_BOOK ) ) );
        $this->assertSame( OpenGraph::PREFIX_BOOK, $this->helper->getPrefixNs( 'book' ) );
        $this->assertSame( array( 'book' ), $this->helper->getPrefixByNs( OpenGraph::PREFIX_BOOK ) );
    }

    /**
     * Test offsets
     */
    public function testOffsets()
    {
        $this->assertTrue( isset( $this->helper->type ) );
        $this->assertInternalType( 'array', $this->helper->type );

        unset( $this->helper->type );
        $this->assertFalse( isset( $this->helper->type ) );

        $this->helper->type = array(
            'property'  => 'og:type',
            'content'   => OpenGraph::TYPE_ARTICLE,
        );

        $this->assertInternalType( 'array', $this->helper->type );

        $this->helper[] = array(
            'property'  => 'og:example',
            'content'   => 'example',
        );

        $this->assertCount( 2, $this->helper );

        $this->assertEquals(
            array(
                'type' => array(
                    'property'  => 'og:type',
                    'content'   => OpenGraph::TYPE_ARTICLE,
                ),
                array(
                    'property'  => 'og:example',
                    'content'   => 'example',
                ),
            ),
            iterator_to_array( $this->helper )
        );
    }

    /**
     * Test types
     */
    public function testTypes()
    {
        $this->assertSame( OpenGraph::TYPE_WEBSITE, $this->helper->getType() );

        unset( $this->helper->type );
        $this->assertSame( OpenGraph::TYPE_WEBSITE, $this->helper->getType() );

        unset( $this->helper->type );
        $this->helper->setType( OpenGraph::TYPE_ARTICLE );
        $this->assertSame( OpenGraph::TYPE_ARTICLE, $this->helper->getType() );
    }

    /**
     * Test prefixes
     */
    public function testPrefixes()
    {
        $this->assertSame( OpenGraph::PREFIX_OG, $this->helper->getPrefixNs( 'og' ) );
        $this->assertSame( array( 'og' ), $this->helper->getPrefixByNs( OpenGraph::PREFIX_OG ) );
        $this->assertSame( 'og', $this->helper->getPrefixByNs( OpenGraph::PREFIX_OG, true ) );
        $this->assertNull( $this->helper->getPrefixNs( 'foo' ) );
        $this->assertSame( array(), $this->helper->getPrefixByNs( 'http://example.com/foo#' ) );
        $this->assertSame( null, $this->helper->getPrefixByNs( 'http://example.com/foo#', true ) );

        $this->helper->addPrefix( 'foo', 'http://example.com/foo#' );
        $this->assertSame( 'http://example.com/foo#', $this->helper->getPrefixNs( 'foo' ) );
        $this->assertSame( array( 'foo' ), $this->helper->getPrefixByNs( 'http://example.com/foo#' ) );
        $this->assertSame( 'foo', $this->helper->getPrefixByNs( 'http://example.com/foo#', true ) );

        $this->helper->removePrefix( 'foo' );
        $this->assertNull( $this->helper->getPrefixNs( 'foo' ) );
        $this->assertSame( array(), $this->helper->getPrefixByNs( 'http://example.com/foo#' ) );
        $this->assertSame( null, $this->helper->getPrefixByNs( 'http://example.com/foo#', true ) );
    }

    /**
     * Test append property / properties
     */
    public function testAppend()
    {
        $this->assertCount( 1, $this->helper );
        $this->helper->append( null );
        $this->assertCount( 1, $this->helper );

        unset( $this->helper->type );
        $this->helper->append( 'property1', 'value1' );

        $this->assertEquals(
            array(
                'property'  => 'property1',
                'content'   => 'value1',
            ),
            $this->helper[0]
        );

        $this->helper->append( array(
            'property2' => 'value2',
            array(
                'property3' => 'value3',
            ),
        ) );

        $this->assertEquals(
            array(
                array(
                    'property'  => 'property1',
                    'content'   => 'value1',
                ),
                array(
                    'property'  => 'property2',
                    'content'   => 'value2',
                ),
                array(
                    'property'  => 'property3',
                    'content'   => 'value3',
                ),
            ),
            iterator_to_array( $this->helper )
        );
    }

    /**
     * Test prefix attribute
     */
    public function testPrefixAttribute()
    {
        $headMeta = $this->getMockBuilder( 'Zend\View\Helper\HeadMeta' )
                         ->disableOriginalConstructor()
                         ->getMock();

        $headMeta->expects( $this->once() )
                 ->method( 'append' )
                 ->with( $this->equalTo( (object) array(
                     'property'     => 'og:type',
                     'content'      => OpenGraph::TYPE_WEBSITE,
                     'type'         => 'property',
                     'modifiers'    => array(),
                 ) ) )
                 ->will( $this->returnSelf() );

        $this->pluginInstances['headmeta'] = $headMeta;

        $this->assertEquals(
            'og: ' . OpenGraph::PREFIX_OG,
            $this->helper->getPrefixAttribute()
        );
    }

    /**
     * Test hasProperty() method
     */
    public function testHasProperty()
    {
        $this->helper->append( 'property1', 'value1' );
        $this->helper->append( 'property2', 'value2' );

        $this->assertTrue( $this->helper->hasProperty( 'property1' ) );
        $this->assertTrue( $this->helper->hasProperty( 'property2' ) );
        $this->assertFalse( $this->helper->hasProperty( 'property3' ) );
    }

    /**
     * Test getSafeLocale() method
     */
    public function testGetSafeLocale()
    {
        $this->assertEquals( 'en_US', $this->helper->getSafeLocale( 'en' ) );
        $this->assertEquals( 'en_US', $this->helper->getSafeLocale( 'en_US' ) );
    }

}
