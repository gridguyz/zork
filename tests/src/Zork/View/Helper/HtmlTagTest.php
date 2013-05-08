<?php

namespace Zork\View\Helper;

use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * HtmlTagTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\View\Helper\HtmlTag
 */
class HtmlTagTest extends TestCase
{

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\View\Helper\HtmlTag';

    /**
     * Test invoke without arguments
     */
    public function testInvokeWithoutArguments()
    {
        $this->assertInstanceOf( static::$helperClass, $this->helper() );
    }

    /**
     * Test domains
     */
    public function testHtmlTags()
    {
        $this->assertSame( '<html />', $this->helper( 'html' ) );
        $this->assertSame( '<html>text</html>', $this->helper( 'html', 'text' ) );

        $this->assertSame(
            '<html id="id&quot;val" class="class&quot;val" />',
            $this->helper( 'html', null, array(
                'id'    => 'id"val',
                'class' => 'class"val',
            ) )
        );

        $this->assertSame(
            '<html id="id&quot;val">text</html>',
            $this->helper( 'html', 'text', array(
                'id' => 'id"val',
            ) )
        );
    }

}
