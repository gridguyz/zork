<?php

namespace Zork\Cache;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * CacheManagerTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Cache\CacheManager
 * @covers Zork\Cache\AbstractCacheStorage
 */
class CacheManagerTest extends TestCase
{

    /**
     * Test defaults
     */
    public function testDefaults()
    {
        $manager = CacheManager::factory( array() );

        $this->assertEmpty( $manager->getStorageOptions() );
        $this->assertEmpty( $manager->getPatternOptions() );

        $manager = CacheManager::factory( array(
            'storage' => new ArrayIterator( array( 'storageOptions' ) ),
            'pattern' => new ArrayIterator( array( 'patternOptions' ) ),
        ) );

        $this->assertEquals( array( 'storageOptions' ), $manager->getStorageOptions() );
        $this->assertEquals( array( 'patternOptions' ), $manager->getPatternOptions() );
    }

    /**
     * Test get storate
     */
    public function testGetStorage()
    {
        $factory = $this->getMock( 'Zend\Cache\StorageFactory' );

        $manager = CacheManager::factory( array(
            'storage' => array(
                'factory'   => $factory,
                'adapter'   => array(
                    'name'      => 'memory',
                    'options'   => array(
                        'namespace' => 'Prefix',
                    ),
                ),
            ),
        ) );

        $factory::staticExpects( $this->once() )
                ->method( 'factory' )
                ->with( $this->equalTo( array(
                    'adapter'   => array(
                        'name'      => 'memory',
                        'options'   => array(
                            'namespace' => 'Prefix\\SampleNamespace',
                        ),
                    ),
                ) ) )
                ->will( $this->returnValue( null ) );

        $this->assertNull( $manager->getStorage( 'SampleNamespace' ) );
    }

    /**
     * Test get pattern
     */
    public function testGetPattern()
    {
        $factory = $this->getMock( 'Zend\Cache\PatternFactory' );

        $manager = CacheManager::factory( array(
            'storage' => array(
                'adapter'   => array(
                    'name' => 'memory'
                ),
            ),
            'pattern' => array(
                'CallbackCache' => array(
                    'factory' => $factory,
                ),
            ),
        ) );

        $factory::staticExpects( $this->once() )
                ->method( 'factory' )
                ->with( $this->equalTo( 'CallbackCache' ),
                        $this->arrayHasKey( 'storage' ) )
                ->will( $this->returnValue( null ) );

        $this->assertNull( $manager->getPattern( 'CallbackCache' ) );
    }

    /**
     * Test AbstractCacheStorage
     */
    public function testAbstractCacheStorage()
    {
        $manager = CacheManager::factory( array(
            'storage' => array(
                'adapter'   => array(
                    'name' => 'memory',
                ),
            ),
        ) );

        $abstractStorage = $this->getMockBuilder( 'Zork\Cache\AbstractCacheStorage' )
                                ->setMethods( null )
                                ->setConstructorArgs( array( $manager ) )
                                ->getMock();

        $this->assertInstanceOf(
            'Zend\Cache\Storage\StorageInterface',
            $abstractStorage->getCacheStorage()
        );
    }

}
