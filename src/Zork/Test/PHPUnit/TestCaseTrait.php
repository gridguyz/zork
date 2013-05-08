<?php

namespace Zork\Test\PHPUnit;

/**
 * TestCaseTrait
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait TestCaseTrait
{

    /**
     * Assert every values to match for a callback, or a constraint
     *
     * @param   callable|\PHPUnit_Framework_Constraint  $callbackOrConstraint
     * @param   array|\Traversable                      $arrayOrTraversable
     * @param   string                                  $message
     */
    public static function assertEvery( $callbackOrConstraint, $arrayOrTraversable, $message = '' )
    {
        $constraint = new Constraint\Every( $callbackOrConstraint );
        self::assertThat( $arrayOrTraversable, $constraint, $message );
    }

    /**
     * Assert some values to match for a callback, or a constraint
     *
     * @param   callable|\PHPUnit_Framework_Constraint  $callbackOrConstraint
     * @param   array|\Traversable                      $arrayOrTraversable
     * @param   string                                  $message
     */
    public static function assertSome( $callbackOrConstraint, $arrayOrTraversable, $message = '' )
    {
        $constraint = new Constraint\Some( $callbackOrConstraint );
        self::assertThat( $arrayOrTraversable, $constraint, $message );
    }

}
