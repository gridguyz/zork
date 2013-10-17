<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zork\Session\Config;

use Zend\Session\Config\SessionConfig as ZendSessionConfig;

/**
 * Session configuration
 */
class SessionConfig extends ZendSessionConfig
{

    /**
     * {@inheritDoc}
     */
    public function setCookieDomain( $cookieDomain )
    {
        $cookieDomain = (string) $cookieDomain;

        if ( empty( $cookieDomain ) )
        {
            $this->cookieDomain = '';
            $this->setStorageOption( 'cookie_domain', '' );
        }
        else
        {
            $subdomains = false;

            if ( '.' === $cookieDomain[0] )
            {
                $subdomains     = true;
                $cookieDomain   = ltrim( $cookieDomain, '.' );
            }

            parent::setCookieDomain( $cookieDomain );

            if ( $subdomains )
            {
                $this->setStorageOption(
                    'cookie_domain',
                    $this->cookieDomain = '.' . $this->cookieDomain
                );
            }
        }

        return $this;
    }

}
