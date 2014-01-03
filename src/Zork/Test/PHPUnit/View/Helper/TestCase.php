<?php

namespace Zork\Test\PHPUnit\View\Helper;

use ReflectionClass;
use Zork\Stdlib\String;
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
     */
    protected static $rendererClass = 'Zend\View\Renderer\RendererInterface';

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
     * @var array
     */
    protected static $pluginAliases = array();

    /**
     * @var array
     */
    protected $pluginInstances = array();

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
     * @param   string|null $className
     * @return  \Zend\View\Helper\HelperInterface
     */
    protected function createHelper( array $ctorArgs = null, $className = null )
    {
        if ( null === $ctorArgs )
        {
            $ctorArgs = static::$helperCtorArgs;
        }

        if ( null === $className )
        {
            $className = static::$helperClass;
        }

        $class  = new ReflectionClass( $className );
        $helper = $class->getConstructor()
                ? $class->newInstanceArgs( $ctorArgs )
                : $class->newInstance();
        return $helper->setView( $this->viewMock );
    }

    /**
     * Create plugin
     *
     * @param   string  $name
     * @param   array   $options
     * @return  \Zend\View\Helper\HelperInterface
     */
    public function plugin( $name, array $options = null )
    {
        $lname = strtolower( $name );

        if ( ! empty( static::$pluginAliases[$lname] ) )
        {
            $name = static::$pluginAliases[$lname];
        }

        if ( isset( $this->pluginInstances[$lname] ) )
        {
            return $this->pluginInstances[$lname];
        }

        if ( ! class_exists( $name ) )
        {
            $name = 'Zend\\View\\Helper\\' . String::camelize( $name, '_', false );
        }

        if ( ! class_exists( $name ) )
        {
            return null;
        }

        return $this->createHelper( $options, $name );
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

        $this->viewMock = $this->getMock( static::$rendererClass );

        if ( method_exists( $this->viewMock, 'plugin' ) )
        {
            $this->viewMock
                 ->expects( $this->any() )
                 ->method( 'plugin' )
                 ->will( $this->returnCallback( array( $this, 'plugin' ) ) );
        }

        $this->helper           = $this->createHelper();
        $this->pluginInstances  = array();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->helper           = null;
        $this->viewMock         = null;
        $this->pluginInstances  = array();
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
