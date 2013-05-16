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
     * @var int
     */
    public function getSiteId()
    {
        return (int) $this->siteId ?: null;
    }

    /**
     * Site schema
     *
     * @var string
     */
    public function getSchema()
    {
        return (string) $this->schema;
    }

    /**
     * Owner's user ID
     *
     * @var int
     */
    public function getOwnerId()
    {
        return (int) $this->ownerId ?: null;
    }

    /**
     * Site created date-time
     *
     * @var string
     */
    public function getCreated()
    {
        return (string) $this->created ?: null;
    }

    /**
     * Actual domain
     *
     * @var string
     */
    public function getDomain()
    {
        return (string) $this->domain;
    }

    /**
     * Actual domain's IDN
     *
     * @return string
     */
    public function getIdn()
    {
        $domain = $this->getDomain();
        return $domain ? @ idn_to_utf8( $domain ) : '';
    }

    /**
     * Actual domain ID
     *
     * @var int
     */
    public function getDomainId()
    {
        return (int) $this->domainId ?: null;
    }

    /**
     * Actual subdomain
     *
     * @var string
     */
    public function getSubdomain()
    {
        return (string) $this->subdomain;
    }

    /**
     * Actual subdomain ID
     *
     * @var int
     */
    public function getSubdomainId()
    {
        return (int) $this->subdomainId ?: null;
    }

    /**
     * Actual fulldomain ([<subdomain>.]<domain>)
     *
     * @var string
     */
    public function getFulldomain()
    {
        return (string) $this->fulldomain;
    }

    /**
     * Actual fulldomain's IDN
     *
     * @return string
     */
    public function getFullIdn()
    {
        $fulldomain = $this->getFulldomain();
        return $fulldomain ? @ idn_to_utf8( $fulldomain ) : '';
    }

}
