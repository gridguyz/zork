<?php

namespace Zork\Session;

use SessionHandler;
use Zend\Session\SaveHandler\SaveHandlerInterface;

/**
 * ReadOnlyHandler
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 * @codeCoverageIgnore
 */
class ReadOnlyHandler extends SessionHandler implements SaveHandlerInterface
{

    /**
     * @param type $session_id
     * @param type $session_data
     */
    public function write ( $session_id, $session_data )
    {
        // do nothing
    }

    /**
     * @param type $maxlifetime
     */
    public function gc ( $maxlifetime )
    {
        // do nothing
    }

}
