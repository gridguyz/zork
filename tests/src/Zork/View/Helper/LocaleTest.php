<?php

namespace Zork\View\Helper;

use Locale as IntlLocale;
use Zork\I18n\Locale\Locale;
use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * LocaleTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\View\Helper\Locale
 */
class LocaleTest extends TestCase
{

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\View\Helper\Locale';

    /**
     * @var Locale
     */
    private static $locale = null;

    /**
     * @var string
     */
    private $previousLocale = null;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$helperCtorArgs = array(
            static::$locale = Locale::factory( array(
                'default'   => 'en',
                'fallback'  => 'en',
                'available' => array(
                    'en'    => true,
                    'en_US' => true,
                ),
            ) ),
        );
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->previousLocale = IntlLocale::getDefault();
        static::$locale->setCurrent( 'en_US' );

        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();

        if ( null !== $this->previousLocale )
        {
            IntlLocale::setDefault( $this->previousLocale );
            $this->previousLocale = null;
        }
    }

    /**
     * Test locales
     */
    public function testLocales()
    {
        $this->assertInstanceOf( 'Zork\I18n\Locale\Locale', $this->helper->getLocaleService() );
        $this->assertInstanceOf( static::$helperClass, $this->helper() );
        $this->assertSame( 'en_US', $this->helper->getCurrent() );
        $this->assertSame( 'en', $this->helper->getDefault() );
        $this->assertSame( array( 'en', 'en_US' ), $this->helper->getAvailableLocales() );
        $this->assertSame( array( 'en' => array( 'en', 'en_US' ) ), $this->helper->getAvailableLocales( true ) );
        $this->assertSame( array( 'en' => true, 'en_US' => true ), $this->helper->getAvailableFlags() );
        $this->assertSame( 'en_US', (string) $this->helper );
        $this->assertSame( 'en', $this->helper->getPrimaryLanguage() );
        $this->assertSame( 'US', $this->helper->getRegion() );
        $this->assertSame( 'en-US', $this->helper->toIso() );
    }

}
