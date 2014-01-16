<?php

namespace Zork\Test\Pdo;

/**
 * Pdo
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Pdo extends \PDO
{

    /**
     * Disable original constructor
     */
    public function __construct()
    {
    }

}
