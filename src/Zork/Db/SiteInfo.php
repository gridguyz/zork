<?php

namespace Zork\Db;

/**
 * SiteInfo
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SiteInfo
{

    /**
     * Site ID
     *
     * @var int
     */
    protected $siteId;

    /**
     * Site schema
     *
     * @var string
     */
    protected $schema;

    /**
     * Owner's user ID
     *
     * @var int
     */
    protected $ownerId;

    /**
     * Site created date-time
     *
     * @var string
     */
    protected $created;

    /**
     * Actual domain
     *
     * @var string
     */
    protected $domain;

    /**
     * Actual domain ID
     *
     * @var int
     */
    protected $domainId;

    /**
     * Actual subdomain
     *
     * @var string
     */
    protected $subdomain;

    /**
     * Actual subdomain ID
     *
     * @var int
     */
    protected $subdomainId;

    /**
     * Actual fulldomain ([<subdomain>.]<domain>)
     *
     * @var string
     */
    protected $fulldomain;

    /**
     * Used scheme (http/https)
     *
     * @var string
     */
    protected $scheme = 'http';

    /**
     * Used port number
     *
     * @var int|null
     */
    protected $port;

    /**
     * Constructor for SiteInfo
     *
     * @param array|\Traversable $siteInfoParams
     */
    public function __construct( $siteInfoParams = null )
    {
        if ( ! empty( $siteInfoParams ) )
        {
            foreach ( $siteInfoParams as $key => $value )
            {
                $this->$key = $value;
            }
        }
    }

    /**
     * Site ID
     *
     * @return  int
     */
    public function getSiteId()
    {
        return (int) $this->siteId ?: null;
    }

    /**
     * Site schema
     *
     * @return  string
     */
    public function getSchema()
    {
        return (string) $this->schema;
    }

    /**
     * Owner's user ID
     *
     * @return  int
     */
    public function getOwnerId()
    {
        return (int) $this->ownerId ?: null;
    }

    /**
     * Site created date-time
     *
     * @return  string
     */
    public function getCreated()
    {
        return (string) $this->created ?: null;
    }

    /**
     * Actual domain
     *
     * @return  string
     */
    public function getDomain()
    {
        return (string) $this->domain;
    }

    /**
     * Actual domain's IDN
     *
     * @return  string
     */
    public function getIdn()
    {
        $domain = $this->getDomain();
        return $domain ? @ idn_to_utf8( $domain ) : '';
    }

    /**
     * Actual domain ID
     *
     * @return  int
     */
    public function getDomainId()
    {
        return (int) $this->domainId ?: null;
    }

    /**
     * Actual subdomain
     *
     * @return  string
     */
    public function getSubdomain()
    {
        return (string) $this->subdomain;
    }

    /**
     * Actual subdomain ID
     *
     * @return  int
     */
    public function getSubdomainId()
    {
        return (int) $this->subdomainId ?: null;
    }

    /**
     * Actual fulldomain ([<subdomain>.]<domain>)
     *
     * @return  string
     */
    public function getFulldomain()
    {
        return (string) $this->fulldomain;
    }

    /**
     * Actual fulldomain's IDN
     *
     * @return  string
     */
    public function getFullIdn()
    {
        $fulldomain = $this->getFulldomain();
        return $fulldomain ? @ idn_to_utf8( $fulldomain ) : '';
    }

    /**
     * Used scheme (http/https)
     *
     * @return  string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Used port number
     *
     * @return  int|null
     */
    public function getPort()
    {
        return ( (int) $this->port ) ?: null;
    }

    /**
     * Get subdomain url
     *
     * @param   null|string $subdomain
     * @param   bool|string $link
     * @return  string
     */
    public function getSubdomainUrl( $subdomain = null, $link = true )
    {
        static $defaultPorts = array(
            'http'  => 80,
            'https' => 443,
        );

        if ( null === $subdomain )
        {
            $domain = $this->getFulldomain();
        }
        else if ( empty( $subdomain ) )
        {
            $domain = $this->getDomain();
        }
        else
        {
            $domain = $subdomain . '.' . $this->getDomain();
        }

        if ( false === $link )
        {
            return $domain;
        }

        $path   = '/' . ( $link === true ? '' : ltrim( $link, '/' ) );
        $scheme = $this->getScheme();
        $port   = $this->getPort();
        $link   = '//' . $domain;

        if ( $scheme )
        {
            $link = $scheme . ':' . $link;
        }

        if ( $port && ( empty( $defaultPorts[$scheme] )
                || $port != $defaultPorts[$scheme] ) )
        {
            $link .= ':' . $port;
        }

        return $link . $path;
    }

}
