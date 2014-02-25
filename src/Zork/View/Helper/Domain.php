<?php

namespace Zork\View\Helper;

use Zork\Db\SiteInfo;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Domain
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Domain extends AbstractHelper
          implements SiteInfoAwareInterface
{

    use SiteInfoAwareTrait;

    /**
     * @param   SiteInfo    $siteInfo
     */
    public function __construct( SiteInfo $siteInfo )
    {
        $this->setSiteInfo( $siteInfo );
    }

    /**
     * Invokable helper
     *
     * @param   null|string $subdomain
     * @param   bool|string $link
     * @return  Domain
     */
    public function __invoke( $subdomain = null, $link = false )
    {
        if ( null !== $subdomain || false !== $link )
        {
            return $this->getSubdomain( $subdomain, $link );
        }

        return $this;
    }

    /**
     * Get subdomain (optionally a whole link) for actual domain
     *
     * @param   null|string $subdomain
     * @param   bool|string $link
     * @return  string
     */
    public function getSubdomain( $subdomain = null, $link = false )
    {
        return $this->getSiteInfo()
                    ->getSubdomainUrl( $subdomain, $link );
    }

    /**
     * Convert to string
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getSiteInfo()
                    ->getFulldomain();
    }

}
