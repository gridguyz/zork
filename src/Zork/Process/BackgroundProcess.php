<?php

namespace Zork\Process;

/**
 * BackgroundProcess
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class BackgroundProcess extends Process
{

    /**
     * Get run command
     *
     * @return  string|null
     */
    public function getRunCommand()
    {
        $command = parent::getRunCommand();

        if ( $command )
        {
            if ( ( strtolower( substr( PHP_OS, 0, 3 ) ) === 'win' ) ||
                 ( strtolower( substr( PHP_OS, 0, 4 ) ) === 'uwin' ) )
            {
                $command = 'start /b ' . $command;
            }
            else
            {
                $command .= ' &';
            }
        }

        return $command;
    }

}
