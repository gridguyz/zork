<?php

namespace Zork\Image;

use Zend\Stdlib\ResponseInterface;
use Zend\Http\Headers as HttpHeaders;
use Zend\Http\Response as HttpResponse;

/**
 * Image
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Image
{

    /**
     * Gif type
     */
    const TYPE_GIF              = IMAGETYPE_GIF;

    /**
     * Jpg/jpeg type
     */
    const TYPE_JPG              = IMAGETYPE_JPEG;

    /**
     * Jpg/jpeg type
     */
    const TYPE_JPEG             = IMAGETYPE_JPEG;

    /**
     * Png type
     */
    const TYPE_PNG              = IMAGETYPE_PNG;

    /**
     * Default jpeg output quality (0-100)
     */
    const DEFAULT_JPEG_QUALITY  = 100;

    /**
     * Default jpeg output quality (0-100)
     */
    const MAX_JPEG_QUALITY      = 100;

    /**
     * Default png output quality (0-9)
     *
     * 0 - no compression ... 9 - *full* compression
     */
    const DEFAULT_PNG_QUALITY   = 9;

    /**
     * Default png output quality (0-9)
     *
     * 0 - no compression ... 9 - *full* compression
     */
    const MAX_PNG_QUALITY       = 9;

    /**
     * Text default size (ttf)
     */
    const DEFAULT_TEXT_SIZE     = 12;

    /**
     * Png no filter
     */
    const PNG_NO_FILTER         = PNG_NO_FILTER;

    /**
     * Png filter: none
     */
    const PNG_FILTER_NONE       = PNG_FILTER_NONE;

    /**
     * Png filter: sub
     */
    const PNG_FILTER_SUB        = PNG_FILTER_SUB;

    /**
     * Png filter: up
     */
    const PNG_FILTER_UP         = PNG_FILTER_UP;

    /**
     * Png filter: avg
     */
    const PNG_FILTER_AVG        = PNG_FILTER_AVG;

    /**
     * Png filter: paeth
     */
    const PNG_FILTER_PAETH      = PNG_FILTER_PAETH;

    /**
     * Png filter: all
     */
    const PNG_ALL_FILTERS       = PNG_ALL_FILTERS;

    /**
     * Default png output filters
     */
    const DEFAULT_PNG_FILTERS   = self::PNG_NO_FILTER;

    /**
     * Stretch resize
     */
    const RESIZE_STRETCH        = 'stretch';

    /**
     * Fit resize
     */
    const RESIZE_FIT            = 'fit';

    /**
     * Frame resize
     */
    const RESIZE_FRAME          = 'frame';

    /**
     * Cut resize
     */
    const RESIZE_CUT            = 'cut';

    /**
     * Default resize
     */
    const RESIZE_DEFAULT        = self::RESIZE_FIT;

    /**
     * Negation filter
     */
    const FILTER_NEGATE         = 'negate';

    /**
     * Grayscale filter
     */
    const FILTER_GRAYSCALE      = 'grayscale';

    /**
     * Brightness filter
     */
    const FILTER_BRIGHTNESS     = 'brightness';

    /**
     * Contrast filter
     */
    const FILTER_CONTRAST       = 'contrast';

    /**
     * Colorize: red filter
     */
    const FILTER_COLORIZE_RED   = 'colorizeRed';

    /**
     * Colorize: green filter
     */
    const FILTER_COLORIZE_GREEN = 'colorizeGreen';

    /**
     * Colorize: blue filter
     */
    const FILTER_COLORIZE_BLUE  = 'colorizeBlue';

    /**
     * Colorize filter
     */
    const FILTER_COLORIZE       = 'colorize';

    /**
     * Edge-detect filter
     */
    const FILTER_EDGEDETECT     = 'edgeDetect';

    /**
     * Emboss filter
     */
    const FILTER_EMBOSS         = 'emboss';

    /**
     * Gaussian blur filter
     */
    const FILTER_GAUSSIAN_BLUR  = 'gaussianBlur';

    /**
     * Selective blur filter
     */
    const FILTER_SELECTIVE_BLUR = 'selectiveBlur';

    /**
     * Mean removal filter
     */
    const FILTER_REMOVE_MEAN    = 'removeMean';

    /**
     * Mean removal filter
     */
    const FILTER_MEAN_REMOVAL   = 'removeMean';

    /**
     * Smooth filter
     */
    const FILTER_SMOOTH         = 'smooth';

    /**
     * Pixelate filter
     */
    const FILTER_PIXELATE       = 'pixelate';

    /**
     * GD-image resource
     *
     * @var resource
     */
    protected $resource;

    /**
     * GD-type
     *
     * @var array
     */
    protected $type;

    /**
     * Width
     *
     * @var array
     */
    protected $width;

    /**
     * Height
     *
     * @var array
     */
    protected $height;

    /**
     * Is true-color
     *
     * @var bool
     */
    protected $trueColor = true;

    /**
     * Transparent index
     *
     * @var int
     */
    protected $transparentIndex = -1;

    /**
     * Color indeces
     *
     * @var array
     */
    protected $colorIndeces = array();

    /**
     * Construct from resource
     *
     * @param resource $resource
     */
    protected function __construct( $resource )
    {
        $this->resource = & $resource;
        imagealphablending( $this->resource, false );
        imagesavealpha( $this->resource, true );
    }

    /**
     * Desctructor
     */
    public function __destruct()
    {
        if ( $this->resource )
        {
            @ imagedestroy( $this->resource );
            $this->resource = null;
        }
    }

    /**
     * Cloning
     */
    public function __clone()
    {
        $width       = $this->getWidth();
        $height      = $this->getHeight();
        $newResource = static::createResource(
            $width, $height,
            $this->isTrueColor(),
            $this->getTransparent()
        );

        imagecopy(
            $newResource,
            $this->resource,
            0, 0, 0, 0,
            $width, $height
        );

        $this->replaceResource( $newResource, true );
    }

    /**
     * Replace the existing resource with a new one
     *
     * @param resource $newResource
     * @param bool $keepOld default: false
     * @return \Zork\Image\Image
     */
    protected function replaceResource( $newResource, $keepOld = false )
    {
        $oldResource            = $this->resource;
        $this->resource         = $newResource;
        $this->colorIndeces     = array();

        if ( ! $keepOld )
        {
            imagedestroy( $oldResource );
        }

        imagealphablending( $this->resource, false );
        imagesavealpha( $this->resource, true );
        return $this;
    }

    /**
     * Create a new resource
     *
     * @param int $width
     * @param int $height
     * @param bool $trueColor
     * @param null|string|\Zork\Image\Color $fillColor
     * @return resource image
     */
    protected static function createResource( $width,
                                              $height,
                                              $trueColor    = true,
                                              $fillColor    = null )
    {
        $resource = imagecreatetruecolor( $width, $height );

        imagealphablending( $resource, false );
        imagesavealpha( $resource, true );

        if ( null !== $fillColor )
        {
            if ( ! $fillColor instanceof Color )
            {
                $fillColor = Color::create( $fillColor );
            }

            if ( $trueColor )
            {
                $fillColor = imagecolorallocatealpha(
                    $resource,
                    $fillColor->getRed(),
                    $fillColor->getGreen(),
                    $fillColor->getBlue(),
                    intval( $fillColor->getAlpha() * 127 / 255 )
                );
            }
            else
            {
                $isTransparent = $fillColor->isTransparent();

                $fillColor = imagecolorallocate(
                    $resource,
                    $fillColor->getRed(),
                    $fillColor->getGreen(),
                    $fillColor->getBlue()
                );

                if ( $isTransparent )
                {
                    imagecolortransparent( $resource, $fillColor );
                }
            }

            imagefilledrectangle(
                $resource,
                0, 0, $width, $height,
                $fillColor
            );
        }

        return $resource;
    }

    /**
     * Create empty image
     *
     * @return \Zork\Image\Image
     */
    public static function create( $width, $height, $trueColor = true )
    {
        $width  = (int) $width;
        $height = (int) $height;
        $result = new static( static::createResource(
            $width, $height, $trueColor, 'transparent'
        ) );

        $result->width      = $width;
        $result->height     = $height;
        $result->trueColor  = (bool) $trueColor;
        $result->type       = $trueColor ? self::TYPE_PNG : self::TYPE_GIF;

        return $result;
    }

    /**
     * Open from path
     *
     * @return \Zork\Image\Image
     */
    public static function open( $path )
    {
        if ( ! is_file( $path ) || 1 > filesize( $path ) )
        {
            return null;
        }

        $info = @ getimagesize( $path );

        if ( ! $info )
        {
            return null;
        }

        switch ( $info[2] )
        {
            case self::TYPE_GIF:
                $result = new static( imagecreatefromgif( $path ) );
                $result->transparentIndex   = imagecolortransparent( $result->resource );
                $result->trueColor          = false;
                break;

            case self::TYPE_JPEG:
                $result = new static( imagecreatefromjpeg( $path ) );
                break;

            case self::TYPE_PNG:
                $result = new static( imagecreatefrompng( $path ) );
                break;

            // @codeCoverageIgnoreStart
            default:
                return null;
            // @codeCoverageIgnoreEnd
        }

        $result->width      = $info[0];
        $result->height     = $info[1];
        $result->type       = $info[2];
        return $result;
    }

    /**
     * Get content-type for type
     *
     * @param int $type
     * @return string
     */
    public static function typeToMimeType( $type )
    {
        return image_type_to_mime_type( $type );
    }

    /**
     * Get filename-extension for type
     *
     * @param int $type
     * @return string
     */
    public static function typeToExtension( $type, $includeDot = true )
    {
        return image_type_to_extension( $type, $includeDot );
    }

    /**
     * Is a filter exists
     *
     * @param string $name
     * @return bool
     */
    public static function isFilter( $name )
    {
        return method_exists( get_called_class(), 'filter' . ucfirst( $name ) );
    }

    /**
     * Is a resize-method exists
     *
     * @param string $name
     * @return bool
     */
    public static function isResize( $name )
    {
        return method_exists( get_called_class(), 'resize' . ucfirst( $name ) );
    }

    /**
     * Render image
     *
     * @param string|\Zend\Stdlib\ResponseInterface $to path / response
     * @param int $type
     * @param int $quality [optional] used if type is png / jpeg
     * @param int $filters [optional] used if type is png
     * @return \Zork\Image\Image
     */
    public function render( $to,
                            $type       = null,
                            $quality    = null,
                            $filters    = null )
    {
        if ( null === $type )
        {
            $type = $this->getType();
        }

        if ( $to instanceof ResponseInterface )
        {
            $resp = $to;
            $to   = null;

            if ( $resp instanceof HttpResponse )
            {
                $headers = new HttpHeaders();
                $headers->addHeaderLine( 'Content-Type',
                                         static::typeToMimeType( $type ) );

                $resp->setHeaders( $headers );
            }

            ob_start();
        }
        else
        {
            $resp = false;
        }

        switch ( $type )
        {
            case self::TYPE_GIF:
                imagegif( $this->resource, $to );
                break;

            case self::TYPE_JPEG:
                imagejpeg(
                    $this->resource, $to,
                    is_null( $quality )
                        ? self::DEFAULT_JPEG_QUALITY
                        : min( $quality, self::MAX_JPEG_QUALITY )
                );
                break;

            case self::TYPE_PNG:
                imagepng(
                    $this->resource, $to,
                    is_null( $quality )
                        ? self::DEFAULT_PNG_QUALITY
                        : min( $quality, self::MAX_PNG_QUALITY ),
                    is_null( $filters )
                        ? self::DEFAULT_PNG_FILTERS
                        : $filters
                );
                break;

            default:
                throw new Exception\OperationException( 'Type not supported' );
        }

        if ( $resp instanceof ResponseInterface )
        {
            $resp->setContent( ob_get_clean() );
        }

        return $this;
    }

    /**
     * Image is true-color
     *
     * @return bool
     */
    public function isTrueColor()
    {
        return $this->trueColor;
    }

    /**
     * Image width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Image height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Image type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Draw text on image
     *
     * @param string $text
     * @param int $left
     * @param int $top
     * @param string|\Zork\Image\Color $color
     * @param int|string|\Zork\Image\Font $font
     * @param int $size (ttf only)
     * @return Zork\Image\Image
     * @codeCoverageIgnore
     */
    public function text( $text,
                          $left,
                          $top,
                          $color,
                          $font = Font::NORMAL,
                          $size = self::DEFAULT_TEXT_SIZE )
    {
        if ( ! $font instanceof Font )
        {
            $font = Font::create( $font );
        }

        $text  = (string) $text;
        $font  = $font->getId();
        $color = $this->colorIndex( $color );

        if ( is_int( $font ) )
        {
            if ( ! imagestring( $this->resource, $font,
                                $left, $top, $text, $color ) )
            {
                throw new Exception\OperationException(
                    'Text cannot be drawn (gd)'
                );
            }
        }
        else
        {
            if ( ! imagettftext( $this->resource, $size, 0,
                                 $left, $top, $color, $font, $text ) )
            {
                throw new Exception\OperationException(
                    'Text cannot be drawn (ttf)'
                );
            }
        }

        return $this;
    }

    /**
     * Get index for a color
     *
     * @param string|\Zork\Image\Color $color
     * @return int
     */
    protected function colorIndex( $color )
    {
        if ( ! $color instanceof Color )
        {
            $color = Color::create( $color );
        }

        $hex = $color->toHex();

        if ( ! isset( $this->colorIndeces[$hex] ) )
        {
            $this->colorIndeces[$hex] = $color->isOpaque()
                ? imagecolorallocatealpha( $this->resource,
                                           $color->getRed(),
                                           $color->getGreen(),
                                           $color->getBlue(),
                                   intval( $color->getAlpha() * 127 / 255 ) )
                : imagecolorallocate( $this->resource,
                                      $color->getRed(),
                                      $color->getGreen(),
                                      $color->getBlue() );
        }

        return $this->colorIndeces[$hex];
    }

    /**
     * Get color from an index
     *
     * @param int $index
     * @return \Zork\Image\Color
     */
    protected function indexColor( $index )
    {
        $rgba = imagecolorsforindex(
            $this->resource,
            $index
        );

        return new Color(
            $rgba['red'],
            $rgba['green'],
            $rgba['blue'],
            isset( $rgba['alpha'] )
                ? intval( $rgba['alpha'] * 255 / 127 )
                : null
        );
    }

    /**
     * Get transparent color
     *
     * @return \Zork\Image\Color
     */
    public function getTransparent()
    {
        if ( $this->transparentIndex >= 0 )
        {
            return $this->indexColor( $this->transparentIndex );
        }
        else
        {
            return Color::create( 'transparent' );
        }
    }

    /**
     * Produce valid coordinates
     *
     * @param int $left
     * @param int $top
     * @param int $width
     * @param int $height
     */
    protected function validCoordinates( &$left, &$top, &$width, &$height )
    {
        $_width     = $this->getWidth();
        $_height    = $this->getHeight();

        while ( $left < 0 )
        {
            $left += $_width;
        }

        while ( $top < 0 )
        {
            $top += $_height;
        }

        if ( is_null( $width ) )
        {
            $width = $_width - $left;
        }
        else
        {
            $width = min( $width, $_width - $left );
        }

        if ( is_null( $height ) )
        {
            $height = $_height - $top;
        }
        else
        {
            $height = min( $height, $_height - $top );
        }
    }

    /**
     * Fill a rectangle
     *
     * @param string|\Zork\Image\Color $color
     * @param int $left [optional]
     * @param int $top [optional]
     * @param int $width [optional]
     * @param int $height [optional]
     * @return \Zork\Image\Image
     */
    public function fill( $color,
                          $left     = 0,
                          $top      = 0,
                          $width    = null,
                          $height   = null )
    {
        $this->validCoordinates( $left, $top, $width, $height );

        if ( ! imagefilledrectangle( $this->resource,
                                     $left, $top,
                                     $left + $width, $top + $height,
                                     $this->colorIndex( $color ) ) )
        {
            // @codeCoverageIgnoreStart
            throw new Exception\OperationException( 'Fill cannot execute' );
            // @codeCoverageIgnoreEnd
        }

        return $this;
    }

    /**
     * Get the color at the specified coordinate
     *
     * @param int $left
     * @param int $top
     * @return \Zork\Image\Color
     */
    public function colorAt( $left, $top )
    {
        $_width     = $this->getWidth();
        $_height    = $this->getHeight();

        while ( $left < 0 )
        {
            $left += $_width;
        }

        while ( $top < 0 )
        {
            $top += $_height;
        }

        return $this->indexColor( imagecolorat(
            $this->resource,
            $left, $top
        ) );
    }

    /**
     * Crop image to its portion
     *
     * @param int $left
     * @param int $top
     * @param int $width
     * @param int $height
     * @return \Zork\Image\Image
     */
    public function cropTo( $left   = 0,
                            $top    = 0,
                            $width  = null,
                            $height = null )
    {
        $this->validCoordinates( $left, $top, $width, $height );

        $newResource = static::createResource(
            $width, $height,
            $this->isTrueColor(),
            $this->getTransparent()
        );

        imagecopy(
            $newResource,
            $this->resource,
            0, 0,
            $left, $top,
            $width, $height
        );

        $this->width    = $width;
        $this->height   = $height;

        return $this->replaceResource( $newResource );
    }

    /**
     * Execute a resize
     *
     * @param int $width
     * @param int $height
     * @param string $method [optional]
     * @return \Zork\Image\Image
     * @throws \Zork\Image\Exception\ResizeException
     */
    public function resize( $width, $height, $name = self::RESIZE_DEFAULT, $bgColor = null )
    {
        $method = array( $this, 'resize' . ucfirst( $name ) );

        if ( ! is_callable( $method ) )
        {
            throw new Exception\ResizeException(
                'Resize: "' . $name . '" does not exists'
            );
        }

        if ( empty( $width ) || empty( $height ) )
        {
            throw new Exception\ResizeException(
                'Resize: width / height must ot be empty'
            );
        }

        if ( empty( $bgColor ) || Color::create( $bgColor )->isTransparent() )
        {
            $bgColor = $this->getTransparent();
        }

        $width  = (int) $width;
        $height = (int) $height;
        return call_user_func( $method, $width, $height, $bgColor );
    }

    /**
     * Resize part of current into new resource
     *
     * @param resource $dst
     * @param int $ox
     * @param int $oy
     * @param int $ix
     * @param int $iy
     * @param int $ow
     * @param int $oh
     * @param int $iw
     * @param int $ih
     * @return bool
     * @throws \Zork\Image\Exception\ResizeException
     */
    protected function innerResize( $dst, $ox, $oy, $ix, $iy, $ow, $oh, $iw, $ih )
    {
        if ( $this->transparentIndex >= 0 )
        {
            $success = imagecopyresized(
                $dst,
                $this->resource,
                $ox, $oy,
                $ix, $iy,
                $ow, $oh,
                $iw, $ih
            );
        }
        else
        {
            $success = imagecopyresampled(
                $dst,
                $this->resource,
                $ox, $oy,
                $ix, $iy,
                $ow, $oh,
                $iw, $ih
            );
        }

        if ( ! $success )
        {
            // @codeCoverageIgnoreStart
            throw new Exception\ResizeException(
                'Resize cannot execute'
            );
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Stretch resize
     *
     * @param int $width
     * @param int $height
     * @return \Zork\Image\Image
     * @throws \Zork\Image\Exception\ResizeException
     */
    public function resizeStretch( $width, $height, $bgColor = null )
    {
        $newResource = static::createResource(
            $width, $height,
            $this->isTrueColor(),
            $bgColor
        );

        $this->innerResize(
            $newResource,
            0, 0, 0, 0,
            $width,
            $height,
            $this->getWidth(),
            $this->getHeight()
        );

        $this->width    = $width;
        $this->height   = $height;

        return $this->replaceResource( $newResource );
    }

    /**
     * Fit resize
     *
     * @param int $width
     * @param int $height
     * @return \Zork\Image\Image
     * @throws \Zork\Image\Exception\ResizeException
     */
    public function resizeFit( $width, $height, $bgColor = null )
    {
        $inputWidth  = $this->getWidth();
        $inputHeight = $this->getHeight();

        if ( $inputWidth <= $width && $inputHeight <= $height )
        {
            return $this;
        }
        else
        {
            if ( $inputWidth / $inputHeight > $width / $height )
            {
                $newWidth   = $width;
                $newHeight  = max( 1, (int) round( $width * $inputHeight / $inputWidth ) );
            }
            else
            {
                $newWidth   = max( 1, (int) round( $height * $inputWidth / $inputHeight ) );
                $newHeight  = $height;
            }

            $newResource = static::createResource(
                $newWidth, $newHeight,
                $this->isTrueColor(),
                $bgColor
            );

            $this->innerResize(
                $newResource,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $inputWidth,
                $inputHeight
            );

            $this->width    = $newWidth;
            $this->height   = $newHeight;

            return $this->replaceResource( $newResource );
        }
    }

    /**
     * Frame resize
     *
     * @param int $width
     * @param int $height
     * @return \Zork\Image\Image
     * @throws \Zork\Image\Exception\ResizeException
     */
    public function resizeFrame( $width, $height, $bgColor = null )
    {
        $inputWidth  = $this->getWidth();
        $inputHeight = $this->getHeight();
        $newResource = static::createResource(
            $width, $height,
            $this->isTrueColor(),
            $bgColor
        );

        if ( $inputWidth <= $width && $inputHeight <= $height )
        {
            $newWidth   = $inputWidth;
            $newHeight  = $inputHeight;
            $newX       = (int) round( ( $width  - $newWidth  ) / 2 );
            $newY       = (int) round( ( $height - $newHeight ) / 2 );
        }
        else
        {
            if ( $inputWidth / $inputHeight > $width / $height )
            {
                $newWidth   = $width;
                $newHeight  = max( 1, (int) round( $width * $inputHeight / $inputWidth ) );
                $newX       = 0;
                $newY       = (int) round( ( $height - $newHeight ) / 2 );
            }
            else
            {
                $newWidth   = max( 1, (int) round( $height * $inputWidth / $inputHeight ) );
                $newHeight  = $height;
                $newX       = (int) round( ( $width - $newWidth ) / 2 );
                $newY       = 0;
            }
        }

        $this->innerResize(
            $newResource,
            $newX, $newY,
            0, 0,
            $newWidth,
            $newHeight,
            $inputWidth,
            $inputHeight
        );

        $this->width    = $width;
        $this->height   = $height;

        return $this->replaceResource( $newResource );
    }

    /**
     * Cut resize
     *
     * @param int $width
     * @param int $height
     * @return \Zork\Image\Image
     * @throws \Zork\Image\Exception\ResizeException
     */
    public function resizeCut( $width, $height )
    {
        $inputWidth  = $this->getWidth();
        $inputHeight = $this->getHeight();
        $newResource = static::createResource(
            $width, $height,
            $this->isTrueColor(),
            $this->getTransparent()
        );

        if ( $inputWidth / $inputHeight < $width / $height )
        {
            $newWidth   = $inputWidth;
            $newHeight  = max( 1, (int) round( ( $inputWidth / $width ) * $height ) );
            $newX       = 0;
            $newY       = (int) round( ( $inputHeight - $newHeight ) / 2 );
        }
        else
        {
            $newWidth   = max( 1, (int) round( ( $inputHeight / $height ) * $width ) );
            $newHeight  = $inputHeight;
            $newX       = (int) round( ( $inputWidth - $newWidth ) / 2 );
            $newY       = 0;
        }

        $this->innerResize(
            $newResource,
            0, 0,
            $newX, $newY,
            $width,
            $height,
            $newWidth,
            $newHeight
        );

        $this->width    = $width;
        $this->height   = $height;

        return $this->replaceResource( $newResource );
    }

    /**
     * Execute a filter
     *
     * @param string $name
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filter( $name )
    {
        $method = array( $this, 'filter' . ucfirst( $name ) );

        if ( ! is_callable( $method ) )
        {
            throw new Exception\FilterException(
                'Filter: "' . $name .
                '" does not exists'
            );
        }

        $args = func_get_args();
        array_shift( $args );

        if ( isset( $args[0] ) && is_array( $args[0] ) )
        {
            $args = $args[0];
        }

        return call_user_func_array( $method, $args );
    }

    /**
     * Filter method: negate
     *
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterNegate()
    {
        if ( ! imagefilter( $this->resource, IMG_FILTER_NEGATE ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: grayscale
     *
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterGrayscale()
    {
        if ( ! imagefilter( $this->resource, IMG_FILTER_GRAYSCALE ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: brightness
     *
     * @param int $level
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterBrightness( $level = 20 )
    {
        if ( ! imagefilter( $this->resource, IMG_FILTER_BRIGHTNESS, $level ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: contrast
     *
     * @param int $level
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterContrast( $level = 20 )
    {
        if ( ! imagefilter( $this->resource, IMG_FILTER_CONTRAST, $level ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: colorizeRed
     *
     * @param int $red [0-255]
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterColorizeRed( $red = 255 )
    {
        $opaq = intval( $opaq * 127 / 255 );

        if ( ! imagefilter( $this->resource, IMG_FILTER_COLORIZE,
                            $red, 0, 0, 0 ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: colorizeGreen
     *
     * @param int $green [0-255]
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterColorizeGreen( $green = 255 )
    {
        $opaq = intval( $opaq * 127 / 255 );

        if ( ! imagefilter( $this->resource, IMG_FILTER_COLORIZE,
                            0, $green, 0, 0 ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: colorizeBlue
     *
     * @param int $blue [0-255]
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterColorizeBlue( $blue = 255 )
    {
        $opaq = intval( $opaq * 127 / 255 );

        if ( ! imagefilter( $this->resource, IMG_FILTER_COLORIZE,
                            0, 0, $blue, 0 ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: colorize
     *
     * @param string|\Zork\Image\Color $color
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterColorize( $color = 'gray' )
    {
        if ( ! $color instanceof Color )
        {
            $color = Color::create( $color );
        }

        if ( ! imagefilter( $this->resource, IMG_FILTER_COLORIZE,
                            $color->getRed(),
                            $color->getGreen(),
                            $color->getBlue(),
                            intval( $color->getAlpha() * 127 / 255 ) ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }
    }

    /**
     * Filter method: edgeDetect
     *
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterEdgeDetect()
    {
        if ( ! imagefilter( $this->resource, IMG_FILTER_EDGEDETECT ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: emboss
     *
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterEmboss()
    {
        if ( ! imagefilter( $this->resource, IMG_FILTER_EMBOSS ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: gaussianBlur
     *
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterGaussianBlur()
    {
        if ( ! imagefilter( $this->resource, IMG_FILTER_GAUSSIAN_BLUR ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: selectiveBlur
     *
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterSelectiveBlur()
    {
        $width  = $this->getWidth();
        $height = $this->getHeight();
        $tmpimg = static::createResource(
            $width + 1, $height + 1,
            $this->isTrueColor(),
            $this->getTransparent()
        );

        imagecopy( $tmpimg, $this->resource, 0, 0, 0, 0, $width, $height );
        $success = imagefilter( $tmpimg, IMG_FILTER_SELECTIVE_BLUR );

        if ( $success )
        {
            imagecopy( $this->resource, $tmpimg, 0, 0, 0, 0, $width, $height );
        }

        imagedestroy( $tmpimg );

        if ( ! $success )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: removeMean
     *
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterRemoveMean()
    {
        if ( ! imagefilter( $this->resource, IMG_FILTER_MEAN_REMOVAL ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: smooth
     *
     * @param int $level
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterSmooth( $level = 20 )
    {
        if ( ! imagefilter( $this->resource, IMG_FILTER_SMOOTH, $level ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

    /**
     * Filter method: pixelate
     *
     * @param int $blockSize
     * @return \Zork\Image\Image
     * @throws \Zork\Image\FilterException
     * @codeCoverageIgnore
     */
    public function filterPixelate( $blockSize = 10 )
    {
        if ( ! imagefilter( $this->resource, IMG_FILTER_PIXELATE,
                            min( (int) $blockSize, 2 ), false ) )
        {
            throw new Exception\FilterException( 'Filter cannot execute' );
        }

        return $this;
    }

}
