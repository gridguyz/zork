<?php

namespace Zork\I18n\Locale;

use ArrayIterator;
use Locale as IntlLocale;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * LocaleTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\I18n\Locale\Locale
 * @covers Zork\I18n\Locale\LocaleServiceFactory
 */
class LocaleTest extends TestCase
{

    /**
     * @var string
     */
    private $previousLocale;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->previousLocale = IntlLocale::getDefault();
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
        }

        $this->previousLocale = null;
    }

    /**
     * Test service factory
     */
    public function testServiceFactory()
    {
        $service = new ServiceManager();
        $config  = array(
            'locale' => array(
                'default'   => 'fr',
                'current'   => 'fr_FR',
                'fallback'  => 'en',
                'available' => array(
                    'en'    => true,
                    'fr'    => true,
                    'fr_FR' => true,
                ),
            ),
        );

        $service->setService( 'Configuration', $config )
                ->setFactory( 'Zork\I18n\Locale\Locale', 'Zork\I18n\Locale\LocaleServiceFactory' )
                ->setAlias( 'Locale', 'Zork\I18n\Locale\Locale' );

        /* @var $locale Locale */
        $locale = $service->get( 'Locale' );

        $this->assertInstanceOf( 'Zork\I18n\Locale\Locale', $locale );
        $this->assertEquals( 'fr_FR', $locale->getCurrent() );
        $this->assertEquals( 'fr', $locale->getDefault() );
        $this->assertEquals( 'en', $locale->getFallback() );

        $this->assertEquals(
            array(
                'en'    => true,
                'fr'    => true,
                'fr_FR' => true,
            ),
            $locale->getAvailableFlags()
        );

        $this->assertEquals(
            array(
                'en',
                'fr',
                'fr_FR',
            ),
            $locale->getAvailableLocales()
        );

        $this->assertEquals(
            array(
                'en' => array( 'en' ),
                'fr' => array( 'fr', 'fr_FR' ),
            ),
            $locale->getAvailableLocales( true )
        );
    }

    /**
     * Test defaults
     */
    public function testDefaults()
    {
        $locale = new Locale;

        $this->assertSame( Locale::DEFAULT_LOCALE, $locale->getDefault() );
        $this->assertSame( Locale::DEFAULT_LOCALE, $locale->getFallback() );
        $this->assertSame( array( Locale::DEFAULT_LOCALE ), $locale->getAvailableLocales() );
    }

    /**
     * Test factory method
     */
    public function testFactoryMethod()
    {
        $locale = Locale::Factory( new ArrayIterator( array(
            'default'   => 'fr',
            'fallback'  => 'en',
            'current'   => 'fr_FR',
            'available' => array(
                'en'    => true,
                'fr'    => true,
                'fr_FR' => true,
            ),
        ) ) );

        $this->assertSame( 'fr', $locale->getDefault() );
        $this->assertSame( 'en', $locale->getFallback() );
        $this->assertSame( 'fr_FR', $locale->getCurrent() );
        $this->assertSame( array( 'en', 'fr', 'fr_FR' ), $locale->getAvailableLocales() );
    }

    /**
     * Test factory method called with non-traversable
     *
     * @expectedException   InvalidArgumentException
     */
    public function testFactoryMethodNonTraversable()
    {
        Locale::Factory( 0 );
    }

    /**
     * Test available locales
     */
    public function testAvailableLocales()
    {
        $locale = new Locale;

        $locale->setDefault( 'en' );
        $this->assertEquals( array( 'en' ), $locale->getAvailableLocales() );

        $locale->setAvailable( 'fr' );
        $this->assertEquals( array( 'en', 'fr' ), $locale->getAvailableLocales() );

        $locale->setAvailable( array( 'fr', 'fr_FR' ) );
        $this->assertEquals( array( 'en', 'fr', 'fr_FR' ), $locale->getAvailableLocales() );

        $locale->setAvailable( array( 'fr' => true, 'fr_FR' => false ) );
        $this->assertEquals( array( 'en', 'fr' ), $locale->getAvailableLocales() );
    }

    /**
     * Test normalize locale
     */
    public function testNormalizeLocale()
    {
        $this->assertEquals( 'en', Locale::normalizeLocale( 'en' ) );
        $this->assertEquals( 'en_US', Locale::normalizeLocale( 'en_US' ) );
        $this->assertEquals( 'en_US', Locale::normalizeLocale( 'en-US' ) );
        $this->assertEquals( 'en_US', Locale::normalizeLocale( 'en-us' ) );
        $this->assertEquals( 'de_DE', Locale::normalizeLocale( 'de-Latn-DE' ) );
        $this->assertEquals( 'de_DE', Locale::normalizeLocale( 'de-Latf-DE' ) );
        $this->assertEquals( 'de_DE', Locale::normalizeLocale( 'de-DE-x-goethe' ) );
        $this->assertEquals( 'de_DE', Locale::normalizeLocale( 'de-Latn-DE-1996' ) );
        $this->assertEquals( 'de_DE', Locale::normalizeLocale( 'de-Deva-DE' ) );
        $this->assertEquals( 'de', Locale::normalizeLocale( 'de-x-DE' ) );
        $this->assertEquals( 'de', Locale::normalizeLocale( 'de-Deva' ) );
        $this->assertEquals( 'xxx', Locale::normalizeLocale( 'xxx' ) );
        $this->assertEquals( '', Locale::normalizeLocale( '' ) );
    }

    /**
     * Test current setter
     */
    public function testCurrent()
    {
        $locale = Locale::factory( array(
            'available' => array(
                'en'    => true,
                'fr'    => true,
                'de'    => true,
            ),
        ) );

        $locale->setCurrent( 'fr' );
        $this->assertEquals( 'fr', $locale->getCurrent() );
        $this->assertEquals( 'fr', (string) $locale );

        $locale->setCurrent( 'de_DE' );
        $this->assertEquals( 'de', $locale->getCurrent() );
        $this->assertEquals( 'de', (string) $locale );
    }

    /**
     * Test accept from http
     */
    public function testAccesptFromHttp()
    {
        $locale = Locale::factory( array(
            'default'   => 'en',
            'available' => array(
                'en'    => true,
                'fr'    => true,
                'de'    => true,
            ),
        ) );

        $this->assertEquals( 'en', $locale->acceptFromHttp( null ) );
        $this->assertEquals( 'fr', $locale->acceptFromHttp( 'fr' ) );
        $this->assertEquals( 'fr', $locale->acceptFromHttp( 'fr, *;q=0.1' ) );
        $this->assertEquals( 'fr', $locale->acceptFromHttp( 'fr, de;q=0.8, *;q=0.1' ) );
        $this->assertEquals( 'de', $locale->acceptFromHttp( 'fr;q=0.8, de, *;q=0.1' ) );
        $this->assertEquals( 'de', $locale->acceptFromHttp( 'hu-HU, de;q=0.8, *;q=0.1' ) );
        $this->assertEquals( 'de', $locale->acceptFromHttp( 'hu, de-DE;q=0.8, *;q=0.1' ) );
        $this->assertEquals( 'en', $locale->acceptFromHttp( 'hu, no;q=0.8, *;q=0.1' ) );
    }

}
