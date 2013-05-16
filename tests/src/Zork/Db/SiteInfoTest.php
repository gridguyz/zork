<?php

namespace Zork\Db;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * SiteInfoTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SiteInfoTest extends TestCase
{

    /**
     * Test create empty site-info
     */
    public function testCreateEmpty()
    {
        $info = new SiteInfo;

        $this->assertSame( null, $info->getSiteId() );
        $this->assertSame( null, $info->getOwnerId() );
        $this->assertSame( null, $info->getDomainId() );
        $this->assertSame( null, $info->getSubdomainId() );
        $this->assertSame( null, $info->getCreated() );
        $this->assertSame( '', $info->getSchema() );
        $this->assertSame( '', $info->getDomain() );
        $this->assertSame( '', $info->getSubdomain() );
        $this->assertSame( '', $info->getFulldomain() );
        $this->assertSame( '', $info->getIdn() );
        $this->assertSame( '', $info->getFullIdn() );
    }

    /**
     * Test create with params
     */
    public function testCreateWithParams()
    {
        $created = date( DATE_ISO8601 );

        $info = new SiteInfo( array(
            'siteId'        => '1',
            'ownerId'       => '2',
            'domainId'      => '3',
            'subdomainId'   => '4',
            'created'       => $created,
            'schema'        => 'schema_value',
            'domain'        => 'xn--parlez-vous-franais-lyb.com',
            'subdomain'     => 'fr',
            'fulldomain'    => 'fr.xn--parlez-vous-franais-lyb.com',
        ) );

        $this->assertSame( 1, $info->getSiteId() );
        $this->assertSame( 2, $info->getOwnerId() );
        $this->assertSame( 3, $info->getDomainId() );
        $this->assertSame( 4, $info->getSubdomainId() );
        $this->assertSame( $created, $info->getCreated() );
        $this->assertSame( 'schema_value', $info->getSchema() );
        $this->assertSame( 'xn--parlez-vous-franais-lyb.com', $info->getDomain() );
        $this->assertSame( 'fr', $info->getSubdomain() );
        $this->assertSame( 'fr.xn--parlez-vous-franais-lyb.com', $info->getFulldomain() );
        $this->assertSame( 'parlez-vous-français.com', $info->getIdn() );
        $this->assertSame( 'fr.parlez-vous-français.com', $info->getFullIdn() );
    }

}
