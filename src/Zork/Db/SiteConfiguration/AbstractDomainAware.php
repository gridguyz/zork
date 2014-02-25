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
    protected $scheme;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var int|null
     */
    protected $port;

    /**
     * @var bool
     */
    private $detected = false;

    /**
     * Detect scheme, domain & port
     *
     * @codeCoverageIgnore
     * @return  void
     */
    private function detect()
    {
        if ( $this->detected )
        {
            return;
        }

        if ( empty( $this->scheme ) )
        {
            if ( empty( $_SERVER['HTTPS'] ) || 'off' === $_SERVER['HTTPS'] )
            {
                $this->scheme = 'http';
            }
            else
            {
                $this->scheme = 'https';
            }
        }

        if ( empty( $this->domain ) )
        {
            if ( isset( $_SERVER['GRIDGUYZ_HOST'] ) )
            {
                $this->domain = $_SERVER['GRIDGUYZ_HOST'];
            }
            else if ( isset( $_SERVER['HTTP_HOST'] ) )
            {
                $matches = array();

                if ( preg_match( '/^([^:]+):(\d+)$/',
                                 $_SERVER['HTTP_HOST'],
                                 $matches ) )
                {
                    $this->domain = $matches[1];
                    $this->port   = (int) $matches[2];
                }
                else
                {
                    $this->domain = $_SERVER['HTTP_HOST'];
                }
            }
            else if ( isset( $_SERVER['SERVER_NAME'] ) )
            {
                $this->domain = $_SERVER['SERVER_NAME'];
            }
            else if ( isset( $_SERVER['SERVER_ADDR'] ) )
            {
                $this->domain = $_SERVER['SERVER_ADDR'];
            }
        }

        if ( empty( $this->port ) )
        {
            if ( isset( $_SERVER['SERVER_PORT'] ) )
            {
                $this->port = (int) $_SERVER['SERVER_PORT'];
            }
        }

        $this->detected = true;
    }

    /**
     * Get scheme
     *
     * @return  string
     */
    public function getScheme()
    {
        $this->detect();
        return $this->scheme;
    }

    /**
     * Set scheme
     *
     * @param   string  $scheme
     * @return  AbstractDomainAware
     */
    public function setScheme( $scheme )
    {
        $this->scheme = (string) $scheme;
        return $this;
    }

    /**
     * Get domain
     *
     * @return  string
     */
    public function getDomain()
    {
        $this->detect();
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

    /**
     * Get port
     *
     * @return  int|null
     */
    public function getPort()
    {
        $this->detect();
        return $this->port;
    }

    /**
     * Set port
     *
     * @param   int|null    $port
     * @return  AbstractDomainAware
     */
    public function setPort( $port )
    {
        $this->port = ( (int) $port ) ?: null;
        return $this;
    }

}
