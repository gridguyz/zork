<?php

namespace Zork\Mvc;

use Locale as IntlLocale;
use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container as SessionContainer;
use Zend\Session\ManagerInterface as SessionManager;

/**
 * Locale
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdminLocale extends AbstractHelper
{

    /**
     * @var string
     */
    const SESSION_CONTAINER = 'admin';

    /**
     * @var string
     */
    const SESSION_KEY       = 'adminLocale';

    /**
     * Current admin locale
     *
     * @var string
     */
    protected $current = 'en';

    /**
     * Session-container
     *
     * @var \Zend\Session\Container
     */
    protected $sessionContainer;

    /**
     * Constructor
     *
     * @param string $current
     */
    public function __construct( $current, SessionManager $sessionManager )
    {
        $this->sessionContainer = new SessionContainer(
            static::SESSION_CONTAINER,
            $sessionManager
        );

        $this->setCurrent( $current );
    }

    /**
     * Normalize locale
     *
     * @param string $locale
     * @return string
     */
    public static function normalizeLocale( $locale )
    {
        $parsed = IntlLocale::parseLocale( $locale );

        if ( empty( $parsed ) ||
             empty( $parsed['language'] ) )
        {
            return null;
        }

        $result = $parsed['language'];

        if ( ! empty( $parsed['region'] ) )
        {
            $result .= '_' . strtoupper( $parsed['region'] );
        }

        return $result;
    }

    /**
     * Get session-container
     *
     * @return \Zend\Session\Container
     */
    public function getSessionContainer()
    {
        return $this->sessionContainer;
    }

    /**
     * Get current admin-locale
     *
     * @return string
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Set current admin-locale
     *
     * @param string $locale
     * @return \Core\Service\Locale
     */
    public function setCurrent( $locale )
    {
        $session       = $this->getSessionContainer();
        $this->current = $locale ? static::normalizeLocale( $locale ) : null;

        if ( null === $this->current )
        {
            if ( empty( $session[static::SESSION_KEY] ) )
            {
                $this->current = IntlLocale::getDefault();
            }
            else
            {
                $this->current = $session[static::SESSION_KEY];
            }
        }
        else
        {
            $session[static::SESSION_KEY] = $this->current;
        }

        return $this;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getCurrent();
    }

    /**
     * For view-helper-like use
     *
     * @return \Zork\Mvc\AdminLocale
     */
    public function __invoke()
    {
        return $this;
    }

}
