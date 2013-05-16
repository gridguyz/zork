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
            $this->domain = isset( $_SERVER['HTTP_HOST'] )
                ? $_SERVER['HTTP_HOST']
                : $_SERVER['SERVER_NAME'];
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
