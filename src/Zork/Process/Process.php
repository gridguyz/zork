<?php

namespace Zork\Process;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zork\Stdlib\PropertiesTrait;

/**
 * Process
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Process
{

    use PropertiesTrait;

    /**
     * @const int
     */
    const STREAM_STDIN = 0;

    /**
     * @const int
     */
    const STREAM_STDOUT = 1;

    /**
     * @const int
     */
    const STREAM_STDERR = 2;

    /**
     * @const string
     */
    const TYPE_PIPE = 'pipe';

    /**
     * @const string
     */
    const TYPE_FILE = 'file';

    /**
     * @const string
     */
    const MODE_READ = 'r';

    /**
     * @const string
     */
    const MODE_WRITE = 'w';

    /**
     * @const string
     */
    const MODE_APPEND = 'a';

    /**
     * @var string
     */
    protected $command;

    /**
     * @var array
     */
    protected $arguments = array();

    /**
     * @var array
     */
    protected $workingDirectory;

    /**
     * @var array
     */
    protected $environmentVariables;

    /**
     * @var array
     */
    protected $mergeEnvironmentVariables = true;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var array
     */
    protected $pipes = array();

    /**
     * Set command
     *
     * @param   string|null $command
     * @return  Process
     */
    public function setCommand( $command )
    {
        $this->command = (string) $command ?: null;
        return $this;
    }

    /**
     * Set arguments
     *
     * @param   array|\Traversable|null $arguments
     * @return  Process
     */
    public function setArguments( $arguments )
    {
        if ( empty( $arguments ) )
        {
            $arguments = array();
        }
        else if ( $arguments instanceof Traversable )
        {
            $arguments = ArrayUtils::iteratorToArray( $arguments );
        }
        else
        {
            $arguments = (array) $arguments;
        }

        $this->arguments = array_filter( $arguments );
        return $this;
    }

    /**
     * Set working directory
     *
     * @param   string|null $workingDirectory
     * @return  Process
     */
    public function setWorkingDirectory( $workingDirectory )
    {
        $this->workingDirectory = (string) $workingDirectory ?: null;
        return $this;
    }

    /**
     * Set environment variables
     *
     * @param   array|\Traversable|null $environment
     * @return  Process
     */
    public function setEnvironmentVariables( $environment )
    {
        if ( $environment instanceof Traversable )
        {
            $environment = ArrayUtils::iteratorToArray( $environment );
        }

        if ( empty( $environment ) )
        {
            $environment = null;
        }
        else
        {
            $environment = (array) $environment;
        }

        $this->environmentVariables = $environment;
        return $this;
    }

    /**
     * Set merge environment variables
     *
     * @param   bool    $merge
     * @return  Process
     */
    public function setMergeEnvironmentVariables( $merge = true )
    {
        $this->mergeEnvironmentVariables = (bool) $merge;
        return $this;
    }

    /**
     * Set options
     *
     * @param   array|\Traversable|null $options
     * @return  Process
     */
    public function setOptions( $options )
    {
        if ( empty( $options ) )
        {
            $options = array();
        }
        else if ( $options instanceof Traversable )
        {
            $options = ArrayUtils::iteratorToArray( $options );
        }
        else
        {
            $options = (array) $options;
        }

        $this->options = $options;
        return $this;
    }

    /**
     * Constructor
     *
     * @param   array|\Traversable  $options
     */
    public function __construct( $properties = null )
    {
        if ( ! empty( $properties ) )
        {
            foreach ( $properties as $key => $value )
            {
                $this->$key = $value;
            }
        }
    }

    /**
     * Get run command
     *
     * @return  string|null
     */
    public function getRunCommand()
    {
        if ( empty( $this->command ) )
        {
            return null;
        }

        $result = escapeshellcmd( $this->command );

        foreach ( $this->arguments as $argument )
        {
            $result .= ' ' . escapeshellarg( $argument );
        }

        return $result;
    }

    /**
     * Is opened
     *
     * @return  bool
     */
    public function isOpened()
    {
        return $this->resource && is_resource( $this->resource );
    }

    /**
     * Open process
     *
     * @param   array   $descriptorspec
     * @return  bool
     */
    public function open( array $descriptorspec = array() )
    {
        $this->close();

        $environment = (array) $this->environmentVariables;

        if ( empty( $_ENV ) )
        {
            $globalVariables = array();

            foreach ( $_SERVER as $key => $value )
            {
                $value = getenv( $key );

                if ( false !== $value )
                {
                    $globalVariables[$key] = $value;
                }
            }
        }
        else
        {
            $globalVariables = $_ENV;
        }

        if ( empty( $environment ) )
        {
            $environment = $globalVariables;
        }
        else if ( $this->mergeEnvironmentVariables )
        {
            $environment = array_merge( $globalVariables, $environment );
        }

        $this->resource = proc_open(
            $this->getRunCommand(),
            $descriptorspec,
            $this->pipes,
            $this->workingDirectory ?: getcwd(),
            $environment,
            $this->options
        );

        return $this->isOpened();
    }

    /**
     * Get pipe
     *
     * @param   int $stream
     * @return  resource|null
     */
    public function getPipe( $stream )
    {
        $stream = (int) $stream;
        return isset( $this->pipes[$stream] ) ? $this->pipes[$stream] : null;
    }

    /**
     * Close process
     *
     * @return  int|bool|null
     */
    public function close()
    {
        if ( $this->isOpened() )
        {
            foreach ( $this->pipes as $pipe )
            {
                if ( is_reource( $pipe ) )
                {
                    fclose( $pipe );
                }
            }

            $result = proc_close( $this->resource );
            $this->resource = null;
            $this->pipes    = array();
            return $result;
        }

        return null;
    }

    /**
     * Run process
     *
     * @return  int|bool
     */
    public function run()
    {
        $this->open();
        return $this->close();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->close();
    }

}
