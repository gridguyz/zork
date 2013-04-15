<?php

namespace Zork\Stdlib;

/**
 * Message
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Message
{

    /**
     * @var string
     */
    const LEVEL_ERROR   = 'ERROR';

    /**
     * @var string
     */
    const LEVEL_WARN    = 'WARN';

    /**
     * @var string
     */
    const LEVEL_INFO    = 'INFO';

    /**
     * @var string
     */
    const CONTAINER     = __CLASS__;

    /**
     * @var string
     */
    const DEFAULT_LEVEL = self::LEVEL_WARN;

    /**
     * @var string
     */
    const DEFAULT_TEXT_DOMAIN = 'default';

    /**
     * Message
     *
     * @var string
     */
    protected $message;

    /**
     * Text-domain
     *
     * @var string
     */
    protected $textDomain;

    /**
     * Message level
     *
     * @var string
     */
    protected $level;

    /**
     * Constructor
     *
     * @param string $message
     * @param string|false $textDomain
     * @param string $level
     */
    public function __construct( $message,
                                 $textDomain    = self::DEFAULT_TEXT_DOMAIN,
                                 $level         = self::DEFAULT_LEVEL )
    {
        $this->message      = (string) $message;
        $this->textDomain   = ( (string) $textDomain ) ?: false;
        $this->level        = strtoupper( (string) $level );
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Has translations (of this message)
     *
     * @return bool
     */
    public function hasTranslations()
    {
        return (bool) $this->textDomain;
    }

    /**
     * Get text-domain for translations
     *
     * @return string
     */
    public function getTextDomain()
    {
        return $this->textDomain;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

}
