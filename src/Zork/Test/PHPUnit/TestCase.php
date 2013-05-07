<?php

namespace Zork\Test\PHPUnit;

use PHPUnit_Framework_TestCase;

/**
 * TestCase
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class TestCase extends PHPUnit_Framework_TestCase
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
