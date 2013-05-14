<?php

namespace Zork\Permissions\Acl;

use Zend\ServiceManager\ServiceManager;
use Zend\Permissions\Acl\Role\GenericRole;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * AclTest
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @covers Zork\Permissions\Acl\Acl
 * @covers Zork\Permissions\Acl\AclServiceFactory
 */
class AclTest extends TestCase
{

    /**
     * Test all-resource rules
     */
    public function testAllResourceRules()
    {
        $acl = new Acl;
        $role = new GenericRole( 'role' );

        $acl->addRole( 'user' );
        $acl->addRole( $role );
        $acl->addResource( 'beforeAdd' );
        $acl->setRule( Acl::OP_ADD, Acl::TYPE_ALLOW, array( 'user', $role ), null );

        $this->assertTrue( $acl->isAllowed( 'user', 'beforeAdd' ) );
        $this->assertTrue( $acl->isAllowed( $role, 'beforeAdd' ) );

        $acl->addResource( 'afterAdd' );

        $this->assertTrue( $acl->isAllowed( 'user', 'afterAdd' ) );
        $this->assertTrue( $acl->isAllowed( $role, 'afterAdd' ) );

        $acl->setRule( Acl::OP_REMOVE, Acl::TYPE_ALLOW, array( 'user', $role ), null );

        $acl->addResource( 'afterRemove' );

        $this->assertFalse( $acl->isAllowed( 'user', 'beforeAdd' ) );
        $this->assertFalse( $acl->isAllowed( $role, 'beforeAdd' ) );
        $this->assertFalse( $acl->isAllowed( 'user', 'afterAdd' ) );
        $this->assertFalse( $acl->isAllowed( $role, 'afterAdd' ) );
        $this->assertFalse( $acl->isAllowed( 'user', 'afterRemove' ) );
        $this->assertFalse( $acl->isAllowed( $role, 'afterRemove' ) );
    }

    /**
     * Test service factory
     */
    public function testServiceFactory()
    {
        $service = new ServiceManager;
        $config  = array(
            'acl' => array(
                'roles' => array(
                    'root'      => null,
                    'parent1'   => 'root',
                    'parent2'   => 'root',
                    'child'     => array( 'parent1', 'parent2' ),
                ),
                'resources' => array(
                    'parent' => null,
                    'child'  => 'parent',
                ),
                'allow' => array(
                    array(
                        'role'      => 'root',
                        'resource'  => 'parent',
                        'privilege' => 'change',
                    ),
                ),
                'deny' => array(
                    array(
                        'role'      => 'child',
                        'resource'  => 'child',
                        'privilege' => 'change',
                    ),
                ),
            ),
        );

        $service->setService( 'Configuration', $config )
                ->setFactory( 'Zork\Permissions\Acl\Acl',
                              'Zork\Permissions\Acl\AclServiceFactory' )
                ->setAlias( 'Acl', 'Zork\Permissions\Acl\Acl' );

        /* @var $acl Acl */
        $acl = $service->get( 'Acl' );

        $this->assertInstanceOf( 'Zork\Permissions\Acl\Acl', $acl );

        $this->assertTrue( $acl->hasRole( 'root' ) );
        $this->assertTrue( $acl->hasRole( 'parent1' ) );
        $this->assertTrue( $acl->hasRole( 'parent2' ) );
        $this->assertTrue( $acl->hasRole( 'child' ) );

        $this->assertTrue( $acl->hasResource( 'parent' ) );
        $this->assertTrue( $acl->hasResource( 'child' ) );

        $this->assertTrue( $acl->isAllowed( 'root', 'parent', 'change' ) );
        $this->assertTrue( $acl->isAllowed( 'parent1', 'parent', 'change' ) );
        $this->assertTrue( $acl->isAllowed( 'parent2', 'parent', 'change' ) );
        $this->assertTrue( $acl->isAllowed( 'child', 'parent', 'change' ) );

        $this->assertTrue( $acl->isAllowed( 'root', 'child', 'change' ) );
        $this->assertTrue( $acl->isAllowed( 'parent1', 'child', 'change' ) );
        $this->assertTrue( $acl->isAllowed( 'parent2', 'child', 'change' ) );
        $this->assertFalse( $acl->isAllowed( 'child', 'child', 'change' ) );
    }

}
