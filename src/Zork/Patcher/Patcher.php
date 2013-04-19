<?php

namespace Zork\Patcher;

use Exception;
use Traversable;
use LogicException;
use IteratorAggregate;
use FilesystemIterator;
use CallbackFilterIterator;
use InvalidArgumentException;
use Zend\Db\Adapter\Adapter as DbAdapter;

/**
 * Patch
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Patcher
{

    /**
     * @const string
     */
    const PATCH_PATTERN = '/^(?P<type>data|schema)\.(?P<from>\d+(\.((dev|a|b|rc)\d*|\d+))*-(?P<to>\d+(\.((dev|a|b|rc)\d*|\d+))*?)\.sql$/';

    /**
     * @var array
     */
    protected $dbConfig;

    /**
     * Is current db a multisite?
     *
     * @var bool
     */
    private $isMultisite;

    /**
     * Schemas' cache
     *
     * @var array
     */
    private $schemaCache = array();

    /**
     * Versions' cache
     *
     * @var array
     */
    private $versionCache = array();

    /**
     * Get the db-config
     *
     * @return  array
     */
    public function getDbConfig()
    {
        return $this->dbConfig;
    }

    /**
     * Set the db-config
     *
     * @return  Patcher
     */
    public function setDbConfig( array $dbConfig = null )
    {
        $this->dbConfig = $dbConfig;
        return $this;
    }

    /**
     * Get the db-adapter object
     *
     * @return \Zend\Db\Adapter\Abstract
     */
    public function getDbAdapter()
    {
        if ( null === $this->dbAdapter )
        {
            $this->dbAdapter = new DbAdapter( $this->getDbConfig() );
        }

        return $this->dbAdapter;
    }

    /**
     * Set the db-adapter object
     *
     * @param   \Zend\Db\Adapter\Adapter    $dbAdapter
     * @return  Patcher
     */
    public function setDbAdapter( DbAdapter $dbAdapter = null )
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    /**
     * Constructor
     *
     * @param   null|array|\Zend\Db\Adapter\Adapter $db
     * @throws  InvalidArgumentException
     */
    public function __construct( $db = null )
    {
        if ( $db instanceof DbAdapter )
        {
            $this->setDbAdapter( $db );
        }
        else if ( is_array( $db ) )
        {
            $this->setDbConfig( $db );
        }
        else if ( ! empty( $db ) )
        {
            throw new InvalidArgumentException( sprintf(
                '%s: $db must be a Zend\Db\Adapter\Adapter instance,' .
                ' or a db-config array, "%s" given.',
                __METHOD__,
                is_object( $db ) ? get_class( $db ) : gettype( $db )
            ) );
        }
    }

    /**
     * Patch with sql-files under multiple paths
     *
     * @param   string|array|\Traversable   $paths
     * @param   string|null                 $toVersion
     * @return  void
     */
    public function patch( $paths, $toVersion = null )
    {
        if ( ! $paths instanceof Traversable && ! is_array( $paths ) )
        {
            $paths = array( (string) $paths );
        }

        $filter = function ( $path ) {
            return $path && is_dir( $path ) && is_readable( $path );
        };

        if ( is_array( $paths ) )
        {
            $paths = array_filter( $paths, $filter );

            if ( empty( $paths ) )
            {
                return;
            }
        }
        elseif ( $paths instanceof Traversable )
        {
            if ( $paths instanceof IteratorAggregate )
            {
                $paths = $paths->getIterator();
            }

            $paths = new CallbackFilterIterator( $paths, $filter );
        }

        $connection = $this->getDbAdapter()
                           ->getDriver()
                           ->getConnection();

        try
        {
            $connection->beginTransaction();

            foreach ( $paths as $path )
            {
                $iterator = new CallbackFilterIterator(
                    new FilesystemIterator(
                        $path,
                        FilesystemIterator::CURRENT_AS_PATHNAME |
                        FilesystemIterator::KEY_AS_FILENAME |
                        FilesystemIterator::SKIP_DOTS |
                        FilesystemIterator::UNIX_PATHS
                    ),
                    function ( $current, $key, $iterator ) {
                        return $iterator->isDir() && '.' !== $key[0];
                    }
                );

                foreach ( $iterator as $section => $pathname )
                {
                    $this->patchSection( $pathname, $section, $toVersion );
                }
            }

            $connection->commit();
        }
        catch ( Exception $exception )
        {
            $connection->rollback();
            throw $exception;
        }
    }

    /**
     * Patch within a section
     *
     * @param   string      $path
     * @param   string      $section
     * @param   string|null $toVersion
     * @return  void
     */
    protected function patchSection( $path, $section, $toVersion = null )
    {
        if ( is_dir( $path . '/common' ) )
        {
            $this->patchSchema( $path . '/common', $section, '_common', $toVersion );
        }

        if ( is_dir( $path . '/central' ) )
        {
            $this->patchSchema( $path . '/central', $section, '_central', $toVersion );
        }

        if ( is_dir( $path . '/site' ) )
        {
            if ( $this->isMultisite() )
            {
                foreach ( $this->getSiteSchemas() as $schema )
                {
                    $this->patchSchema( $path . '/site', $section, $schema, $toVersion );
                }
            }
            else
            {
                $this->patchSchema( $path . '/site', $section, null, $toVersion );
            }
        }
    }

    /**
     * Is current db a multisite?
     *
     * @return  bool
     */
    protected function isMultisite()
    {
        if ( null === $this->isMultisite )
        {
            $this->isMultisite = false;

            $db     = $this->getDbAdapter();
            $query  = $db->query( 'SELECT TRUE AS "exists"
                                     FROM information_schema.tables
                                    WHERE table_schema  = :schema
                                      AND table_name    = :table' )
                         ->execute( array(
                             'schema'   => '_central',
                             'table'    => 'site',
                         ) );

            foreach ( $query as $row )
            {
                $this->isMultisite = $this->isMultisite || $row['exists'];
            }
        }

        return $this->isMultisite;
    }

    /**
     * Get site schemas
     *
     * @return  array|null
     */
    protected function getSiteSchemas()
    {
        if ( $this->isMultisite() )
        {
            if ( null === $this->schemaCache )
            {
                $db     = $this->getDbAdapter();
                $query  = $db->query( 'SELECT "schema" FROM "_central"."site"' )
                             ->execute();

                $this->schemaCache = array();

                foreach ( $query as $row )
                {
                    $schema = $row['schema'];
                    $this->schemaCache[$schema] = $schema;
                }
            }

            return $this->schemaCache;
        }

        return null;
    }

    /**
     * Patch a single schema
     *
     * @param   string      $path
     * @param   string      $section
     * @param   string|null $schema
     * @param   string|null $toVersion
     * @return  void
     */
    protected function patchSchema( $path, $section, $schema = null, $toVersion = null )
    {
        $db         = $this->getDbAdapter();
        $connection = $db->getDriver()
                         ->getConnection();

        if ( null !== $schema )
        {
            $oldSchema = $connection->setCurrentSchema( $schema );
        }

        $fromVersion = $this->getVersion( $section, $schema );

        if ( null === $toVersion || ! $fromVersion )
        {
            $direction = 1;
        }
        else if ( ! $toVersion )
        {
            $direction = -1;
        }
        else
        {
            $direction = version_compare( $toVersion, $fromVersion );
        }

        if ( $direction !== 0 )
        {
            $info = $this->getPatchInfo( $path );
            $prev = $next = $fromVersion;

            while ( true )
            {
                $prev = $next;
                $next = $this->getNextVersion( $info, $direction, $prev, $toVersion );

                if ( ! $next )
                {
                    break;
                }

                foreach ( $info as $patch )
                {
                    if ( $patch['from'] == $prev && $patch['to'] == $next )
                    {
                        $connection->getResource()
                                   ->exec( file_get_contents( $patch['path'] ) );
                    }
                }
            }

            if ( null !== $toVersion && $prev !== $toVersion )
            {
                throw new LogicException( sprintf(
                    '%s: section "%s" cannot be patched to version "%s", step from "%s" is missing',
                    __METHOD__,
                    $section,
                    $toVersion,
                    $prev
                ) );
            }

            $this->setVersion( $section, $prev, $schema );
        }

        if ( null !== $schema )
        {
            $connection->setCurrentSchema( $oldSchema );
        }
    }

    /**
     * Get version of section (in a schema)
     *
     * @param   string      $section
     * @param   string|null $schema
     * @return  string
     */
    protected function getVersion( $section, $schema = null )
    {
        if ( ! isset( $this->versionCache[$schema] ) )
        {
            $exists     = false;
            $db         = $this->getDbAdapter();
            $platform   = $db->getPlatform();
            $quoted     = '';

            if ( $schema )
            {
                $quoted = $platform->quoteIdentifier( $schema );
                $query  = $db->query( 'SELECT TRUE AS "exists"
                                         FROM information_schema.schemata
                                        WHERE schema_name = :schema' )
                             ->execute( array(
                                 'schema' => $schema,
                             ) );

                foreach ( $query as $row )
                {
                    $exists = $exists || $row['exists'];
                }

                if ( ! $exists )
                {
                    $db->query( 'CREATE SCHEMA ' . $quoted );
                }

                $quoted .= '.';
            }

            $db->query( 'CREATE TABLE IF NOT EXISTS ' . $quoted . '"patch" (
                             "id"        SERIAL              PRIMARY KEY,
                             "section"   CHARACTER VARYING   NOT NULL        UNIQUE,
                             "version"   CHARACTER VARYING   NOT NULL
                         )' )
               ->execute();

            $query = $db->query( 'SELECT * FROM ' . $quoted . '."patch"' )
                        ->execute();

            foreach ( $query as $row )
            {
                $this->versionCache[$schema][$row['section']] = $row['version'];
            }
        }

        if ( empty( $this->versionCache[$schema][$section] ) )
        {
            return 0;
        }

        return $this->versionCache[$schema][$section];
    }

    /**
     * Set version of section (in a schema)
     *
     * @param   string      $section
     * @param   string      $newVersion
     * @param   string|null $schema
     * @return  \Zork\Patcher\Patcher
     */
    protected function setVersion( $section, $newVersion, $schema = null )
    {
        $oldVersion = $this->getVersion( $section, $schema );

        if ( $oldVersion !== $newVersion )
        {
            $db         = $this->getDbAdapter();
            $platform   = $db->getPlatform();
            $prefix     = $schema ? $platform->quoteIdentifier( $schema ) : '';

            if ( $oldVersion )
            {
                $query = $db->query( '
                    UPDATE ' . $prefix . '."patch"
                       SET "version" = :version
                     WHERE "section" = :section
                ' );

            }
            else
            {
                $query = $db->query( '
                    INSERT INTO ' . $prefix . '."patch" ( "section", "version" )
                         VALUES ( :section, :version )
                ' );
            }

            $query->execute( array(
                'version' => $newVersion,
                'section' => $section,
            ) );

            $this->versionCache[$schema][$section] = $newVersion;
        }

        return $this;
    }

    /**
     * Get patch info
     *
     * @param   string $path
     * @return  array
     */
    protected function getPatchInfo( $path )
    {
        $iterator = new FilesystemIterator(
            $path,
            FilesystemIterator::CURRENT_AS_PATHNAME |
            FilesystemIterator::KEY_AS_FILENAME |
            FilesystemIterator::SKIP_DOTS |
            FilesystemIterator::UNIX_PATHS
        );

        $data   = array();
        $schema = array();

        foreach ( $iterator as $name => $pathname )
        {
            $matches = array();

            if ( preg_match( static::PATCH_PATTERN, $name, $matches ) )
            {
                $store = array(
                    'name'  => $name,
                    'path'  => $pathname,
                    'type'  => $matches['type'],
                    'from'  => $matches['from'],
                    'to'    => $matches['to'],
                );

                switch ( $matches['type'] )
                {
                    case 'data':    $data[]   = $store; break;
                    case 'schema':  $schema[] = $store; break;
                }
            }
        }

        return array_merge( $schema, $data );
    }

    /**
     * Get next version
     *
     * @param   array   $info
     * @param   int     $direction
     * @param   string  $fromVersion
     * @param   string  $toVersion
     * @return  string
     */
    protected function getNextVersion( $info, $direction, $fromVersion, $toVersion )
    {
        $extrema = null;

        foreach ( $info as $patch )
        {
            $dir = version_compare( $patch['to'], $patch['from'] );

            if ( $patch['from'] == $fromVersion && $dir === $direction &&
                // ( is upgrade     && ( no max yet || ( current is greater than max                    && ( no upper bound || current is under the upper bound                ) ) ) )
                 ( ( $direction > 0 && ( ! $extrema || ( version_compare( $patch['to'], $extrema, '>' ) && ( ! $toVersion || version_compare( $patch['to'], $toVersion, '<=' ) ) ) ) ) ||
                // ( is downgrade   && ( no min yet || ( current is lesser than min                     && ( no lower bound || current is above the lower bound                ) ) ) )
                   ( $direction < 0 && ( ! $extrema || ( version_compare( $patch['to'], $extrema, '<' ) && ( ! $toVersion || version_compare( $patch['to'], $toVersion, '>=' ) ) ) ) ) ) )
            {
                $extrema = $patch['to'];
            }
        }

        return $extrema;
    }

}
