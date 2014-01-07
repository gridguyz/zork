<?php

namespace Zork\Test\PHPUnit\DbAdapterAware;

use Zork\Model\DbAdapterAwareInterface;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zork\Test\PHPUnit\TestCase as BaseTestCase;
// use Zend\Db\Adapter\Platform\PlatformInterface;

/**
 * TestCase
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TestCase extends BaseTestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $dbDriverMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $dbPlatformMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $dbConnectionMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $dbAdapterMock;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->dbDriverMock     = null;
        $this->dbPlatformMock   = null;
        $this->dbConnectionMock = null;
        $this->dbAdapterMock    = null;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getMockedDbDriver()
    {
        if ( empty( $this->dbDriverMock ) )
        {
            $this->dbDriverMock = $this->getMock(
                'Zend\Db\Adapter\Driver\DriverInterface',
                array(),
                array(),
                '',
                false,
                false
            );

            $this->dbDriverMock
                 ->expects( $this->any() )
                 ->method( 'getDatabasePlatformName' )
                 ->will( $this->returnValue( 'SQL92' ) );

            $this->dbDriverMock
                 ->expects( $this->any() )
                 ->method( 'checkEnvironment' )
                 ->will( $this->returnValue( true ) );

            $this->dbDriverMock
                 ->expects( $this->any() )
                 ->method( 'getConnection' )
                 ->will( $this->returnCallback( array(
                     $this, 'getMockedDbConnection'
                 ) ) );

         /* $this->dbDriverMock
                 ->expects( $this->any() )
                 ->method( 'createStatement' )
                 ->will( $this->returnValue( null ) ); */

         /* $this->dbDriverMock
                 ->expects( $this->any() )
                 ->method( 'createResult' )
                 ->will( $this->returnValue( null ) ); */

            $this->dbDriverMock
                 ->expects( $this->any() )
                 ->method( 'getPrepareType' )
                 ->will( $this->returnValue( DriverInterface::PARAMETERIZATION_POSITIONAL ) );

            $this->dbDriverMock
                 ->expects( $this->any() )
                 ->method( 'formatParameterName' )
                 ->will( $this->returnValue( '?' ) );

         /* $this->dbDriverMock
                 ->expects( $this->any() )
                 ->method( 'getLastGeneratedValue' )
                 ->will( $this->returnValue( null ) ); */
        }

        return $this->dbDriverMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getMockedDbPlatform()
    {
        if ( empty( $this->dbPlatformMock ) )
        {
            $this->dbPlatformMock = $this->getMock(
                'Zend\Db\Adapter\Platform\Sql92',
                null,
                array(),
                '',
                true,
                true
            );
        }

        return $this->dbPlatformMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getMockedDbConnection()
    {
        if ( empty( $this->dbConnectionMock ) )
        {
            $this->dbConnectionMock = $this->getMock(
                'Zend\Db\Adapter\Driver\ConnectionInterface',
                array(),
                array(),
                '',
                false,
                false
            );
        }

        return $this->dbConnectionMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getMockedDbAdapter()
    {
        if ( empty( $this->dbAdapterMock ) )
        {
            $this->dbAdapterMock = $this->getMock(
                'Zend\Db\Adapter\Adapter',
                array(),
                array(),
                '',
                false,
                false
            );

            $this->dbAdapterMock
                 ->expects( $this->any() )
                 ->method( 'getDriver' )
                 ->will( $this->returnCallback( array(
                     $this,
                     'getMockedDbDriver'
                 ) ) );

            $this->dbAdapterMock
                 ->expects( $this->any() )
                 ->method( 'getPlatform' )
                 ->will( $this->returnCallback( array(
                     $this,
                     'getMockedDbPlatform'
                 ) ) );
        }

        return $this->dbAdapterMock;
    }

    /**
     * @param   DbAdapterAwareInterface $dbAdapterAware
     * @return  DbAdapterAwareInterface $dbAdapterAware
     */
    public function getMockedDbAdapterAware( DbAdapterAwareInterface $dbAdapterAware )
    {
        $dbAdapterAware->setDbAdapter( $this->getMockedDbAdapter() );
        return $dbAdapterAware;
    }

    /**
     * @param   string      $sql
     * @param   array       $parameters
     * @param   array|int   $results
     * @return  void
     */
    protected function expectStatementSql( $sql,
                                           $parameters  = array(),
                                           $results     = array(),
                                           $generated   = null )
    {
        $statement = $this->getMock(
            'Zend\Db\Adapter\Driver\StatementInterface',
            array(),
            array(),
            '',
            false,
            false
        );

        $result = $this->getMock(
            'Zend\Db\Adapter\Driver\ResultInterface',
            array(),
            array(),
            '',
            false,
            false
        );

        $this->getMockedDbDriver()
             ->expects( $this->once() )
             ->method( 'createStatement' )
             ->will( $this->returnValue( $statement ) );

        if ( null !== $generated )
        {
            $this->getMockedDbDriver()
                 ->expects( $this->once() )
                 ->method( 'getLastGeneratedValue' )
                 ->will( $this->returnValue( $generated ) );
        }

        $statement->expects( $this->any() )
                  ->method( 'getResource' )
                  ->will( $this->returnValue( null ) );

        $statement->expects( $this->any() )
                  ->method( 'setSql' )
                  ->with( $sql )
                  ->will( $this->returnSelf() );

        $statement->expects( $this->any() )
                  ->method( 'getSql' )
                  ->will( $this->returnValue( $sql ) );

        $statement->expects( $this->never() )
                  ->method( 'setParameterContainer' );

        $parameterContainer = new \Zend\Db\Adapter\ParameterContainer;

        $statement->expects( $this->any() )
                  ->method( 'getParameterContainer' )
                  ->will( $this->returnValue( $parameterContainer ) );

        $statement->expects( $this->any() )
                  ->method( 'prepare' )
                  ->will( $this->returnValue( $statement ) );

        $statement->expects( $this->any() )
                  ->method( 'execute' )
                  ->will( $this->returnCallback( function () use ( $parameters, $parameterContainer, $result ) {
                      $this->assertEquals( $parameters, $parameterContainer->getPositionalArray() );
                      return $result;
                  } ) );

        $result->expects( $this->any() )
               ->method( 'buffer' )
               ->will( $this->returnValue( null ) );

        $result->expects( $this->any() )
               ->method( 'isBuffered' )
               ->will( $this->returnValue( false ) );

        $result->expects( $this->any() )
               ->method( 'isQueryResult' )
               ->will( $this->returnValue( true ) );

        $result->expects( $this->any() )
               ->method( 'getAffectedRows' )
               ->will( $this->returnValue( count( $results ) ) );

        $result->expects( $this->any() )
               ->method( 'getGeneratedValue' )
               ->will( $this->returnValue( $generated ) );

        $result->expects( $this->any() )
               ->method( 'getResource' )
               ->will( $this->returnValue( null ) );

        $count = 0;

        if ( $results instanceof \Traversable )
        {
            $result = iterator_to_array( $result );
        }

        if ( is_array( $results ) )
        {
            foreach ( $results as $row )
            {
                $count = max( $count, count( $row ) );
            }
        }
        else if ( is_numeric( $results ) )
        {
            $results = array_fill( 0, $results, array() );
        }

        $result->expects( $this->any() )
               ->method( 'getFieldCount' )
               ->will( $this->returnValue( $count ) );

        $result->expects( $this->any() )
               ->method( 'count' )
               ->will( $this->returnValue( count( $results ) ) );

        $result->expects( $this->any() )
               ->method( 'current' )
               ->will( $this->returnCallback( function () use ( & $results ) {
                   return current( $results );
               } ) );

        $result->expects( $this->any() )
               ->method( 'key' )
               ->will( $this->returnCallback( function () use ( & $results ) {
                   return key( $results );
               } ) );

        $result->expects( $this->any() )
               ->method( 'next' )
               ->will( $this->returnCallback( function () use ( & $results ) {
                   return next( $results );
               } ) );

        $result->expects( $this->any() )
               ->method( 'valid' )
               ->will( $this->returnCallback( function () use ( & $results ) {
                   return null !== key( $results );
               } ) );

        $result->expects( $this->any() )
               ->method( 'rewind' )
               ->will( $this->returnCallback( function () use ( & $results ) {
                   return reset( $results );
               } ) );
    }

}
