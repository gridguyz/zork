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
     * @param   string      $scheme
     * @param   string      $domain
     * @param   int|null    $port
     * @param   string      $reason
     * @param   bool        $usePath
     */
    public function __construct( $scheme,
                                 $domain,
                                 $port,
                                 $reason    = '',
                                 $usePath   = false )
    {
        $this->scheme   = (string) $scheme;
        $this->domain   = (string) $domain;
        $this->port     = ( (int)  $port ) ?: null;
        $this->reason   = (string) $reason;
        $this->usePath  = (bool)   $usePath;
    }

    /**
     * @return  string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return  string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return  int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param   string|null $path
     * @return  string
     */
    public function getUrl( $path = null )
    {
        static $defaultPorts = array(
            'http'  => 80,
            'https' => 443,
        );

        $url    = '';
        $scheme = $this->getScheme();
        $path   = '/' . ltrim( $path, '/' );

        if ( $scheme )
        {
            $url .= $scheme . ':';
        }

        $url .= '//' . $this->getDomain();
        $port = $this->getPort();

        if ( $port && ( empty( $defaultPorts[$scheme] )
                || $port != $defaultPorts[$scheme] ) )
        {
            $url .= ':' . $port;
        }

        return $url . $path;
    }

    /**
     * @return  string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return  bool
     */
    public function getUsePath()
    {
        return $this->usePath;
    }

}
