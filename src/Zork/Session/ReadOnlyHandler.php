<?php

namespace Zork\Session;

use SessionHandler;

/**
 * ReadOnlyHandler
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ReadOnlyHandler extends SessionHandler
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
