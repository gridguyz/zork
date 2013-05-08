<?php

namespace Zork\View\Helper;

use Zork\Test\PHPUnit\View\Helper\TestCase;

/**
 * ConfigTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\View\Helper\Config
 */
class ConfigTest extends TestCase
{

    /**
     * @var string
     */
    protected static $helperClass = 'Zork\View\Helper\Config';

    /**
     * @var array
     */
    protected static $helperCtorArgs = array(
        array(
            'modules' => array(
                'TestGlobal'        => 'test-global',
                'Test\Namespaced'   => 'test-namespaced'
            ),
        ),
    );

    /**
     * Test invoke without arguments
     */
    public function testInvokeWithoutArguments()
    {
        $this->assertInstanceOf( static::$helperClass, $this->helper() );
    }

    /**
     * Test modules
     */
    public function testModules()
    {
        $this->assertSame( 'test-global', $this->helper( 'testGlobal' ) );
        $this->assertSame( 'test-global', $this->helper( 'TestGlobal' ) );
        $this->assertSame( 'test-namespaced', $this->helper( 'Test\Namespaced' ) );
        $this->assertNull( $this->helper( 'NonExisting' ) );
        $this->assertNull( $this->createHelper( array( array() ) )->__invoke( 'Test' ) );
        $this->assertNull( $this->createHelper( array( array( 'modules' => 0 ) ) )->__invoke( 'Test' ) );
    }

}
