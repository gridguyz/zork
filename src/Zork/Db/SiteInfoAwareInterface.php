<?php

namespace Zork\Db;

/**
 * \Zork\Db\SiteInfoAwareInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface SiteInfoAwareInterface
{

    /**
     * @return \Zork\Db\SiteInfo
     */
    public function getSiteInfo();

    /**
     * @param \Zork\Db\SiteInfo $siteInfo
     * @return \Zork\Db\SiteInfoAwareInterface
     */
    public function setSiteInfo( SiteInfo $siteInfo );

}
