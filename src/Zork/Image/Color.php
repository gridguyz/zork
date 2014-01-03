<?php

namespace Zork\Image;

/**
 * Color
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Color
{

    /**
     * Red channel
     *
     * @var int
     */
    protected $red;

    /**
     * Green channel
     *
     * @var int
     */
    protected $green;

    /**
     * Blue channel
     *
     * @var int
     */
    protected $blue;

    /**
     * Alpha channel
     *
     * @var int
     */
    protected $alpha;

    /**
     * Colors by name
     *
     * @var array
     */
    protected static $names = array(
        'transparent'   => array( 128, 128, 128, 255 ),
        'aqua'          => array(   0, 255, 255,   0 ),
        'black'         => array(   0,   0,   0,   0 ),
        'blue'          => array(   0,   0, 255,   0 ),
        'cyan'          => array(   0, 255, 255,   0 ),
        'fuchsia'       => array( 255,   0, 255,   0 ),
        'gray'          => array( 128, 128, 128,   0 ),
        'grey'          => array( 128, 128, 128,   0 ),
        'green'         => array(   0, 128,   0,   0 ),
        'lime'          => array(   0, 255,   0,   0 ),
        'magenta'       => array( 255,   0, 255,   0 ),
        'maroon'        => array( 128,   0,   0,   0 ),
        'navy'          => array(   0,   0, 128,   0 ),
        'olive'         => array( 128, 128,   0,   0 ),
        'purple'        => array( 128,   0, 128,   0 ),
        'red'           => array( 255,   0,   0,   0 ),
        'teal'          => array(   0, 128, 128,   0 ),
        'white'         => array( 255, 255, 255,   0 ),
        'yellow'        => array( 255, 255,   0,   0 ),
    );

    /**
     * Constructor
     *
     * @param   int     $red
     * @param   int     $green
     * @param   int     $blue
     * @param   int     $alpha
     */
    public function __construct( $red, $green, $blue, $alpha = 0 )
    {
        $this->red      = min( max( (int) $red,   0 ), 255 );
        $this->green    = min( max( (int) $green, 0 ), 255 );
        $this->blue     = min( max( (int) $blue,  0 ), 255 );
        $this->alpha    = min( max( (int) $alpha, 0 ), 255 );
    }

    /**
     * Get color from name
     *
     * @param   string  $name
     * @return  Color
     */
    public static function fromName( $name )
    {
        $result = new static( 0, 0, 0, 0 );

        if ( ! empty( static::$names[$name] ) )
        {
            list( $result->red,
                  $result->green,
                  $result->blue,
                  $result->alpha ) = static::$names[$name];
        }

        return $result;
    }

    /**
     * Get color from name
     *
     * @param   string  $hex in "rrggbb[aa]" format
     * @return  Color
     */
    public static function fromHex( $hex )
    {
        $hex = (string) $hex;

        return new static(
            hexdec( substr( $hex, 0, 2 ) ),
            hexdec( substr( $hex, 2, 2 ) ),
            hexdec( substr( $hex, 4, 2 ) ),
            hexdec( substr( $hex, 6, 2 ) )
        );
    }

    /**
     * Create a color from name / hex
     *
     * @param   string  $from name or hex
     * @return  Color
     */
    public static function create( $from )
    {
        return isset( static::$names[$from] )
            ? static::fromName( $from )
            : static::fromHex( $from );
    }

    /**
     * Convert to hex form
     *
     * @return  string
     */
    public function toHex()
    {
        $alpha  = $this->getAlpha();
        $result = str_pad( dechex( $this->getRed()   ), 2, '0', STR_PAD_LEFT )
                . str_pad( dechex( $this->getGreen() ), 2, '0', STR_PAD_LEFT )
                . str_pad( dechex( $this->getBlue()  ), 2, '0', STR_PAD_LEFT );

        if ( $alpha > 0 )
        {
            $result .= str_pad( dechex( $alpha ), 2, '0', STR_PAD_LEFT );
        }

        return $result;
    }

    /**
     * Convert to string
     */
    public function __toString()
    {
        return $this->toHex();
    }

    /**
     * Get the red channel
     *
     * @return  int
     */
    public function getRed()
    {
        return $this->red;
    }

    /**
     * Set the red channel
     *
     * @param   int     $value
     * @return  Color
     */
    public function setRed( $value )
    {
        $this->red = min( max( (int) $value, 0 ), 255 );
        return $this;
    }

    /**
     * Get the green channel
     *
     * @return  int
     */
    public function getGreen()
    {
        return $this->green;
    }

    /**
     * Set the green channel
     *
     * @param   int     $value
     * @return  Color
     */
    public function setGreen( $value )
    {
        $this->green = min( max( (int) $value, 0 ), 255 );
        return $this;
    }

    /**
     * Get the blue channel
     *
     * @return  int
     */
    public function getBlue()
    {
        return $this->blue;
    }

    /**
     * Set the blue channel
     *
     * @param   int     $value
     * @return  Color
     */
    public function setBlue( $value )
    {
        $this->blue = min( max( (int) $value, 0 ), 255 );
        return $this;
    }

    /**
     * Get the alpha channel
     *
     * @return  int
     */
    public function getAlpha()
    {
        return $this->alpha;
    }

    /**
     * Set the alpha channel
     *
     * @param   int     $value
     * @return  Color
     */
    public function setAlpha( $value )
    {
        $this->alpha = min( max( (int) $value, 0 ), 255 );
        return $this;
    }

    /**
     * This color is a transparent color?
     *
     * @return bool
     */
    public function isTransparent()
    {
        return $this->alpha == 255;
    }

    /**
     * This color is a semi-opaque color?
     *
     * @return bool
     */
    public function isOpaque()
    {
        return 0 < $this->alpha;
    }

    /**
     * Get the distance of 2 colors
     *
     * @param   Color|mixed $color
     * @param   bool        $alpha
     * @return  float
     */
    public function distance( $color, $alpha = false )
    {
        static $wr = 0.299;
        static $wg = 0.587;
        static $wb = 0.114;
        static $um = 0.436;
        static $vm = 0.615;

        $r1 = $this->red / 255;
        $g1 = $this->green / 255;
        $b1 = $this->blue / 255;
        $a1 = $this->alpha / 255;
        $y1 = $wr * $r1 + $wg * $g1 + $wb * $b1;
        $u1 = $um * ( $b1 - $y1 ) / ( 1 - $wb ) + $um;
        $v1 = $vm * ( $r1 - $y1 ) / ( 1 - $wr ) + $vm;

        if ( ! $color instanceof self )
        {
            $color = static::create( $color );
        }

        $r2 = $color->red / 255;
        $g2 = $color->green / 255;
        $b2 = $color->blue / 255;
        $a2 = $color->alpha / 255;
        $y2 = $wr * $r2 + $wg * $g2 + $wb * $b2;
        $u2 = $um * ( $b2 - $y2 ) / ( 1 - $wb ) + $um;
        $v2 = $vm * ( $r2 - $y2 ) / ( 1 - $wr ) + $vm;

        return sqrt(
            pow( $y1 - $y2, 2 ) +
            pow( $u1 - $u2, 2 ) +
            pow( $v1 - $v2, 2 ) +
            ( $alpha ? pow( $a1 - $a2, 2 ) : 0 )
        );
    }

}
