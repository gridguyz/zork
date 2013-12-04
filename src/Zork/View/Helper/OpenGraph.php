<?php

namespace Zork\View\Helper;

use Countable;
use Traversable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Zend\View\Helper\HeadMeta;
use Zend\View\Helper\HeadLink;
use Zend\View\Helper\AbstractHelper;

/**
 * HtmlTag
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class OpenGraph extends AbstractHelper
             implements Countable,
                        ArrayAccess,
                        IteratorAggregate
{

    /**
     * @const string
     */
    const PREFIX_OG = 'http://ogp.me/ns#';

    /**
     * @const string
     */
    const PREFIX_FB = 'http://ogp.me/ns/fb#';

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
     * Safe locale aliases
     *
     * @var array
     */
    protected static $safeLocaleAliases = array(
        'af' => 'af_ZA', 'ar' => 'ar_AR', 'az' => 'az_AZ', 'be' => 'be_BY',
        'bg' => 'bg_BG', 'bn' => 'bn_IN', 'bs' => 'bs_BA', 'ca' => 'ca_ES',
        'cs' => 'cs_CZ', 'cy' => 'cy_GB', 'da' => 'da_DK', 'de' => 'de_DE',
        'el' => 'el_GR', 'en' => 'en_US', 'eo' => 'eo_EO', 'es' => 'es_ES',
        'et' => 'et_EE', 'eu' => 'eu_ES', 'fa' => 'fa_IR', 'fi' => 'fi_FI',
        'fo' => 'fo_FO', 'fr' => 'fr_FR', 'fy' => 'fy_NL', 'ga' => 'ga_IE',
        'gl' => 'gl_ES', 'he' => 'he_IL', 'hi' => 'hi_IN', 'hr' => 'hr_HR',
        'hu' => 'hu_HU', 'hy' => 'hy_AM', 'id' => 'id_ID', 'is' => 'is_IS',
        'it' => 'it_IT', 'ja' => 'ja_JP', 'ka' => 'ka_GE', 'km' => 'km_KH',
        'ko' => 'ko_KR', 'ku' => 'ku_TR', 'la' => 'la_VA', 'lt' => 'lt_LT',
        'lv' => 'lv_LV', 'mk' => 'mk_MK', 'ml' => 'ml_IN', 'ms' => 'ms_MY',
        'nb' => 'nb_NO', 'ne' => 'ne_NP', 'nl' => 'nl_NL', 'nn' => 'nn_NO',
        'no' => 'nn_NO', 'pa' => 'pa_IN', 'pl' => 'pl_PL', 'ps' => 'ps_AF',
        'pt' => 'pt_PT', 'ro' => 'ro_RO', 'ru' => 'ru_RU', 'sk' => 'sk_SK',
        'sl' => 'sl_SI', 'sq' => 'sq_AL', 'sr' => 'sr_RS', 'sv' => 'sv_SE',
        'sw' => 'sw_KE', 'ta' => 'ta_IN', 'te' => 'te_IN', 'th' => 'th_TH',
        'tl' => 'tl_PH', 'tr' => 'tr_TR', 'uk' => 'uk_UA', 'vi' => 'vi_VN',
        'zh' => 'zh_CN',
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
     * @codeCoverageIgnore
     * @deprecated
     * @ignore
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
     * @codeCoverageIgnore
     * @deprecated
     * @ignore
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
    public function __invoke( $set = null )
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
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return  Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator( $this->properties );
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return  int
     */
    public function count()
    {
        return count( $this->properties );
    }

    /**
     * Get type
     *
     * @return \Zork\View\Helper\OpenGraph
     */
    public function getType()
    {
        if ( ! isset( $this['type'] ) )
        {
            $this['type'] = array(
                'property'  => 'og:type',
                'content'   => self::TYPE_WEBSITE,
            );
        }

        return $this['type']['content'];
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
     * Get prefix namespace
     *
     * @param   string  $prefix
     * @return  string|null
     */
    public function getPrefixNs( $prefix )
    {
        if ( empty( $this->prefixes[$prefix] ) )
        {
            return null;
        }

        return $this->prefixes[$prefix];
    }

    /**
     * Get prefixes by namespace
     *
     * @param   string  $prefixNs
     * @param   bool    $onlyFirst
     * @return  array|string|null
     */
    public function getPrefixByNs( $prefixNs, $onlyFirst = false )
    {
        $prefixes = array();

        foreach ( $this->prefixes as $prefix => $ns )
        {
            if ( $ns == $prefixNs )
            {
                if ( $onlyFirst )
                {
                    return $prefix;
                }

                $prefixes[] = $prefix;
            }
        }

        if ( $onlyFirst )
        {
            return null;
        }

        return $prefixes;
    }

    /**
     * Add a prefix
     *
     * @param   string  $prefix
     * @param   string  $ns
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function addPrefix( $prefix, $ns )
    {
        $this->prefixes[$prefix] = (string) $ns;
        return $this;
    }

    /**
     * Add prefixes
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
                $this->addPrefix( $prefix, $ns );
            }
        }

        return $this;
    }

    /**
     * Remove prefix
     *
     * @param   string  $prefix
     * @return  \Zork\View\Helper\OpenGraph
     */
    public function removePrefix( $prefix )
    {
        if ( isset( $this->prefixes[$prefix] ) )
        {
            unset( $this->prefixes[$prefix] );
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
     * Has a property defined?
     *
     * @param   string  $property
     * @return  bool
     */
    public function hasProperty( $property )
    {
        foreach ( $this->properties as $meta )
        {
            if ( $meta['property'] == $property )
            {
                return true;
            }
        }

        return false;
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

    /**
     * Get safe locale
     *
     * @staticvar   array   $localeAliases
     * @param       string  $locale
     * @return      string
     */
    public function getSafeLocale( $locale )
    {
        $locale = (string) $locale;

        if ( ! empty( static::$safeLocaleAliases[$locale] ) )
        {
            $locale = static::$safeLocaleAliases[$locale];
        }

        return $locale;
    }

}
