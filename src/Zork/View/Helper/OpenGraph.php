<?php

namespace Zork\View\Helper;

use Traversable;
use ArrayAccess;
use Zend\View\Helper\HeadMeta;
use Zend\View\Helper\HeadLink;
use Zend\View\Helper\AbstractHelper;

/**
 * HtmlTag
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class OpenGraph extends AbstractHelper
             implements ArrayAccess
{

    /**
     * @const string
     */
    const PREFIX_OG = 'http://ogp.me/ns#';

    /**
     * @const string
     */
    const PREFIX_MUSIC = 'http://ogp.me/ns/music#';

    /**
     * @const string
     */
    const PREFIX_VIDEO = 'http://ogp.me/ns/video#';

    /**
     * @const string
     */
    const PREFIX_ARTICLE = 'http://ogp.me/ns/article#';

    /**
     * @const string
     */
    const PREFIX_BOOK = 'http://ogp.me/ns/book#';

    /**
     * @const string
     */
    const PREFIX_PROFILE = 'http://ogp.me/ns/profile#';

    /**
     * @const string
     */
    const PREFIX_WEBSITE = 'http://ogp.me/ns/website#';

    /**
     * @const string
     */
    const TYPE_MUSIC_SONG = 'music.song';

    /**
     * @const string
     */
    const TYPE_MUSIC_ALBUM = 'music.album';

    /**
     * @const string
     */
    const TYPE_MUSIC_PLAYLIST = 'music.playlist';

    /**
     * @const string
     */
    const TYPE_MUSIC_RADIOSTATION = 'music.radio_station';

    /**
     * @const string
     */
    const TYPE_VIDEO_MOVIE = 'video.movie';

    /**
     * @const string
     */
    const TYPE_VIDEO_EPISODE = 'video.episode';

    /**
     * @const string
     */
    const TYPE_VIDEO_TVSHOW = 'video.tv_show';

    /**
     * @const string
     */
    const TYPE_VIDEO_OTHER = 'video.other';

    /**
     * @const string
     */
    const TYPE_ARTICLE = 'article';

    /**
     * @const string
     */
    const TYPE_BOOK = 'book';

    /**
     * @const string
     */
    const TYPE_PROFILE = 'profile';

    /**
     * @const string
     */
    const TYPE_WEBSITE = 'website';

    /**
     * @var \Zend\View\Helper\HeadMeta
     */
    protected $headMetaHelper;

    /**
     * @var \Zend\View\Helper\HeadLink
     */
    protected $headLinkHelper;

    /**
     * @var \Zork\View\Helper\HeadTitle
     */
    protected $headTitleHelper;

    /**
     * @var array
     */
    protected $properties = array(
        'type' => array(
            'property'  => 'og:type',
            'content'   => self::TYPE_WEBSITE,
        ),
    );

    /**
     * @var array
     */
    protected $prefixes = array(
        'og' => self::PREFIX_OG,
    );

    /**
     * Automatic type-prefixes
     *
     * @var array
     */
    protected static $autoTypePrefixes = array(
        self::TYPE_ARTICLE              => array( 'article' => self::PREFIX_ARTICLE ),
        self::TYPE_BOOK                 => array( 'book'    => self::PREFIX_BOOK ),
        self::TYPE_MUSIC_ALBUM          => array( 'music'   => self::PREFIX_MUSIC ),
        self::TYPE_MUSIC_PLAYLIST       => array( 'music'   => self::PREFIX_MUSIC ),
        self::TYPE_MUSIC_RADIOSTATION   => array( 'music'   => self::PREFIX_MUSIC ),
        self::TYPE_MUSIC_SONG           => array( 'music'   => self::PREFIX_MUSIC ),
        self::TYPE_PROFILE              => array( 'profile' => self::PREFIX_PROFILE ),
        self::TYPE_VIDEO_EPISODE        => array( 'video'   => self::PREFIX_VIDEO ),
        self::TYPE_VIDEO_MOVIE          => array( 'video'   => self::PREFIX_VIDEO ),
        self::TYPE_VIDEO_OTHER          => array( 'video'   => self::PREFIX_VIDEO ),
        self::TYPE_VIDEO_TVSHOW         => array( 'video'   => self::PREFIX_VIDEO ),
     // self::TYPE_WEBSITE              => array( 'website' => self::PREFIX_WEBSITE ),
    );

    /**
     * Retrieve the HeadMeta helper
     *
     * @return \Zend\View\Helper\HeadMeta
     */
    protected function getHeadMetaHelper()
    {
        if ( $this->headMetaHelper )
        {
            return $this->headMetaHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->headMetaHelper = $this->view
                                         ->plugin( 'headMeta' );
        }

        if ( ! $this->headMetaHelper instanceof HeadMeta )
        {
            $this->headMetaHelper = new HeadMeta();
        }

        return $this->headMetaHelper;
    }

    /**
     * Retrieve the HeadLink helper
     *
     * @return \Zend\View\Helper\HeadLink
     */
    protected function getHeadLinkHelper()
    {
        if ( $this->headLinkHelper )
        {
            return $this->headLinkHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->headLinkHelper = $this->view
                                         ->plugin( 'headLink' );
        }

        if ( ! $this->headLinkHelper instanceof HeadLink )
        {
            $this->headLinkHelper = new HeadLink();
        }

        return $this->headLinkHelper;
    }

    /**
     * Retrieve the HeadTitle helper
     *
     * @return \Zork\View\Helper\HeadTitle
     */
    protected function getHeadTitleHelper()
    {
        if ( $this->headTitleHelper )
        {
            return $this->headTitleHelper;
        }

        if ( method_exists( $this->view, 'plugin' ) )
        {
            $this->headTitleHelper = $this->view
                                          ->plugin( 'headTitle' );
        }

        if ( ! $this->headTitleHelper instanceof HeadTitle )
        {
            $this->headTitleHelper = new HeadTitle();
        }

        return $this->headTitleHelper;
    }

    /**
     * Invokable helper
     *
     * @param   array|string    $set
     * @return  string|\Zork\View\Helper\HtmlTag
     */
    public function __invoke( $set = null  )
    {
        if ( null !== $set )
        {
            if ( is_scalar( $set ) )
            {
                return $this->setType( $set );
            }

            return $this->addPrefixes( $set );
        }

        return $this;
    }

    /**
     * Get property
     *
     * @param   int|string  $offset
     * @return  array
     */
    public function & __get( $offset )
    {
        return $this->offsetGet( $offset );
    }

    /**
     * Get property
     *
     * @param   int|string  $offset
     * @return  array
     */
    public function & offsetGet( $offset )
    {
        return $this->properties[$offset];
    }

    /**
     * Set property
     *
     * @param   int|string  $offset
     * @param   array       $value
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function __set( $offset, $value )
    {
        return $this->offsetSet( $offset, $value );
    }

    /**
     * Set property
     *
     * @param   int|string  $offset
     * @param   array       $value
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function offsetSet( $offset, $value )
    {
        if ( null === $offset )
        {
            $this->properties[] = (array) $value;
        }
        else
        {
            $this->properties[$offset] = (array) $value;
        }

        return $this;
    }

    /**
     * Property exists
     *
     * @param   int|string  $offset
     * @return  bool
     */
    public function __isset( $offset )
    {
        return $this->offsetExists( $offset );
    }

    /**
     * Property exists
     *
     * @param   int|string  $offset
     * @return  bool
     */
    public function offsetExists( $offset )
    {
        return isset( $this->properties[$offset] );
    }

    /**
     * Unset property
     *
     * @param   int|string  $offset
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function __unset( $offset )
    {
        return $this->offsetUnset( $offset );
    }

    /**
     * Unset property
     *
     * @param   int|string  $offset
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function offsetUnset( $offset )
    {
        unset( $this->properties[$offset] );
        return $this;
    }

    /**
     * Set OpenGraph object-type
     *
     * @param   string  $type
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function setType( $type )
    {
        if ( isset( $this['type'] ) )
        {
            $this['type']['content'] = $type;
        }
        else
        {
            $this['type'] = array(
                'property'  => 'og:type',
                'content'   => $type,
            );
        }

        if ( ! empty( static::$autoTypePrefixes[$type] ) )
        {
            $this->addPrefixes( static::$autoTypePrefixes[$type] );
        }

        return $this;
    }

    /**
     * Set prefixes
     *
     * @param   array|\Traversable  $prefixes
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function addPrefixes( $prefixes )
    {
        if ( ! empty( $prefixes ) )
        {
            foreach ( $prefixes as $prefix => $ns )
            {
                $this->prefixes[$prefix] = (string) $ns;
            }
        }

        return $this;
    }

    /**
     * Append property / properties
     *
     * @param   array|string    $property
     * @param   null|string     $content
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function append( $property, $content = null  )
    {
        if ( null === $property )
        {
            return $this;
        }

        if ( is_scalar( $property ) )
        {
            $this[] = array(
                'property'  => (string) $property,
                'content'   => (string) $content,
            );
        }
        else if ( ! empty( $property ) )
        {
            foreach ( $property as $key => $value )
            {
                if ( $value instanceof Traversable || is_array( $value ) )
                {
                    $this->append( $value );
                }
                else
                {
                    $this[] = array(
                        'property'  => (string) $key,
                        'content'   => (string) $value,
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Get prefix attribute's value
     *
     * @return  string
     */
    public function getPrefixAttribute()
    {
        if ( method_exists( $this->view, 'plugin' ) )
        {
            foreach ( $this->properties as $property )
            {
                $this->getHeadMetaHelper()
                     ->append( (object) ( $property + array(
                         'type'         => 'property',
                         'modifiers'    => array(),
                     ) ) );
            }
        }

        $attribute = array();

        foreach ( $this->prefixes as $prefix => $ns )
        {
            $attribute[] = $prefix . ': ' . $ns;
        }

        return implode( ' ', $attribute );
    }

}
