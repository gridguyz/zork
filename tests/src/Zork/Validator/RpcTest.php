<?php

namespace Zork\Validator;

use ArrayIterator;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * RpcTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Validator\Rpc
 */
class RpcTest extends TestCase
{

    /**
     * @var \Zork\Rpc\CallableInterface
     */
    protected $rpc;

    /**
     *
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->rpc = $this->getMock( 'Zork\Rpc\CallableInterface' );
        $this->serviceManager = new ServiceManager;
        $this->serviceManager
             ->setService( 'SampleRpc', $this->rpc );
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->serviceManager   = null;
        $this->rpc              = null;
    }

    // Test returns scalars & null

    /**
     * Test call an rpc & return true to the validator
     */
    public function testCallReturnTrue()
    {
        $validator = new Rpc( 'SampleRpc' );
        $validator->setMethod( __FUNCTION__ );
        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', array( 'context' ) ) ) )
             ->will( $this->returnValue( true ) );

        $this->assertTrue( $validator->isValid( 'value', array( 'context' ) ) );
    }

    /**
     * Test call an rpc & return false to the validator
     */
    public function testCallReturnFalse()
    {
        $validator = new Rpc( new ArrayIterator( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( false ) );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::RETURN_FALSE, $validator->getMessages() );
    }

    /**
     * Test call an rpc & return null to the validator
     */
    public function testCallReturnNull()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( null ) );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::RETURN_NULL, $validator->getMessages() );
    }

    /**
     * Test call an rpc & return empty value (0) to the validator
     */
    public function testCallReturnEmpty()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( 0 ) );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::RETURN_EMPTY, $validator->getMessages() );
    }

    // Test returns array

    /**
     * Test call an rpc & return empty array to the validator
     */
    public function testCallReturnEmptyArray()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( array() ) );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::RETURN_EMPTY, $validator->getMessages() );
    }

    /**
     * Test call an rpc & return array contains true to the validator
     */
    public function testCallReturnArrayContainsTrue()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( array( true ) ) );

        $this->assertTrue( $validator->isValid( 'value' ) );
    }

    /**
     * Test call an rpc & return array contains false to the validator
     */
    public function testCallReturnArrayContainsFalseAndMessage()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( array( false, __METHOD__ ) ) );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::RETURN_FALSE, $validator->getMessages() );
        $this->assertContains( __METHOD__, $validator->getMessages() );
    }

    // Test returns object

    /**
     * Test call an rpc & return empty object to the validator
     */
    public function testCallReturnEmptyObject()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( (object) array() ) );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::RETURN_NULL, $validator->getMessages() );
    }

    /**
     * Test call an rpc & return object contains true to the validator
     */
    public function testCallReturnObjectContainsTrue()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( (object) array(
                 'success' => true
             ) ) );

        $this->assertTrue( $validator->isValid( 'value' ) );
    }

    /**
     * Test call an rpc & return object contains false to the validator
     */
    public function testCallReturnObjectContainsFalseAndMessage()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( (object) array(
                 'success' => false,
                 'message' => __METHOD__
             ) ) );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::RETURN_FALSE, $validator->getMessages() );
        $this->assertContains( __METHOD__, $validator->getMessages() );
    }

    // Test returns string

    /**
     * Test call an rpc & return empty string to the validator
     */
    public function testCallReturnEmptyString()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( '' ) );

        $this->assertTrue( $validator->isValid( 'value' ) );
    }

    /**
     * Test call an rpc & return error string to the validator
     */
    public function testCallReturnErrorString()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->returnValue( __METHOD__ ) );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( __METHOD__, $validator->getMessages() );
    }

    /**
     * Test missing locator
     */
    public function testMissingLocator()
    {
        $validator = new Rpc;

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::MISSING_LOCATOR, $validator->getMessages() );
    }

    /**
     * Test missing service
     */
    public function testMissingService()
    {
        $validator = new Rpc;
        $validator->setServiceLocator( $this->serviceManager );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::MISSING_SERVICE, $validator->getMessages() );
    }

    /**
     * Test missing method
     */
    public function testMissingMethod()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => '',
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::MISSING_METHOD, $validator->getMessages() );
    }

    /**
     * Test not-valid service
     */
    public function testNotValidService()
    {
        $validator = new Rpc( 'NotValidRpc' );
        $validator->setServiceLocator( $this->serviceManager );

        $this->serviceManager
             ->setService( 'NotValidRpc', (object) array() );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::NOT_VALID_SERVICE, $validator->getMessages() );
    }

    /**
     * Test not-valid method
     */
    public function testNotValidMethod()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->throwException(
                 new \Zork\Rpc\Exception\BadMethodCallException(
                     __METHOD__
                 )
             ) );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::NOT_VALID_METHOD, $validator->getMessages() );
        $this->assertContains( __METHOD__, $validator->getMessages() );
    }

    /**
     * Test exception
     */
    public function testException()
    {
        $validator = new Rpc( array(
            'service'   => 'SampleRpc',
            'method'    => __FUNCTION__,
        ) );

        $validator->setServiceLocator( $this->serviceManager );

        $this->rpc
             ->expects( $this->once() )
             ->method( 'call' )
             ->with( $this->equalTo( __FUNCTION__ ),
                     $this->equalTo( array( 'value', null ) ) )
             ->will( $this->throwException( new \Exception( __METHOD__ ) ) );

        $this->assertFalse( $validator->isValid( 'value' ) );
        $this->assertContains( 'validate.rpc.' . Rpc::EXCEPTION, $validator->getMessages() );
        $this->assertContains( __METHOD__, $validator->getMessages() );
    }

}
