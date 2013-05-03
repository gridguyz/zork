<?php

namespace Zork\Image;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * ColorTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Image\Color
 */
class ColorTest extends TestCase
{

    /**
     * Test create basic
     */
    public function testCreateBasic()
    {
        $color = new Color( 3, 2, 1 );

        $this->assertEquals( 3, $color->getRed() );
        $this->assertEquals( 2, $color->getGreen() );
        $this->assertEquals( 1, $color->getBlue() );
        $this->assertEquals( 0, $color->getAlpha() );

        $color->setRed( 0 )
              ->setGreen( 1 )
              ->setBlue( 2 )
              ->setAlpha( 3 );

        $this->assertEquals( 0, $color->getRed() );
        $this->assertEquals( 1, $color->getGreen() );
        $this->assertEquals( 2, $color->getBlue() );
        $this->assertEquals( 3, $color->getAlpha() );

        $color->setAlpha( 255 );
        $this->assertTrue( $color->isTransparent() );
        $this->assertTrue( $color->isOpaque() );

        $color->setAlpha( 127 );
        $this->assertFalse( $color->isTransparent() );
        $this->assertTrue( $color->isOpaque() );

        $color->setAlpha( 0 );
        $this->assertFalse( $color->isTransparent() );
        $this->assertFalse( $color->isOpaque() );
    }

    /**
     * Test create by name
     */
    public function testCreateByName()
    {
        $color = Color::create( 'transparent' );

        $this->assertEquals( 255, $color->getAlpha() );
        $this->assertTrue( $color->isTransparent() );
        $this->assertTrue( $color->isOpaque() );

        $this->assertEquals( new Color( 128, 0, 0 ), Color::create( 'maroon' ) );
        $this->assertEquals( new Color( 255, 0, 0 ), Color::create( 'red' ) );
        $this->assertEquals( new Color( 0, 128, 0 ), Color::create( 'green' ) );
        $this->assertEquals( new Color( 0, 255, 0 ), Color::create( 'lime' ) );
        $this->assertEquals( new Color( 0, 0, 128 ), Color::create( 'navy' ) );
        $this->assertEquals( new Color( 0, 0, 255 ), Color::create( 'blue' ) );
    }

    /**
     * Test create by hex
     */
    public function testCreateByHex()
    {
        $color = Color::create( '0abcdeff' );

        $this->assertEquals( 255, $color->getAlpha() );
        $this->assertTrue( $color->isTransparent() );
        $this->assertTrue( $color->isOpaque() );

        $this->assertEquals( new Color( 128, 0, 0 ), Color::create( '800000' ) );
        $this->assertEquals( new Color( 255, 0, 0 ), Color::create( 'ff0000' ) );
        $this->assertEquals( new Color( 0, 128, 0 ), Color::create( '008000' ) );
        $this->assertEquals( new Color( 0, 255, 0 ), Color::create( '00ff00' ) );
        $this->assertEquals( new Color( 0, 0, 128 ), Color::create( '000080' ) );
        $this->assertEquals( new Color( 0, 0, 255 ), Color::create( '0000ff' ) );
    }

    /**
     * Test convert to hex
     */
    public function testToHex()
    {
        $this->assertEquals( '800000', Color::create( '800000' )->toHex() );
        $this->assertEquals( 'ff0000', Color::create( 'ff0000' )->toHex() );
        $this->assertEquals( '008000', Color::create( '008000' )->toHex() );
        $this->assertEquals( '00ff00', Color::create( '00ff00' )->toHex() );
        $this->assertEquals( '000080', Color::create( '000080' )->toHex() );
        $this->assertEquals( '0000ff', Color::create( '0000ff' )->toHex() );
        $this->assertEquals( '01234567', (string) Color::create( '01234567' ) );
    }

}
