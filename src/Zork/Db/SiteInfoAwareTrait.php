<?php

namespace Zork\Db;

/**
 * SiteInfoAwareTrait
 *
 * implements \Zork\Db\SiteInfoAwareInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait SiteInfoAwareTrait
{

    /**
     * @var \Zork\Db\SiteInfo
     */
    protected $siteInfo;

    /**
     * @return \Zork\Db\SiteInfo
     */
    public function getSiteInfo()
    {
        return $this->siteInfo;
    }

    /**
     * @param \Zork\Db\SiteInfo $siteInfo
     * @return self
     */
    public function setSiteInfo( SiteInfo $siteInfo )
    {
        $this->siteInfo = $siteInfo;
        return $this;
    }

}
