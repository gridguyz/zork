<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zork\Session\Config;

use Zend\Session\Config\StandardConfig as ZendStandardConfig;

/**
 * Standard session configuration
 */
class StandardConfig extends ZendStandardConfig
{

    /**
     * {@inheritDoc}
     */
    public function setCookieDomain( $cookieDomain )
    {
        $cookieDomain = (string) $cookieDomain;

        if ( empty( $cookieDomain ) )
        {
            $this->cookieDomain = null;
            $this->setStorageOption( 'cookie_domain', null );
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
