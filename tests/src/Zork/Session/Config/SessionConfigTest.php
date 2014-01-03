<?php

namespace Zork\Session\Config;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * SessionConfigTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class SessionConfigTest extends TestCase
{

    /**
     * Test setCookieDomain()
     */
    public function testSetCookieDomain()
    {
        $config = new SessionConfig;

        $config->setCookieDomain( '.example.com' );
        $this->assertEquals( '.example.com', $config->getCookieDomain() );

        $config->setCookieDomain( '' );
        $this->assertEmpty( $config->getCookieDomain() );
    }

}
