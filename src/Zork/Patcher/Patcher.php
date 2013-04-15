<?php

namespace Zork\Patcher;

use FilesystemIterator;
use Zend\Db\Adapter\Adapter as DbAdapter;

/**
 * Patch
 *
 * @author Sipos Zoltán <sipiszoty@gmail.com>
 */
class Patcher
{

    /**
     * @var string
     */
    const PATCH_PATTERN = '/^(\\d{4}\\.\\d{2}\\.\\d{2})(\\.(\\d{2}))?-([^\\.]+)\\.sql$/i';

    /**
     * Patch directory
     *
     * @var string
     */
    protected $directory;

    /**
     * Db-config
     *
     * @var array
     */
    protected $dbConfig;

    /**
     * Execute on this schemas only (null if all)
     *
     * @var array|null
     */
    protected $schemas;

    /**
     * Db-adapterInstance
     *
     * @var \Zend\Db\Adapter\Adapter
     */
    private $dbAdapter;

    /**
     * Get the configured patch directory
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Set the patch directory
     *
     * @return Patcher Method returns own object
     */
    public function setDirectory( $dir )
    {
        $this->directory = $dir;
        return $this;
    }

    /**
     * Get the configured schemas
     *
     * @return string
     */
    public function getDbConfig()
    {
        return $this->dbConfig;
    }

    /**
     * Set the schemas
     *
     * @return Patcher Method returns own object
     */
    public function setDbConfig( $dbConfig )
    {
        $this->dbConfig = (array) $dbConfig;
        return $this;
    }

    /**
     * Get the configured schemas
     *
     * @return string
     */
    public function getSchemas()
    {
        return $this->schemas;
    }

    /**
     * Set the schemas
     *
     * @return Patcher Method returns own object
     */
    public function setSchemas( $schemas )
    {
        $this->schemas = $schemas ?: null;
        return $this;
    }

    /**
     * Return the current adapter by the cofiguration of Webriq 3 project
     *
     * @return \Zend\Db\Adapter\Abstract
     */
    protected function getDbAdapter()
    {
        if ( null === $this->dbAdapter )
        {
            $this->dbAdapter = new DbAdapter( $this->getDbConfig() );
        }

        return $this->dbAdapter;
    }

    /**
     * Collect patches from the $directory directory
     *
     * @return array
     */
    protected function getPatchList()
    {
        $result     = array();
        $iterator   = new FilesystemIterator( $this->getDirectory() );

        foreach ( $iterator as $fileInfo )
        {
            if ( $fileInfo->isFile() &&
                 ( $fileName = $fileInfo->getFilename() ) &&
                 preg_match( self::PATCH_PATTERN, $fileName ) )
            {
                $result[] = $fileName;
            }
        }

        natsort( $result );
        return $result;
    }

    /**
     * Method recieve the list of schemas
     *
     * @return array  Array set of schemas
     */
    protected function getSchemaList()
    {
        $schemas = $this->getSchemas();

        if ( empty( $schemas ) )
        {
            $schemas    = array( '_template' );
            $result     = $this->getDbAdapter()
                               ->query( 'SELECT "schema" FROM "_central"."site"' )
                               ->execute();

            foreach ( $result as $row )
            {
                $schemas[] = $row['schema'];
            }
        }

        return $schemas;
    }

    /**
     * Create a more complex data structure from two singe string-array.
     * Thet structure consists the full patch name, the simply patch name,
     * the date and the number of issue of each patches.
     * Also, the patch indexes contain the shemas array for updates.
     *
     * @param array $patchList   List of patches
     * @param array $schemaList  List of available patches
     * @return array             Complex data structure
     */
    protected function evaluatePatches( $patchList, $schemaList )
    {
        $patchIndex = array();
        $allSchemas = $schemaList;

        foreach ( $patchList as $patch )
        {
            $patchParts = array();
            preg_match( self::PATCH_PATTERN, $patch, $patchParts );

            $patchIndex[] = array(
                'patch'     => $patchParts[0],
                'date'      => $patchParts[1],
                'number'    => (int) $patchParts[3] ?: 1,
                'name'      => $patchParts[4],
                'schemas'   => $patchParts[4] == '_allsite'
                                ? $schemaList
                                : array( $patchParts[4] )
            );

            if ( $patchParts[4] != '_allsite' &&
                 ! in_array( $patchParts[4], $allSchemas ) )
            {
                $allSchemas[] = $patchParts[4];
            }
        }

        return array( $patchIndex, $allSchemas );
    }

    /**
     * Collect the executed patches of all schema.
     *
     * @param array $schemaList                     List of schemas
     * @return   An associative array, keys consists the schema names,
     *           elements consists a list of executed patches
     */
    protected function getContentOfSchemaPatchTables( $schemaList )
    {
        $result = array();
        $db     = $this->getDbAdapter();
        $plf    = $db->getPlatform();

        foreach ( $schemaList as $schema )
        {
            $result[$schema] = array();
            $schemaQuoted    = $plf->quoteIdentifier( $schema );

            $db->query( '
                    CREATE TABLE IF NOT EXISTS ' . $schemaQuoted . '."patch"
                    (
                        "id"        SERIAL                      PRIMARY KEY,
                        "patch"     CHARACTER VARYING           NOT NULL    UNIQUE,
                        "date"      DATE                        NOT NULL,
                        "number"    INTEGER                     NOT NULL,
                        "name"      CHARACTER VARYING           NOT NULL,
                        "timestamp" TIMESTAMP WITH TIME ZONE    NOT NULL    DEFAULT CURRENT_TIMESTAMP
                    )
                ' )
               ->execute();

            $query = $db->query( 'SELECT "patch" FROM ' . $schemaQuoted . '."patch"' )
                        ->execute();

            foreach ( $query as $row )
            {
                $result[$schema][] = $row['patch'];
            }
        }

        return $result;
    }

    /**
     * Method filter element whiches are contained by schema array
     *
     * @param array $patchList    Patch list with patch records
     * @param array $schemaList   Schema list,
     * @return array              The filterd patch list elements
     */
    protected function filterPatches( $patchList, $schemaList )
    {
        foreach ( $patchList as $patchKey => &$patch )
        {
            foreach ( $patch['schemas'] as $schemaKey => $schema )
            {
                if ( isset( $schemaList[$schema] ) &&
                     is_array( $schemaList[$schema] ) &&
                     in_array( $patch['patch'], $schemaList[$schema] ) )
                {
                    unset( $patchList[$patchKey]['schemas'][$schemaKey] );
                }
            }

            if ( empty( $patch['schemas'] ) )
            {
                unset( $patchList[$patchKey] );
            }
        }

        return $patchList;
    }

    /**
     * Execute query-s in a transaction from patches applied to the specified schemas
     *
     * @param array $patchList Valuable patches to execute in transaction
     * @return                 An argument list, which consists informations about the process.
     */
    protected function executePatchTransaction( $patchList )
    {
        $result = new Result();
        $result->log( 'Starting patcher', Result::LOG_INFO );

        if ( empty( $patchList ) )
        {
            $result->log( 'Empty patch-list', Result::LOG_INFO );
            $result->status = Result::STATUS_NONE;
            return $result;
        }

        $result->log( 'Begin transaction', Result::LOG_INFO );
        $result->log( 'BEGIN', Result::LOG_SQL );

        $db   = $this->getDbAdapter();
        $plf  = $db->getPlatform();
        $drv  = $db->getDriver();
        $conn = $drv->getConnection();
        $pdo  = $conn->getResource();

        $conn->beginTransaction();

        try
        {
            $sql = function ( $sql ) use ( $pdo, $result )
            {
                $result->log( $sql, Result::LOG_SQL );
                return $pdo->exec( $sql );
            };

            foreach ( $patchList as $patchRecord )
            {
                $patch = $patchRecord['patch'];

                $result->patchList[] = $patch;
                $result->patch       = $patch;

                $result->log( 'Run patch: ' . $patch, Result::LOG_INFO );

                $patchContent = file_get_contents(
                    $this->getDirectory() . DIRECTORY_SEPARATOR . $patch
                );

                foreach ( $patchRecord['schemas'] as $schema )
                {
                    $result->schemaList[] = $schema;
                    $result->schema       = $schema;
                    $schemaQuoted         = $plf->quoteIdentifier( $schema );

                    $sql( 'SET search_path TO ' . $schemaQuoted . ', "_common"' );
                    $sql( $patchContent );

                    // Update schemas patch table
                    $db->query( 'INSERT INTO "patch" ( "patch", "date", "number", "name" ) VALUES ( ?, ?, ?, ? )' )
                       ->execute( array( $patch, $patchRecord['date'], $patchRecord['number'], $patchRecord['name'] ) );
                }
            }

            $result->log( 'Commit transaction', Result::LOG_INFO );
            $result->log( 'COMMIT', Result::LOG_SQL );

            $conn->commit();

            $result->status     = Result::STATUS_SUCCESS;
            $result->schemaList = array_unique( $result->schemaList );
        }
        catch ( \Exception $ex )
        {
            $result->log( (string) $ex, Result::LOG_ERROR );

            $result->log( 'Rollback transaction', Result::LOG_INFO );
            $result->log( 'ROLLBACK', Result::LOG_SQL );

            $conn->rollBack();

            $result->status = Result::STATUS_ERROR;
            $result->error  = $ex->getMessage();
        }

        return $result;
    }

    /**
     * Execute the patching process by given options
     *
     * @return   Method returns a record array about process.
     */
    public function execute()
    {
        $patchList  = $this->getPatchList();
        $schemaList = $this->getSchemaList();

        list( $patchIndex, $allSchemas ) = $this->evaluatePatches(
            $patchList, $schemaList
        );

        return $this->executePatchTransaction( $this->filterPatches(
            $patchIndex, $this->getContentOfSchemaPatchTables( $allSchemas )
        ) );
    }
}
