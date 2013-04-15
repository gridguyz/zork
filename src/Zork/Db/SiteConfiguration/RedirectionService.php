<?php

namespace Zork\Db\SiteConfiguration;

/**
 * RedirectionService
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RedirectionService
{

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $reason;

    /**
     * @var bool
     */
    protected $usePath;

    /**
     * Constructor
     *
     * @param string $domain
     * @param string $reason
     * @param bool   $usePath
     */
    public function __construct( $domain, $reason = '', $usePath = false )
    {
        $this->domain   = (string)  $domain;
        $this->reason   = (string)  $reason;
        $this->usePath  = (bool)    $usePath;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return bool
     */
    public function getUsePath()
    {
        return $this->usePath;
    }

}
