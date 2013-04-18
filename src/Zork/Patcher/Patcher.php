<?php

namespace Zork\Patcher;

use Exception;
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
    const PATCH_PATTERN = '/^(?P<type>data|schema)\.(?P<from>\d(\.\d){0,2})-(?P<to>\d(\.\d){0,2})\.sql$/';

    /**
     * @var array
     */
    protected $dbConfig;

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
     * @param   array|\Zend\Db\Adapter\Adapter  $db
     * @throws  InvalidArgumentException
     */
    public function __construct( $db )
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
     * Patch with sql-files under a path
     *
     * @param   string      $path
     * @param   string|null $toVersion
     * @return  void
     */
    public function patch( $path, $toVersion = null )
    {
        if ( ! is_dir( $path ) )
        {
            return;
        }

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

        $connection = $this->getDbAdapter()
                           ->getDriver()
                           ->getConnection();

        try
        {
            $connection->beginTransaction();

            foreach ( $iterator as $section => $pathname )
            {
                $this->patchSection( $pathname, $section, $toVersion );
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
            if ( false )
            {
                // do loop with every site's schema
            }
            else
            {
                $this->patchSchema( $path . '/site', $section, null, $toVersion );
            }
        }
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
            $db         = $this->getDbAdapter();
            $platform   = $db->getPlatform();
            $prefix     = $schema ? $platform->quoteIdentifier( $schema ) : '';

            if ( $prefix )
            {
                $prefix .= '.';
            }

            $db->query( 'CREATE TABLE IF NOT EXISTS ' . $prefix . '"patch" (
                             "id"        SERIAL              PRIMARY KEY,
                             "section"   CHARACTER VARYING   NOT NULL        UNIQUE,
                             "version"   CHARACTER VARYING   NOT NULL
                         )' )
               ->execute();

            $query = $db->query( 'SELECT * FROM ' . $prefix . '."patch"' )
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
            $dir = version_compare( $patch['from'], $patch['to'] );

            if ( $patch['from'] == $fromVersion && $dir === $direction &&
                 ( ( $direction > 0 && ( ! $extrema || ( version_compare( $patch['to'], $extrema, '>' ) && ( ! $toVersion || version_compare( $patch['to'], $toVersion, '<=' ) ) ) ) ) ||
                   ( $direction < 0 && ( ! $extrema || ( version_compare( $patch['to'], $extrema, '<' ) && ( ! $toVersion || version_compare( $patch['to'], $toVersion, '>=' ) ) ) ) ) ) )
            {
                $extrema = $patch['to'];
            }
        }

        return $extrema;
    }

}
