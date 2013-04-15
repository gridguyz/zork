<?php

namespace Zork\Permissions\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;

/**
 * Acl
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Acl extends ZendAcl
{

    /**
     * @var array
     */
    private $allResources = array();

    /**
     * @param string $type
     * @param array|null $roles
     * @param array|null $privileges
     * @param \Zend\Permissions\Acl\Assertion\AssertionInterface $assert
     */
    private function getAllResourcesId( $type, $roles, $privileges, AssertionInterface $assert = null )
    {
        $roleIds = array();

        if ( ! empty( $roles ) )
        {
            foreach ( (array) $roles as $role )
            {
                if ( $role instanceof RoleInterface )
                {
                    $roleIds[] = $role->getRoleId();
                }
                else
                {
                    $roleIds[] = (string) $role;
                }
            }
        }

        return http_build_query( array(
            't' => $type,
            'r' => $roleIds,
            'p' => (array) $privileges,
            'a' => $assert ? spl_object_hash( $assert ) : null,
        ) );
    }

    /**
     * @param string $type
     * @param array|null $roles
     * @param array|null $privileges
     * @param AssertionInterface $assert
     */
    private function addForAllResources( $type, $roles, $privileges,
                                         AssertionInterface $assert = null )
    {
        $id = $this->getAllResourcesId( $type, $roles, $privileges, $assert );
        $this->allResources[$id] = array( $type, $roles, $privileges, $assert );
        return $this;
    }

    /**
     * @param string $type
     * @param array|null $roles
     * @param array|null $privileges
     * @param AssertionInterface $assert
     */
    private function removeFromAllResources( $type, $roles, $privileges,
                                             AssertionInterface $assert = null )
    {
        $id = $this->getAllResourcesId( $type, $roles, $privileges, $assert );
        unset( $this->allResources[$id] );
        return $this;
    }

    /**
     * Performs operations on ACL rules
     *
     * The $operation parameter may be either OP_ADD or OP_REMOVE, depending on whether the
     * user wants to add or remove a rule, respectively:
     *
     * OP_ADD specifics:
     *
     *      A rule is added that would allow one or more Roles access to [certain $privileges
     *      upon] the specified Resource(s).
     *
     * OP_REMOVE specifics:
     *
     *      The rule is removed only in the context of the given Roles, Resources, and privileges.
     *      Existing rules to which the remove operation does not apply would remain in the
     *      ACL.
     *
     * The $type parameter may be either TYPE_ALLOW or TYPE_DENY, depending on whether the
     * rule is intended to allow or deny permission, respectively.
     *
     * The $roles and $resources parameters may be references to, or the string identifiers for,
     * existing Resources/Roles, or they may be passed as arrays of these - mixing string identifiers
     * and objects is ok - to indicate the Resources and Roles to which the rule applies. If either
     * $roles or $resources is null, then the rule applies to all Roles or all Resources, respectively.
     * Both may be null in order to work with the default rule of the ACL.
     *
     * The $privileges parameter may be used to further specify that the rule applies only
     * to certain privileges upon the Resource(s) in question. This may be specified to be a single
     * privilege with a string, and multiple privileges may be specified as an array of strings.
     *
     * If $assert is provided, then its assert() method must return true in order for
     * the rule to apply. If $assert is provided with $roles, $resources, and $privileges all
     * equal to null, then a rule having a type of:
     *
     *      TYPE_ALLOW will imply a type of TYPE_DENY, and
     *
     *      TYPE_DENY will imply a type of TYPE_ALLOW
     *
     * when the rule's assertion fails. This is because the ACL needs to provide expected
     * behavior when an assertion upon the default ACL rule fails.
     *
     * @param  string                                   $operation
     * @param  string                                   $type
     * @param  Role\RoleInterface|string|array          $roles
     * @param  Resource\ResourceInterface|string|array  $resources
     * @param  string|array                             $privileges
     * @param  Assertion\AssertionInterface             $assert
     * @throws Exception\InvalidArgumentException
     * @return Acl Provides a fluent interface
     */
    public function setRule( $operation,
                             $type,
                             $roles                     = null,
                             $resources                 = null,
                             $privileges                = null,
                             AssertionInterface $assert = null )
    {
        $allResources = empty( $resources );

        $result = parent::setRule(
            $operation,
            $type,
            $roles,
            $resources,
            $privileges,
            $assert
        );

        if ( $allResources )
        {
            switch ( strtoupper( $operation ) )
            {
                case self::OP_ADD:

                    $this->addForAllResources(
                        $type,
                        $roles,
                        $resources,
                        $privileges,
                        $assert
                    );

                    break;

                case self::OP_REMOVE:

                    $this->removeFromAllResources(
                        $type,
                        $roles,
                        $resources,
                        $privileges,
                        $assert
                    );

                    break;
            }
        }

        return $result;
    }

    /**
     * Adds a Resource having an identifier unique to the ACL
     *
     * The $parent parameter may be a reference to, or the string identifier for,
     * the existing Resource from which the newly added Resource will inherit.
     *
     * @param  Resource\ResourceInterface|string $resource
     * @param  Resource\ResourceInterface|string $parent
     * @throws Exception\InvalidArgumentException
     * @return Acl Provides a fluent interface
     */
    public function addResource( $resource, $parent = null )
    {
        $result = parent::addResource( $resource, $parent );

        foreach ( $this->allResources as $rule )
        {
            list( $type, $roles, $privileges, $assert ) = $rule;
            parent::setRule( self::OP_ADD, $type, $roles, $resource, $privileges, $assert );
        }

        return $result;
    }

}
