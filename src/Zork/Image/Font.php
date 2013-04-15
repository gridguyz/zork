<?php

namespace Zork\Image;

/**
 * Font
 * 
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @codeCoverageIgnore
 */
class Font
{
    
    /**
     * Font sized 1
     */
    const SIZE_1        = 1;
    
    /**
     * Font sized 2
     */
    const SIZE_2        = 2;
    
    /**
     * Font sized 3
     */
    const SIZE_3        = 3;
    
    /**
     * Font sized 4
     */
    const SIZE_4        = 4;
    
    /**
     * Font sized 5
     */
    const SIZE_5        = 5;
    
    /**
     * Font sized tiny
     */
    const TINY          = self::SIZE_1;
    
    /**
     * Font sized small
     */
    const SMALL         = self::SIZE_2;
    
    /**
     * Font sized medium
     */
    const MEDIUM        = self::SIZE_3;
    
    /**
     * Font sized normal
     */
    const NORMAL        = self::SIZE_4;
    
    /**
     * Font sized large
     */
    const LARGE         = self::SIZE_5;
    
    /**
     * Identification
     * 
     * @var int|string
     */
    protected $id;
    
    /**
     * Identification
     * 
     * @var int|string
     */
    protected $size;
    
    /**
     * Instance cache
     * 
     * @var array
     */
    protected static $cache = array();
    
    /**
     * Constructor
     * 
     * @param int|string $id 
     */
    public function __construct( $id )
    {
        $id = (string) $id;
        
        switch ( true )
        {
            case is_numeric( $id ) && $id >= 1 && $id < 6:
                $this->id = (int) $id;
                break;
            
            case preg_match( '/\.ttf$/', $id ):
                $this->id = realpath( $id );
                break;
            
            default:
                $this->id = imageloadfont( $id );
        }
    }
    
    /**
     * Create instance
     * 
     * @param int|string $id 
     * @return \Zork\Image\Font
     */
    public static function create( $id )
    {
        if ( empty( static::$cache[$id] ) )
        {
            static::$cache[$id] = new static( $id );
        }
        
        return static::$cache[$id];
    }
    
    /**
     * Get id of the font
     * 
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Print text
     * 
     * @param \Zork\Image\Image $image
     * @param string $text
     * @param int $left
     * @param int $top
     * @return \Zork\Image\Font
     * @codeCoverageIgnore
     */
    public function text( Image $image, $text, $left, $top, $color )
    {
        $image->text( $text, $left, $top, $color, $this );
        return $this;
    }
    
}
