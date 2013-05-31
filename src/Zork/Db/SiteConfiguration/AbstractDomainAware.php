<?php

namespace Zork\Db\SiteConfiguration;

use Zork\Db\SiteConfigurationInterface;
use Zork\ServiceManager\ServiceLocatorAwareTrait;

/**
 * AbstractDomainAware
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractDomainAware implements SiteConfigurationInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * @var string
     */
    protected $domain;

    /**
     * Get domain
     *
     * @return  string
     */
    public function getDomain()
    {
        // @codeCoverageIgnoreStart

        if ( empty( $this->domain ) )
        {
            switch ( true )
            {
                case isset( $_SERVER['HTTP_HOST'] ):
                    $this->domain = $_SERVER['HTTP_HOST'];
                    break;

                case isset( $_SERVER['SERVER_NAME'] ):
                    $this->domain = $_SERVER['SERVER_NAME'];
                    break;
            }
        }

        // @codeCoverageIgnoreEnd

        return $this->domain;
    }

    /**
     * Set domain
     *
     * @param   string  $domain
     * @return  AbstractDomainAware
     */
    public function setDomain( $domain )
    {
        $this->domain = (string) $domain;
        return $this;
    }

}
