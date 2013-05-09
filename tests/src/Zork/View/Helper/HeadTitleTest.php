<?php

namespace Zork\View\Helper;

use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * HeadDefaultsTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\View\Helper\HeadTitle
 */
class HeadTitleTest extends TestCase
{

    /**
     * @var string
     */
    protected static $rendererClass = 'Zend\View\Renderer\PhpRenderer';

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\View\Helper\HeadTitle';

    /**
     * @var array
     */
    protected static $helperCtorArgs = array();

    /**
     * @var string
     */
    protected $separator = '/';

    /**
     * @var string
     */
    protected $separatorRegexPart = '\\s*(<span(\s[^>]*)?>%1$s<\\/span>|%1$s)\\s*';

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->helper
             ->getContainer()
             ->exchangeArray( array( 'part1', 'part2', 'part3' ) );

        $this->helper->setSeparator( $this->separator );
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        $this->helper
             ->getContainer()
             ->exchangeArray( array() );

        parent::tearDown();
    }

    /**
     * Get separator regex part
     *
     * @param   string  $separator
     * @param   string  $delimiter
     * @return  string
     */
    protected function separatorRegex( $separator = null, $delimiter = '/' )
    {
        if ( null === $separator )
        {
            $separator = $this->separator;
        }

        return sprintf(
            $this->separatorRegexPart,
            preg_quote( $separator, $delimiter )
        );
    }

    /**
     * Test default slice
     */
    public function testDefaultSlice()
    {
        $this->assertRegExp(
            '/^part2' . $this->separatorRegex() . 'part3$/',
            $this->helper->slice( 1 )
        );

        $this->assertRegExp(
            '/^part1' . $this->separatorRegex( '|' ) . 'part2$/',
            $this->helper->slice( 0, 2, '|' )
        );
    }

    /**
     * Test translated slice
     */
    public function testTranslatedSlice()
    {
        $translator = $this->getMock( 'Zend\I18n\Translator\Translator' );

        $translator->expects( $this->any() )
                   ->method( 'translate' )
                   ->will( $this->returnValueMap( array(
                       array( 'part1', 'default', null, 'translated1' ),
                       array( 'part2', 'default', null, 'translated2' ),
                       array( 'part3', 'default', null, 'translated3' ),
                   ) ) );

        $this->helper->setTranslatorEnabled( true );
        $this->helper->setTranslator( $translator, 'default' );

        $this->assertRegExp(
            '/^translated2' . $this->separatorRegex() . 'translated3$/',
            $this->helper->slice( 1 )
        );

        $this->assertRegExp(
            '/^translated1' . $this->separatorRegex( '|' ) . 'translated2$/',
            $this->helper->slice( 0, 2, '|' )
        );
    }

}
