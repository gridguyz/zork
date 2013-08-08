<?php

namespace Zork\Log\Formatter;

use Zend\Debug\Debug;
use Zend\Log\Formatter\Simple;

/**
 * ExtraHandler
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ExtraHandler extends Simple
{

    /**
     * @const string
     */
    const DEFAULT_FORMAT = "%timestamp% %priorityName% (%priority%)\n\n%message%\n\n%extra%";

    /**
     * This method formats the event for the PHP Exception
     *
     * @param   array   $event
     * @return  string
     */
    public function format( $event )
    {
        if ( ! empty( $event['extra'] ) )
        {
            $event['extra'] = (string) Debug::dump(
                $event['extra'],
                'Extra',
                false
            );
        }

        return @ parent::format( $event );
    }

}
