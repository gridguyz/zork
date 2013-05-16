<?php

namespace Zork\Db\SiteConfiguration;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * RedirectionServiceTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RedirectionServiceTest extends TestCase
{

    /**
     * Test default functionality
     */
    public function testDefaults()
    {
        $redirect = new RedirectionService(
            'redirect-to.example.com',
            'reason',
            true
        );

        $this->assertEquals( 'redirect-to.example.com', $redirect->getDomain() );
        $this->assertEquals( 'reason', $redirect->getReason() );
        $this->assertTrue( $redirect->getUsePath() );
    }

}
