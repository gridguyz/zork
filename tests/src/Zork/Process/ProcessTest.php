<?php

namespace Zork\Process;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * AlternateTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Process\Process
 */
class ProcessTest extends TestCase
{

    /**
     * @var
     */
    protected $callbacks;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        include_once __DIR__ . '/_files/ProcessClasses.php';
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->callbacks = $this->getMock( 'Zork\Process\Process\Callbacks' );
    }

    /**
     * @param   string  $method
     * @return  callable
     */
    protected function getCallback( $method )
    {
        return array( $this->callbacks, (string) $method );
    }

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $process = new Process( array(
            'command'                   => 'test_command',
            'openCallback'              => $open = $this->getCallback( 'open' ),
            'closeCallback'             => $close = $this->getCallback( 'close' ),
            'validationCallback'        => $val = $this->getCallback( 'validate' ),
            'arguments'                 => $args = array( 'arg_1', 'arg_2' ),
            'workingDirectory'          => __DIR__,
            'environmentVariables'      => $envs = array( 'env_1' => 'val 1',
                                                          'env_2' => 'val 2' ),
            'mergeEnvironmentVariables' => true,
            'options'                   => $opts = array( 'opt_1' => 'val 1',
                                                          'opt_2' => 'val 2' ),
        ) );

        $this->assertEquals( 'test_command', $process->getCommand() );
        $this->assertEquals( $open, $process->getOpenCallback() );
        $this->assertEquals( $close, $process->getCloseCallback() );
        $this->assertEquals( $val, $process->getValidationCallback() );
        $this->assertEquals( $args, $process->getArguments() );
        $this->assertEquals( __DIR__, $process->getWorkingDirectory() );
        $this->assertEquals( $envs, $process->getEnvironmentVariables() );
        $this->assertTrue( $process->getMergeEnvironmentVariables() );
        $this->assertEquals( $opts, $process->getOptions() );
    }

    /**
     * Test getters and setters
     */
    public function testGettersAndSetters()
    {
        $process = new Process;

        $process->setCommand( 'test_command' );
        $this->assertEquals( 'test_command', $process->getCommand() );

        $process->setOpenCallback( $open = $this->getCallback( 'open' ) );
        $this->assertEquals( $open, $process->getOpenCallback() );

        $process->setCloseCallback( $close = $this->getCallback( 'close' ) );
        $this->assertEquals( $close, $process->getCloseCallback() );

        $process->setValidationCallback( $val = $this->getCallback( 'validate' ) );
        $this->assertEquals( $val, $process->getValidationCallback() );

        $process->setArguments( $args = array( 'arg_1', 'arg_2' ) );
        $this->assertEquals( $args, $process->getArguments() );

        $process->setArguments( new ArrayIterator( $args ) );
        $this->assertEquals( $args, $process->getArguments() );

        $process->setArguments( null );
        $this->assertEquals( array(), $process->getArguments() );

        $process->setArguments( array( 'arg_1', '', 'arg_2' ) );
        $this->assertEquals( $args, array_values( $process->getArguments() ) );

        $process->setWorkingDirectory( __DIR__ );
        $this->assertEquals( __DIR__, $process->getWorkingDirectory() );

        $process->setWorkingDirectory( '' );
        $this->assertNull( $process->getWorkingDirectory() );

        $process->setEnvironmentVariables( $envs = array( 'env_1' => 'val 1',
                                                          'env_2' => 'val 2' ) );
        $this->assertEquals( $envs, $process->getEnvironmentVariables() );

        $process->setEnvironmentVariables( new ArrayIterator( $envs ) );
        $this->assertEquals( $envs, $process->getEnvironmentVariables() );

        $process->setEnvironmentVariables( array() );
        $this->assertEquals( null, $process->getEnvironmentVariables() );

        $process->setMergeEnvironmentVariables();
        $this->assertTrue( $process->getMergeEnvironmentVariables() );

        $process->setMergeEnvironmentVariables( false );
        $this->assertFalse( $process->getMergeEnvironmentVariables() );

        $process->setMergeEnvironmentVariables( true );
        $this->assertTrue( $process->getMergeEnvironmentVariables() );

        $process->setOptions( $opts = array( 'opt_1' => 'val 1',
                                             'opt_2' => 'val 2' ) );
        $this->assertEquals( $opts, $process->getOptions() );

        $process->setOptions( new ArrayIterator( $opts ) );
        $this->assertEquals( $opts, $process->getOptions() );

        $process->setOptions( null );
        $this->assertEquals( array(), $process->getOptions() );
    }

    /**
     * Test run command
     */
    public function testRunCommand()
    {
        $process = new Process( array(
            'command'   => 'test$command',
            'arguments' => array(
                'arg 1',
                'arg/2',
                'arg$3',
            ),
        ) );

        $this->assertEquals(
            escapeshellcmd( 'test$command' ) .
            ' ' . escapeshellarg( 'arg 1' ) .
            ' ' . escapeshellarg( 'arg/2' ) .
            ' ' . escapeshellarg( 'arg$3' ),
            $process->getRunCommand()
        );

        $emptyProcess = new Process;
        $this->assertNull( $emptyProcess->getRunCommand() );
    }

    /**
     * Test open() & close() & related functionaily
     */
    public function testOpenAndClose()
    {
        $process = new Process( array(
            'command'                   => $cmd = 'test_command',
            'openCallback'              => $this->getCallback( 'open' ),
            'closeCallback'             => $this->getCallback( 'close' ),
            'validationCallback'        => $this->getCallback( 'validate' ),
            'arguments'                 => $args = array( 'arg_1', 'arg_2' ),
            'workingDirectory'          => __DIR__,
            'environmentVariables'      => $envs = array( 'env_1' => 'val 1',
                                                          'env_2' => 'val 2' ),
            'mergeEnvironmentVariables' => false,
            'options'                   => $opts = array( 'opt_1' => 'val 1',
                                                          'opt_2' => 'val 2' ),
        ) );

        $descriptors = array();

        $this->callbacks
             ->expects( $this->any() )
             ->method( 'validate' )
             ->will( $this->returnCallback( function ( $pid ) {
                 return $pid == 'pid';
             } ) );

        $this->callbacks
             ->expects( $this->once() )
             ->method( 'open' )
             ->with( escapeshellcmd( $cmd ) . ' ' .
                     implode( ' ', array_map( 'escapeshellarg', $args ) ),
                     $descriptors,
                     array(),
                     __DIR__,
                     $envs,
                     $opts )
             ->will( $this->returnValue( 'pid' ) );

        $this->callbacks
             ->expects( $this->once() )
             ->method( 'close' )
             ->with( 'pid' )
             ->will( $this->returnValue( true ) );

        $process->run();
    }

}
