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
     * @const string
     */
    const DEFAULT_OPEN_CALLBACK = 'proc_open';

    /**
     * @const string
     */
    const DEFAULT_CLOSE_CALLBACK = 'proc_close';

    /**
     * @const string
     */
    const DEFAULT_VALIDATION_CALLBACK = 'is_resource';

    /**
     * @var string
     */
    protected $command;

    /**
     * @var callable
     */
    protected $openCallback = self::DEFAULT_OPEN_CALLBACK;

    /**
     * @var callable
     */
    protected $closeCallback = self::DEFAULT_CLOSE_CALLBACK;

    /**
     * @var callable
     */
    protected $validationCallback = self::DEFAULT_VALIDATION_CALLBACK;

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
     * Get command
     *
     * @return  string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set open callback
     *
     * @param   callable $callback
     * @return  Process
     */
    public function setOpenCallback( callable $callback )
    {
        $this->openCallback = $callback;
        return $this;
    }

    /**
     * Get open callback
     *
     * @return  callable
     */
    public function getOpenCallback()
    {
        return $this->openCallback;
    }

    /**
     * Set close callback
     *
     * @param   callable $callback
     * @return  Process
     */
    public function setCloseCallback( callable $callback )
    {
        $this->closeCallback = $callback;
        return $this;
    }

    /**
     * Get close callback
     *
     * @return  callable
     */
    public function getCloseCallback()
    {
        return $this->closeCallback;
    }

    /**
     * Set validation callback
     *
     * @param   callable $callback
     * @return  Process
     */
    public function setValidationCallback( callable $callback )
    {
        $this->validationCallback = $callback;
        return $this;
    }

    /**
     * Get validation callback
     *
     * @return  callable
     */
    public function getValidationCallback()
    {
        return $this->validationCallback;
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
     * Get arguments
     *
     * @return  array
     */
    public function getArguments()
    {
        return $this->arguments;
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
     * Get working directory
     *
     * @return  string|null
     */
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
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
     * Get environment variables
     *
     * @return  array|null
     */
    public function getEnvironmentVariables()
    {
        return $this->environmentVariables;
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
     * Get merge environment variables
     *
     * @return  bool
     */
    public function getMergeEnvironmentVariables()
    {
        return $this->mergeEnvironmentVariables;
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
     * Get options
     *
     * @return  array
     */
    public function getOptions()
    {
        return $this->options;
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
        $validator = $this->validationCallback;
        return $this->resource && $validator( $this->resource );
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

        $open = $this->openCallback;
        $this->resource = $open(
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
                if ( is_resource( $pipe ) )
                {
                    fclose( $pipe );
                }
            }

            $close  = $this->closeCallback;
            $result = $close( $this->resource );
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
     *
     * @codeCoverageIgnore
     */
    public function __destruct()
    {
        $this->close();
    }

}
