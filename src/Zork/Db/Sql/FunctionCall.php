<?php

namespace Zork\Db\Sql;

use Zend\Db\Sql\Exception;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\AbstractSql;
use Zend\Db\Sql\SqlInterface;
use Zend\Db\Adapter\Platform\Sql92;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\PreparableSqlInterface;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\StatementContainerInterface;

/**
 * FunctionCall
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class FunctionCall extends AbstractSql
                implements SqlInterface,
                           PreparableSqlInterface
{

    /**#@+
     * Constants
     *
     * @const
     */
    const MODE_SINGLE       = 'callSingle';
    const MODE_RESULT_SET   = 'callResultSet';
    const MODE_DEFAULT      = self::MODE_SINGLE;
    const ARGUMENTS_MERGE   = 'merge';
    const ARGUMENTS_SET     = 'set';
    /**#@-*/

    /**
     * @var array Specification array
     */
    protected $specifications = array(
        self::MODE_SINGLE       => 'SELECT %1$s(%2$s) AS %3$s',
        self::MODE_RESULT_SET   => 'SELECT * FROM %1$s(%2$s)',
    );

    /**
     * @var string
     */
    protected $mode             = self::MODE_DEFAULT;

    /**
     * @var string
     */
    protected $function         = null;

    /**
     * @var array
     */
    protected $arguments        = array();

    /**
     * @var string
     */
    protected $resultKey        = 'result';

    /**
     * Constructor
     *
     * @param  null|string|array    $function
     * @param  null|array           $args
     * @param  null|string          $mode
     */
    public function __construct( $function = null, array $args = null, $mode = null )
    {
        if ( $function )
        {
            $this->name( $function );
        }

        if ( $args )
        {
            $this->arguments( $args );
        }

        if ( $mode )
        {
            $this->mode( $mode );
        }
    }

    /**
     * Create SELECT clause
     *
     * @param  string|array $function
     * @return FunctionCall
     */
    public function name( $function )
    {
        $this->function = $function;
        return $this;
    }

    /**
     * Set mode
     *
     * @param  string $mode
     * @return FunctionCall
     */
    public function mode( $mode = self::MODE_DEFAULT )
    {
        $this->mode = (string) $mode;
        return $this;
    }

    /**
     * Create AS clause
     *
     * @param  string $key
     * @return FunctionCall
     */
    public function resultKey( $key )
    {
        $this->resultKey = (string) $key;
        return $this;
    }

    /**
     * @return string|array
     */
    public function getName()
    {
        return $this->function;
    }

    /**
     * @return string|array
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return string
     */
    public function getResultKey()
    {
        return $this->resultKey;
    }

    /**
     * Specify arguments to call with
     *
     * @param  array $values
     * @param  string $flag one of ARGUMENTS_MERGE or ARGUMENTS_SET; defaults to ARGUMENTS_SET
     * @return FunctionCall
     */
    public function arguments( array $values, $flag = self::ARGUMENTS_SET )
    {
        if ( $flag == self::ARGUMENTS_MERGE )
        {
            $this->arguments = array_merge( $this->arguments, $values );
        }
        else
        {
            $this->arguments = $values;
        }

        return $this;
    }

    /**
     * @param null|string $key
     * @return mixed
     */
    public function getRawState( $key = null )
    {
        $rawState = array(
            'mode'      => $this->mode,
            'name'      => $this->function,
            'function'  => $this->function,
            'arguments' => $this->arguments,
            'resultKey' => $this->resultKey,
        );

        return ( isset( $key ) && array_key_exists( $key, $rawState ) )
                ? $rawState[$key] : $rawState;
    }

    /**
     * @param   AdapterInterface            $adapter
     * @param   StatementContainerInterface $statementContainer
     * @return  void
     */
    public function prepareStatement( AdapterInterface $adapter,
                                      StatementContainerInterface $statementContainer )
    {
        $driver   = $adapter->getDriver();
        $platform = $adapter->getPlatform();
        $parameterContainer = $statementContainer->getParameterContainer();

        if ( ! $parameterContainer instanceof ParameterContainer )
        {
            $parameterContainer = new ParameterContainer();
            $statementContainer->setParameterContainer( $parameterContainer );
        }

        $function   = $platform->quoteIdentifierChain( $this->function );
        $resultKey  = $platform->quoteIdentifier( $this->resultKey );
        $arguments  = array();
        $i          = 0;

        foreach ( $this->arguments as $argument )
        {
            if ( $argument instanceof Expression )
            {
                $exprData       = $this->processExpression( $argument, $platform, $driver );
                $arguments[]    = $exprData->getSql();
                $parameterContainer->merge( $exprData->getParameterContainer() );
            }
            else
            {
                $parameterName = 'func_arg_' . ( $i++ );
                $arguments[] = $driver->formatParameterName( $parameterName );
                $parameterContainer->offsetSet( $parameterName, $argument );
            }
        }

        $sql = sprintf(
            $this->specifications[$this->mode],
            $function,
            implode( ', ', $arguments ),
            $resultKey
        );

        $statementContainer->setSql( $sql );
    }

    /**
     * Get SQL string for this statement
     *
     * @param  null|PlatformInterface $adapterPlatform Defaults to Sql92 if none provided
     * @return string
     */
    public function getSqlString( PlatformInterface $adapterPlatform = null )
    {
        $adapterPlatform = $adapterPlatform ?: new Sql92;
        $function   = $adapterPlatform->quoteIdentifierChain( $this->function );
        $resultKey  = $adapterPlatform->quoteIdentifier( $this->resultKey );
        $arguments  = array();

        foreach ( $this->arguments as $argument )
        {
            if ( $argument instanceof Expression )
            {
                $exprData = $this->processExpression( $argument, $adapterPlatform );
                $arguments[] = $exprData->getSql();
            }
            elseif ( null === $argument )
            {
                $arguments[] = 'NULL';
            }
            else
            {
                $arguments[] = $adapterPlatform->quoteValue( $argument );
            }
        }

        return sprintf(
            $this->specifications[$this->mode],
            $function,
            implode( ', ', $arguments ),
            $resultKey
        );
    }

}
