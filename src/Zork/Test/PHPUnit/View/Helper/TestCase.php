<?php

namespace Zork\Test\PHPUnit\View\Helper;

use ReflectionClass;
use PHPUnit_Framework_Exception;

/**
 * TestCase
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class TestCase extends \Zork\Test\PHPUnit\TestCase
{

    /**
     * @var string
     * @abstract
     */
    protected static $helperClass = '';

    /**
     * @var array
     * @abstract
     */
    protected static $helperCtorArgs = array();

    /**
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $viewMock;

    /**
     * @var \Zend\View\Helper\HelperInterface
     */
    protected $helper;

    /**
     * Create helper
     *
     * @param   array|null  $ctorArgs
     * @param   string|null $class
     * @return  \Zend\View\Helper\HelperInterface
     */
    protected function createHelper( $ctorArgs = null, $class = null )
    {
        if ( null === $ctorArgs )
        {
            $ctorArgs = static::$helperCtorArgs;
        }

        if ( null === $class )
        {
            $class = static::$helperClass;
        }

        $rClass = new ReflectionClass( $class );
        $helper = $rClass->getConstructor()
                ? $rClass->newInstanceArgs( $ctorArgs )
                : $rClass->newInstance();
        $helper->setView( $this->viewMock );
        return $helper;
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        if ( empty( static::$helperClass ) ||
             ! class_exists( static::$helperClass ) )
        {
            throw new PHPUnit_Framework_Exception( sprintf(
                '%s: view-helper class "%s" does not exists',
                __METHOD__,
                static::$helperClass
            ) );
        }

        $this->viewMock = $this->getMock( 'Zend\View\Renderer\RendererInterface' );
        $this->helper   = $this->createHelper();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->helper   = null;
        $this->viewMock = null;
    }

    /**
     * Call helper (usable only if callable)
     *
     * @param   mixed $...
     * @return  mixed
     */
    final protected function helper()
    {
        return call_user_func_array(
            $this->helper,
            func_get_args()
        );
    }

    /**
     * Test view getter & setter
     */
    public function testViewGetterAndSetter()
    {
        $this->assertSame( $this->viewMock, $this->helper->getView() );
    }

}
