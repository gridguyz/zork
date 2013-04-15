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
     * @param \Zork\Db\SiteInfo $siteInfo
     */
    public function __construct( SiteInfo $siteInfo )
    {
        $this->setSiteInfo( $siteInfo );
    }

    /**
     * Invokable helper
     *
     * @return \Zork\View\Helper\Locale
     */
    public function __invoke( $subdomain = null )
    {
        if ( null !== $subdomain )
        {
            return $this->getSubdomain( $subdomain );
        }

        return $this;
    }

    /**
     * Get subdomain for actual domain
     *
     * @param   string  $subdomain
     * @return  string
     */
    public function getSubdomain( $subdomain = null )
    {
        if ( null === $subdomain )
        {
            return $this->getSiteInfo()
                        ->getFulldomain();
        }

        if ( empty( $subdomain ) )
        {
            return $this->getSiteInfo()
                        ->getDomain();
        }

        return $subdomain . '.'
             . $this->getSiteInfo()
                    ->getDomain();
    }

    /**
     * Get current locale
     *
     * @return String
     */
    public function __toString()
    {
        return $this->getSiteInfo()
                    ->getFulldomain();
    }

}
