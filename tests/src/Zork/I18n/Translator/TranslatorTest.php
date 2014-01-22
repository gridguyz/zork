<?php

namespace Zork\I18n\Translator;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

class TranslatorTest extends TestCase
{

    /**
     * Test factory() & schemas support
     */
    public function testFactoryAndSchemas()
    {
        $translator = Translator::factory( new ArrayIterator( array(
            'translation_file_my_patterns' => array(
                'test my translations' => array(
                    'type'          => 'phpArray',
                    'base_dir'      => __DIR__ . '/_files/my-translations',
                    'pattern'       => '%s/%s/%s.php',
                ),
            ),
        ) ) );

        $translator->setMySchemas( new ArrayIterator( array(
            'my test schema',
            2,
            true,
            false,
            null,
        ) ) );

        $this->assertEquals(
            array( 'my test schema', '2', '1' ),
            $translator->getMySchemas()
        );

        $translator->addMySchema( 'extra schema' );

        $this->assertEquals(
            array( 'my test schema', '2', '1', 'extra schema' ),
            $translator->getMySchemas()
        );
    }

    /**
     * Test factory() called with my patters is not an array
     */
    public function testFactoryMyPattersIsNotAnArray()
    {
        $this->setExpectedException(
            'Zend\I18n\Exception\InvalidArgumentException'
        );

        Translator::factory( array(
            'translation_file_my_patterns' => true,
        ) );
    }

    /**
     * Test factory() called with my patters, which contains an invalid entry
     */
    public function testFactoryMyPattersContainsInvalidEntry()
    {
        $this->setExpectedException(
            'Zend\I18n\Exception\InvalidArgumentException'
        );

        Translator::factory( array(
            'translation_file_my_patterns' => array(
                array(),
            ),
        ) );
    }

    /**
     * Test translate()
     */
    public function testTranslate()
    {
        $translator = Translator::factory( array(
            'locale' => array( 'en', 'en' ),
            'translation_file_my_patterns' => array(
                'test my translations' => array(
                    'type'          => 'phpArray',
                    'base_dir'      => __DIR__ . '/_files/my-translations',
                    'pattern'       => '%s/%s/%s.php',
                ),
            ),
        ) );

        $translator->setMySchemas( array(
            'schema1',
            'schema2',
        ) );

        $this->assertEquals(
            'test1 at schema1',
            $translator->translate( 'domain.test1' )
        );

        $this->assertEquals(
            'test2 at schema2',
            $translator->translate( 'domain.test2' )
        );

        $this->assertContains(
            $translator->translate( 'domain.both' ),
            array( 'both at schema1', 'both at schema2' )
        );

        $this->assertEquals(
            'domain.none',
            $translator->translate( 'domain.none' )
        );

        $this->assertEquals(
            'unknown.none',
            $translator->translate( 'unknown.none' )
        );

        $this->assertEquals(
            '',
            $translator->translate( '' )
        );
    }

    /**
     * Test translatePlural()
     */
    public function testTranslatePlural()
    {
        $translator = Translator::factory( array(
            'locale' => array( 'en', 'en' ),
            'translation_file_my_patterns' => array(
                'test my translations' => array(
                    'type'          => 'phpArray',
                    'base_dir'      => __DIR__ . '/_files/my-translations',
                    'pattern'       => '%s/%s/%s.php',
                ),
            ),
        ) );

        $translator->setMySchemas( array(
            'schema1',
            'schema2',
        ) );

        $this->assertEquals(
            'test1 at schema1',
            $translator->translatePlural(
                'domain.test1',
                'domain.test2',
                1
            )
        );

        $this->assertEquals(
            'test2 at schema2',
            $translator->translatePlural(
                'domain.test1',
                'domain.test2',
                2
            )
        );

        $this->assertEquals(
            '1 match',
            sprintf( $translator->translatePlural(
                'domain.matches',
                'domain.matches',
                1
            ), 1 )
        );

        $this->assertEquals(
            '2 matches',
            sprintf( $translator->translatePlural(
                'domain.matches',
                'domain.matches',
                2
            ), 2 )
        );
    }

}
