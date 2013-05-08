<?php

namespace Zork\View\Helper;

use Zork\Db\SiteInfo;
use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * DomainTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DomainTest extends TestCase
{

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\View\Helper\Domain';

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$helperCtorArgs = array(
            new SiteInfo( array(
                'subdomain'     => 'sub',
                'domain'        => 'zork.test',
                'fulldomain'    => 'sub.zork.test',
            ) )
        );
    }

    /**
     * Test invoke without arguments
     */
    public function testInvokeWithoutArguments()
    {
        $this->assertInstanceOf( static::$helperClass, $this->helper() );
    }

    /**
     * Test domains
     */
    public function testDomains()
    {
        $this->assertSame( 'sub.zork.test', (string) $this->helper );
        $this->assertSame( 'zork.test', $this->helper( '' ) );
        $this->assertSame( 'abc.zork.test', $this->helper( 'abc' ) );
        $this->assertSame( 'sub.zork.test', $this->helper->getSubdomain( null ) );
    }

}
