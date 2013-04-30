<?php

namespace Zork\Stdlib;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * StringTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Stdlib\String
 */
class StringTest extends TestCase
{

    /**
     * Test generate random
     */
    public function testGenerateRandom()
    {
        $this->assertNotEquals(
            String::generateRandom(),
            String::generateRandom()
        );
    }

    /**
     * Test camelize
     */
    public function testCamelize()
    {
        $this->assertEquals( 'camelIzedWords', String::camelize( 'camel-ized-words' ) );
        $this->assertEquals( 'CamelIzedWords', String::camelize( 'Camel-Ized-Words' ) );
        $this->assertEquals( 'camelIzedWords', String::camelize( 'Camel.Ized.Words', '.', true ) );
        $this->assertEquals( 'CamelIzedWords', String::camelize( 'camel.ized.words', '.', false ) );
    }

    /**
     * Test decamelize
     */
    public function testDecamelize()
    {
        $this->assertEquals( 'camel-ized-words', String::decamelize( 'CamelIzedWords' ) );
        $this->assertEquals( 'camel.ized.words', String::decamelize( 'CamelIzedWords', '.' ) );
    }

    /**
     * Test template
     */
    public function testTemplate()
    {
        $this->assertEquals(
            'foovaluebar',
            String::template(
                'foo[VAR]bar',
                array(
                    'var' => 'value',
                )
            )
        );

        $this->assertEquals(
            'foovaluebar',
            String::template(
                'foo(VAR)bar',
                array(
                    'var' => 'value',
                ),
                '(%s)'
            )
        );
    }

    /**
     * Test strip html
     */
    public function testStripHtml()
    {
        $this->assertEquals(
            "foo\nbar",
            String::stripHtml( 'foo<br />bar' )
        );

        $this->assertRegExp(
            "/^foo\n-{3,}\nbar$/",
            String::stripHtml( 'foo<hr noshade />bar' )
        );

        $this->assertRegExp(
            "/^foo ?bar[ \n]?$/",
            String::stripHtml( 'foo<script type="x">xxx</script>bar<style type="y">yyy</style>' )
        );

        $this->assertEquals(
            "foo\nbar",
            String::stripHtml( '<p>foo</p><div>bar</div>' )
        );

        $this->assertEquals(
            "foo & bar\nbaz",
            String::stripHtml( " foo \t & bar \n\r baz\n " )
        );
    }

}
