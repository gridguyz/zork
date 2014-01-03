<?php

namespace Zork\Image;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * ImageTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Image\Image
 */
class ImageTest extends TestCase
{

    /**
     * Test static methods
     */
    public function testStatic()
    {
         // JPG is just an alias
        $this->assertEquals( Image::TYPE_JPEG, Image::TYPE_JPG );

        $this->assertEquals( 'image/gif', Image::typeToMimeType( Image::TYPE_GIF ) );
        $this->assertEquals( 'image/png', Image::typeToMimeType( Image::TYPE_PNG ) );
        $this->assertEquals( 'image/jpeg', Image::typeToMimeType( Image::TYPE_JPEG ) );

        $this->assertEquals( '.gif', Image::typeToExtension( Image::TYPE_GIF ) );
        $this->assertEquals( '.png', Image::typeToExtension( Image::TYPE_PNG ) );
        $this->assertEquals( '.jpeg', Image::typeToExtension( Image::TYPE_JPEG ) );

        $this->assertEquals( 'gif', Image::typeToExtension( Image::TYPE_GIF, false ) );
        $this->assertEquals( 'png', Image::typeToExtension( Image::TYPE_PNG, false ) );
        $this->assertEquals( 'jpeg', Image::typeToExtension( Image::TYPE_JPEG, false ) );

        $this->assertTrue( Image::isResize( Image::RESIZE_DEFAULT ) );
        $this->assertTrue( Image::isResize( Image::RESIZE_CUT ) );
        $this->assertTrue( Image::isResize( Image::RESIZE_FIT ) );
        $this->assertTrue( Image::isResize( Image::RESIZE_FRAME ) );
        $this->assertTrue( Image::isResize( Image::RESIZE_STRETCH ) );

        $this->assertTrue( Image::isFilter( Image::FILTER_BRIGHTNESS ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_COLORIZE ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_COLORIZE_BLUE ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_COLORIZE_GREEN ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_COLORIZE_RED ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_CONTRAST ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_EDGEDETECT ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_EMBOSS ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_GAUSSIAN_BLUR ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_GRAYSCALE ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_MEAN_REMOVAL ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_NEGATE ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_PIXELATE ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_REMOVE_MEAN ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_SELECTIVE_BLUR ) );
        $this->assertTrue( Image::isFilter( Image::FILTER_SMOOTH ) );
    }

    /**
     * Test opening an image file
     */
    public function testOpen()
    {
        $image = Image::open( __DIR__ . '/_files/sample.gif' );

        $this->assertEquals( Image::TYPE_GIF, $image->getType() );
        $this->assertFalse( $image->isTrueColor() );
        $this->assertEquals( 200, $image->getWidth() );
        $this->assertEquals( 150, $image->getHeight() );

        $image = null;

        $image = Image::open( __DIR__ . '/_files/sample.jpg' );

        $this->assertEquals( Image::TYPE_JPEG, $image->getType() );
        $this->assertTrue( $image->isTrueColor() );
        $this->assertEquals( 200, $image->getWidth() );
        $this->assertEquals( 150, $image->getHeight() );

        $image = null;

        $image = Image::open( __DIR__ . '/_files/sample.png' );

        $this->assertEquals( Image::TYPE_PNG, $image->getType() );
        $this->assertTrue( $image->isTrueColor() );
        $this->assertEquals( 200, $image->getWidth() );
        $this->assertEquals( 150, $image->getHeight() );

        $image = null;

        $this->assertEquals( null, Image::open( __DIR__ . '/_files/non-existent' ) );
        $this->assertEquals( null, Image::open( __DIR__ . '/_files/non-image' ) );
        $this->assertEquals( null, Image::open( __DIR__ . '/_files/empty' ) );
    }

    /**
     * Test create and clone
     */
    public function testCreateAndClone()
    {
        $image = Image::create( 200, 150, false );

        $this->assertEquals( Image::TYPE_GIF, $image->getType() );
        $this->assertFalse( $image->isTrueColor() );
        $this->assertEquals( 200, $image->getWidth() );
        $this->assertEquals( 150, $image->getHeight() );

        $image = null;
        $image = Image::create( 200, 150 );

        $this->assertEquals( Image::TYPE_PNG, $image->getType() );
        $this->assertTrue( $image->isTrueColor() );
        $this->assertEquals( 200, $image->getWidth() );
        $this->assertEquals( 150, $image->getHeight() );

        $clone = clone $image;
        $image = null;

        $this->assertEquals( Image::TYPE_PNG, $clone->getType() );
        $this->assertTrue( $clone->isTrueColor() );
        $this->assertEquals( 200, $clone->getWidth() );
        $this->assertEquals( 150, $clone->getHeight() );

        $clone = null;
    }

    /**
     * Test color fill & crop
     */
    public function testColorFillCrop()
    {
        $image = Image::create( 200, 150, false );
        $this->assertTrue( $image->getTransparent()->isTransparent() );

        $image = Image::create( 200, 150 );
        $this->assertTrue( $image->getTransparent()->isTransparent() );

        $color = Color::create( 'red' );
        $image->fill( $color );
        $this->assertEquals( $color, $image->colorAt( 100, 75 ) );

        $color = Color::create( 'green' );
        $image->fill( $color, 50, 50, 100, 50 );

        $this->assertEquals( $color, $image->colorAt( 100, 75 ) );

        $color = '0000ffff';
        $image->fill( $color, -150, -50 );

        $this->assertEquals(
            Color::create( $color ),
            $image->colorAt( -100, -25 )
        );

        $image->cropTo( 50, 50, 100, 50 );

        $this->assertEquals( 100, $image->getWidth() );
        $this->assertEquals( 50, $image->getHeight() );
        $this->assertEquals(
            Color::create( 'green' ),
            $image->colorAt( 50, 25 )
        );

        $image = null;
    }

    /**
     * @var array
     */
    protected static $resizes = array(
        Image::RESIZE_CUT      => 'sample.gif',
        Image::RESIZE_FIT      => 'sample.jpg',
        Image::RESIZE_FRAME    => 'sample.png',
        Image::RESIZE_STRETCH  => 'sample.png',
    );

    /**
     * Assert 2 image files are equals (within a tolerance)
     *
     * @param   string  $expected
     * @param   string  $actual
     * @param   string  $message
     * @param   float   $downscale
     * @param   float   $tolerance
     * @param   bool    $alpha
     */
    protected function assertImageFileEquals( $expected,
                                              $actual,
                                              $message   = '',
                                              $downscale = 5,
                                              $tolerance = 0.1,
                                              $alpha     = false )
    {
        $expectedInfo   = getimagesize( $expected );
        $actualInfo     = getimagesize( $actual );

        $this->assertSame(
            array_slice( $expectedInfo, 0, 3 ),
            array_slice( $actualInfo, 0, 3 ),
            $message
        );

        list( $width, $height, $type ) = $actualInfo;
        $scaleToWidth   = max( 1, intval( $width / $downscale ) );
        $scaleToHeight  = max( 1, intval( $height / $downscale ) );

        $this->assertContains(
            $type,
            array(
                IMAGETYPE_GIF,
                IMAGETYPE_JPEG,
                IMAGETYPE_PNG
            ),
            'Image types are not comparable'
        );

        switch ( $type )
        {
            case IMAGETYPE_GIF:
                $expectedInput  = imagecreatefromgif( $expected );
                $actualInput    = imagecreatefromgif( $actual );
                break;

            case IMAGETYPE_JPEG:
                $expectedInput  = imagecreatefromjpeg( $expected );
                $actualInput    = imagecreatefromjpeg( $actual );
                break;

            case IMAGETYPE_PNG:
                $expectedInput  = imagecreatefrompng( $expected );
                $actualInput    = imagecreatefrompng( $actual );
                break;

            default:
                throw new \Exception;
        }

        $expectedCompare = imagecreatetruecolor( $scaleToWidth, $scaleToHeight );
        $actualCompare   = imagecreatetruecolor( $scaleToWidth, $scaleToHeight );

        imagecopyresampled(
            $expectedCompare,
            $expectedInput,
            0, 0, 0, 0,
            $scaleToWidth,
            $scaleToHeight,
            $width,
            $height
        );

        imagecopyresampled(
            $actualCompare,
            $actualInput,
            0, 0, 0, 0,
            $scaleToWidth,
            $scaleToHeight,
            $width,
            $height
        );

        $distance = 0;

        for ( $x = 0; $x < $scaleToWidth; $x++ )
        {
            for ( $y = 0; $y < $scaleToHeight; $y++ )
            {
                $expectedIndex  = imagecolorat( $expectedCompare, $x, $y );
                $actualIndex    = imagecolorat( $actualCompare, $x, $y );
                @ list( $er, $eg, $eb, $ea ) = imagecolorsforindex( $expectedCompare, $expectedIndex );
                @ list( $ar, $ag, $ab, $aa ) = imagecolorsforindex( $actualCompare, $actualIndex );
                $ea = $alpha ? intval( $ea * 255 / 127 ) : 0;
                $aa = $alpha ? intval( $aa * 255 / 127 ) : 0;
                $expectedColor  = new Color( $er, $eg, $eb, $ea );
                $actualColor    = new Color( $ar, $ag, $ab, $aa );
                $distance += $expectedColor->distance( $actualColor, $alpha );
            }
        }

        $this->assertLessThanOrEqual(
            $tolerance,
            $distance / $scaleToWidth / $scaleToHeight,
            $message
        );
    }

    /**
     * Test render & resizes
     */
    public function testRenderAndResizes()
    {
        foreach ( self::$resizes as $method => $file)
        {
            $image = Image::open( __DIR__ . '/_files/' . $file );
            $file  = $method . Image::typeToExtension( $image->getType() );

            $image->resize( 100, 100, $method );
            $image->render( __DIR__ . '/_files/~' . $file );

            $this->assertImageFileEquals(
                __DIR__ . '/_files/' . $file,
                __DIR__ . '/_files/~' . $file
            );

            unlink( __DIR__ . '/_files/~' . $file );

            $image = null;
        }
    }

    /**
     * Test resize with unknown method
     *
     * @expectedException   Zork\Image\Exception\ResizeException
     */
    public function testResizeWithUnknownMethod()
    {
        $image = Image::create( 100, 100 );
        $image->resize( 50, 50, 'nonExists' );
    }

    /**
     * Test resize with unknown width
     *
     * @expectedException   Zork\Image\Exception\ResizeException
     */
    public function testResizeWithUnknownWidth()
    {
        $image = Image::create( 100, 100 );
        $image->resize( null, 50, Image::RESIZE_DEFAULT );
    }

    /**
     * Test resize with unknown height
     *
     * @expectedException   Zork\Image\Exception\ResizeException
     */
    public function testResizeWithUnknownHeight()
    {
        $image = Image::create( 100, 100 );
        $image->resize( 50, null, Image::RESIZE_DEFAULT );
    }

    /**
     * Test resize to too small
     *
     * @expectedException   Zork\Image\Exception\ResizeException
     */
    public function testResizeTooSmall()
    {
        $image = Image::create( 100, 100 );
        $image->resize( 0, 0, Image::RESIZE_DEFAULT );
    }

    /**
     * Test render with unknown type
     *
     * @expectedException   Zork\Image\Exception\ExceptionInterface
     */
    public function testRenderWithUnknownType()
    {
        $image = Image::create( 100, 100 );
        $image->render( '/tmp/~' . uniqid(), -1 );
    }

}
