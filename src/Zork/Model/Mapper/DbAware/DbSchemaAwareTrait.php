<?php

namespace Zork\Model\Mapper\DbAware;

use Zork\Db\Sql\TableIdentifier;

/**
 * SchemaAwareTrait
 *
 * implements SchemaAwareInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
trait DbSchemaAwareTrait
{

    /**
     * Schema used in all queries
     *
     * @example <pre>
     * protected $dbSchema = 'my_schema_name';
     * </pre>
     *
     * @abstract
     * @var string
     */
    protected $dbSchema = null;

    /**
     * Get schema
     *
     * @return string
     */
    public function getDbSchema()
    {
        return $this->dbSchema;
    }

    /**
     * Set schema
     *
     * @param string $schema
     * @return self
     */
    public function setDbSchema( $schema = null )
    {
        $this->dbSchema = ( (string) $schema ) ?: null;
        return $this;
    }

    /**
     * Get table in schema
     *
     * @param   string      $table
     * @param   string|null $dbSchema
     * @return  \Zork\Db\Sql\TableIdentifier
     * @see     \Zend\Db\Adapter\Platform\PlatformInterface::quoteIdentifierChain()
     */
    protected function getTableInSchema( $table, $dbSchema = null )
    {
        if ( null === $dbSchema )
        {
            $dbSchema = $this->dbSchema;
        }

        if ( $dbSchema )
        {
            return new TableIdentifier( $table, $dbSchema ?: null );
        }

        return $table;
    }

}
