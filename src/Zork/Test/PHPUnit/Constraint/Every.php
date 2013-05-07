<?php

namespace Zork\Test\PHPUnit\Constraint;

use PHPUnit_Util_Type;

/**
 * Every
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Every extends CallbackOrConstraint
{

    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param   mixed $other Value or object to evaluate.
     * @return  bool
     */
    protected function matches( $other )
    {
        foreach ( $this->createTraversable( $other ) as $entry )
        {
            if ( ! $this->matchEntry( $entry ) )
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return  string
     */
    public function toString()
    {
        return 'matches every with '
             . PHPUnit_Util_Type::export( $this->callbackOrConstraint );
    }

}
