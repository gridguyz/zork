<?php

namespace Zork\Test\PHPUnit\Constraint;

use Traversable;
use PHPUnit_Framework_Constraint;
use PHPUnit_Util_InvalidArgumentHelper;

/**
 * CallbackOrConstraint
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class CallbackOrConstraint extends PHPUnit_Framework_Constraint
{

    /**
     * @var callable
     */
    protected $callbackOrConstraint;

    /**
     * @param   callable|\PHPUnit_Framework_Constraint  $callbackOrConstraint
     */
    public function __construct( $callbackOrConstraint )
    {
        if ( ! $callbackOrConstraint instanceof PHPUnit_Framework_Constraint &&
             ! is_callable( $callbackOrConstraint ) )
        {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(
                1,
                'PHPUnit_Framework_Constraint or a callable'
            );
        }

        $this->callbackOrConstraint = $callbackOrConstraint;
    }

    /**
     * Create traversable
     *
     * @param   mixed   $other
     * @return  array|\Traversable
     */
    protected function createTraversable( $other )
    {
        if ( is_array( $other ) || $other instanceof Traversable )
        {
            return $other;
        }

        if ( is_object( $other ) )
        {
            return array( $other );
        }

        return (array) $other;
    }

    /**
     * Match an entry
     *
     * @param   mixed   $entry
     * @return  bool
     */
    protected function matchEntry( $entry )
    {
        if ( $this->callbackOrConstraint instanceof PHPUnit_Framework_Constraint )
        {
            return $this->callbackOrConstraint->evaluate( $entry, '', true );
        }

        return (bool) call_user_func( $this->callbackOrConstraint, $entry );
    }

}
