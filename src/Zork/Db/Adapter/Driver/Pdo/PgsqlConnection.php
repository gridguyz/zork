<?php

namespace Zork\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver\Pdo\Connection;

/**
 * PgsqlConnection
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class PgsqlConnection extends Connection
{

    /**
     * Nestable transactions flag
     *
     * @var bool
     */
    protected $nestableTransactionsEnabled  = false;

    /**
     * Current transation nexting level
     *
     * @var int
     */
    protected $transactionNestingLevel      = 0;

    /**
     * Last transaction savepoint unque ID
     *
     * @var string
     */
    protected $transactionSavepointUniqueId;

    /**
     * @param array|\PDO|null $connectionParameters
     * @throws \Zend\Db\Adapter\Exception\InvalidArgumentException
     */
    public function __construct( $connectionParameters = null )
    {
        $this->transactionSavepointUniqueId = uniqid();

        if ( is_array( $connectionParameters ) &&
             isset( $connectionParameters['nestableTransactions'] ) )
        {
            $this->setNestableTransactionsEnabled(
                $connectionParameters['nestableTransactions']
            );
        }

        if ( is_array( $connectionParameters ) &&
             isset( $connectionParameters['schema'] ) )
        {
            $schema = $connectionParameters['schema'];
            unset( $connectionParameters['schema'] );
        }
        else
        {
            $schema = null;
        }

        parent::__construct( $connectionParameters );

        if ( null !== $schema )
        {
            if ( is_array( $schema ) )
            {
                $this->setCurrentSchemas( $schema );
            }
            else
            {
                $this->setCurrentSchema( $schema );
            }
        }
    }

    /**
     * Get current (active) schemas
     *
     * @param bool $catalog if true, pg_catalog is listed here
     * @return array|null
     */
    public function getCurrentSchemas( $catalog = false )
    {
        if ( ! $this->isConnected() )
        {
            $this->connect();
        }

        /** @var $result \PDOStatement */
        $result = $this->getResource()
                       ->query( 'SELECT UNNEST( CURRENT_SCHEMAS( ' .
                                ( $catalog ? 'TRUE' : 'FALSE' ) .
                                ' ) )' );

        if ( $result instanceof \PDOStatement )
        {
            return $result->fetchAll( \PDO::FETCH_COLUMN );
        }

        return null;
    }

    /**
     * Escape a schema name
     *
     * @staticvar array $escape
     * @param string $schema
     * @return string
     */
    protected function escapeSchema( $schema )
    {
        static $escape = array( '"' => '""' );
        return '"' . strtr( $schema, $escape ) . '"';
    }

    /**
     * Set current schemas
     *
     * @param array $schemas
     * @return \Zork\Db\Adapter\Driver\Pdo\PgsqlConnection
     */
    public function setCurrentSchemas( $schemas )
    {
        if ( ! $this->isConnected() )
        {
            $this->connect();
        }

        $this->getResource()
             ->query( 'SET search_path TO ' . implode(
                    ', ',
                    array_map(
                        array( $this, 'escapeSchema' ),
                        (array) $schemas
                    )
                ) );

        return $this;
    }

    /**
     * Set current schema
     *
     * @param string $schema
     * @return \Zork\Db\Adapter\Driver\Pdo\PgsqlConnection
     */
    public function setCurrentSchema( $schema )
    {
        $schema  = (string) $schema;
        $current = (array) $this->getCurrentSchemas( false );

        if ( isset( $current[0] ) && $current[0] == $schema )
        {
            return $this;
        }

        $current[0] = $schema;
        return $this->setCurrentSchemas( $current );
    }

    /**
     * Get current transation nexting level
     *
     * @return int
     */
    public function getTransactionnestingLevel()
    {
        return $this->transactionNestingLevel;
    }

    /**
     * Is nestable transactions enabled
     *
     * @return bool
     */
    public function isNestableTransactionsEnabled()
    {
        return $this->getNestableTransactionsEnabled();
    }

    /**
     * Get nestable transactions enabled flag
     *
     * @return bool
     */
    public function getNestableTransactionsEnabled()
    {
        return $this->nestableTransactionsEnabled;
    }

    /**
     * Set nestable transactions enabled flag
     *
     * @param bool $flag
     * @return \Zork\Db\Adapter\Driver\Pdo\PgsqlConnection
     */
    public function setNestableTransactionsEnabled( $flag )
    {
        $this->nestableTransactionsEnabled = (bool) $flag;
        return $this;
    }

    /**
     * Checks for unbalanced nested transactions
     *
     * @throws UnbalancedNestedTransactionsException
     */
    protected function checkUnbalancedNestedTransactions()
    {
        if ( $this->transactionNestingLevel < 0 )
        {
            throw new UnbalancedNestedTransactionsException(
                'Transaction nesting level cannot be under 0'
            );
        }
    }

    /**
     * Rollback all level of transactions
     *
     * @return \Zork\Db\Adapter\Driver\Pdo\PgsqlConnection
     * @throws UnbalancedNestedTransactionsException
     */
    public function resetTransactions()
    {
        if ( $this->isNestableTransactionsEnabled() )
        {
            $this->checkUnbalancedNestedTransactions();

            while ( $this->transactionNestingLevel > 0 )
            {
                $this->rollBack();
            }
        }
        else
        {
            $this->rollBack();
        }

        $this->transactionNestingLevel = 0;
        return $this;
    }

    /**
     * @return \Zork\Db\Adapter\Driver\Pdo\PgsqlConnection
     * @throws UnbalancedNestedTransactionsException
     */
    public function beginTransaction()
    {
        if ( $this->isNestableTransactionsEnabled() )
        {
            $this->checkUnbalancedNestedTransactions();

            if ( $this->transactionNestingLevel == 0 )
            {
                parent::beginTransaction();
            }
            else
            {
                if ( ! $this->isConnected() )
                {
                    $this->connect();
                }

                $this->getResource()
                     ->exec( 'SAVEPOINT zorksavepoint_' .
                             $this->transactionSavepointUniqueId .
                             '_' . $this->transactionNestingLevel );
            }

            $this->transactionNestingLevel++;
            return $this;
        }

        return parent::beginTransaction();
    }

    /**
     * @return \Zork\Db\Adapter\Driver\Pdo\PgsqlConnection
     * @throws UnbalancedNestedTransactionsException
     */
    public function commit()
    {
        if ( $this->isNestableTransactionsEnabled() )
        {
            $this->transactionNestingLevel--;
            $this->checkUnbalancedNestedTransactions();

            if ( $this->transactionNestingLevel == 0 )
            {
                parent::commit();
            }
            else
            {
                if ( ! $this->isConnected() )
                {
                    $this->connect();
                }

                $this->getResource()
                     ->exec( 'RELEASE SAVEPOINT zorksavepoint_' .
                             $this->transactionSavepointUniqueId .
                             '_' . $this->transactionNestingLevel );
            }

            return $this;
        }

        return parent::commit();
    }

    /**
     * @return \Zork\Db\Adapter\Driver\Pdo\PgsqlConnection
     * @throws UnbalancedNestedTransactionsException
     */
    public function rollBack()
    {
        if ( $this->isNestableTransactionsEnabled() )
        {
            $this->transactionNestingLevel--;
            $this->checkUnbalancedNestedTransactions();

            if ( $this->transactionNestingLevel == 0 )
            {
                parent::rollBack();
            }
            else
            {
                if ( ! $this->isConnected() )
                {
                    $this->connect();
                }

                $this->getResource()
                     ->exec( 'ROLLBACK TO SAVEPOINT zorksavepoint_' .
                             $this->transactionSavepointUniqueId .
                             '_' . $this->transactionNestingLevel );
            }

            return $this;
        }

        return parent::rollBack();
    }

}
