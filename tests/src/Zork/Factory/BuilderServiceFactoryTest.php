<?php

namespace Zork\Factory;

use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * BuilderServiceFactoryTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Factory\BuilderServiceFactory
 */
class BuilderServiceFactoryTest extends TestCase
{

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        include_once __DIR__ . '/_files/BuilderTestClasses.php';
    }

    /**
     * Test create servive
     */
    public function testCreateService()
    {
        $ns = __NAMESPACE__ . '\\BuilderTest\\';
        $serviceFactory = new BuilderServiceFactory;
        $serviceManager = new ServiceManager;

        $serviceManager->setAlias( 'Configuration', 'Config' );
        $serviceManager->setService( 'Config', array(
            'factory'   => array(
                $ns . 'Factory'     => array(
                    'dependency'    => array( $ns . 'Dependecy', 'Countable' ),
                    'adapter'       => array(
                        'adapter1'  => $ns . 'Adapter1',
                        'adapter2'  => $ns . 'Adapter2',
                    ),
                ),
            ),
        ) );

        $builder = $serviceFactory->createService( $serviceManager );
        $this->assertFalse( $builder->isFactoryRegistered( 'NonExistentFactory' ) );
        $this->assertTrue( $builder->isFactoryRegistered( $ns . 'Factory' ) );
        $this->assertTrue( $builder->isAdapterRegistered( $ns . 'Factory', $ns . 'Adapter1' ) );
        $this->assertTrue( $builder->isAdapterRegistered( $ns . 'Factory', $ns . 'Adapter2' ) );
        $this->assertFalse( $builder->isAdapterRegistered( $ns . 'Factory', $ns . 'Adapter3' ) );
    }

}
