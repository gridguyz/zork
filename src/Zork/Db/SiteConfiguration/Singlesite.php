<?php

namespace Zork\Db\SiteConfiguration;

use Zork\Db\SiteInfo;
use Zork\Db\SiteConfigurationInterface;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\ServiceManager\Exception;
use Zork\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Singlesite
 *
 * @author pozs
 */
class Singlesite implements SiteConfigurationInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * Setup services which depends on the db
     *
     * @param   \Zend\Db\Adapter\Adapter $db
     * @return  \Zend\Db\Adapter\Adapter
     */
    public function configure( DbAdapter $db )
    {
        $sm         = $this->getServiceLocator();
        $platform   = $db->getPlatform();
        $driver     = $db->getDriver();
        $matches    = array();
        $fulldomain = isset( $_SERVER['HTTP_HOST'] )
            ? $_SERVER['HTTP_HOST']
            : $_SERVER['SERVER_NAME'];

        if ( preg_match( '/^(.*)\.([a-z0-9-]+\.[a-z0-9-]+)$/', $fulldomain, $matches ) )
        {
            $subdomain  = $matches[1];
            $domain     = $matches[2];
        }
        else
        {
            $subdomain  = '';
            $domain     = $fulldomain;
        }

        $query = $db->query( '
            SELECT *
              FROM ' . $platform->quoteIdentifier( 'subdomain' ) . '
             WHERE ' . $platform->quoteIdentifier( 'subdomain' ) . '
                 = ' . $driver->formatParameterName( 'subdomain' ) . '
        ' );

        $result = $query->execute( array(
            'subdomain' => $subdomain
        ) );

        if ( $result->getAffectedRows() > 0 )
        {
            $schema = $db->getCurrentSchema();

            foreach ( $result as $data )
            {
                $info = new SiteInfo( array(
                    'schema'        => $schema,
                    'domain'        => $domain,
                    'subdomain'     => $subdomain,
                    'subdomainId'   => $data['id'],
                    'fulldomain'    => $fulldomain,
                ) );

                $sm->setService( 'SiteInfo', $info );
                return $db;
            }
        }
        else if ( $domain )
        {
            $sm->setService(
                'RedirectToDomain',
                new RedirectionService(
                    $domain,
                    'sub-domain not found',
                    false
                )
            );
        }
        else
        {
            $config = $driver->getConnection()
                             ->getConnectionParameters();

            if ( empty( $config['defaultDomain'] ) )
            {
                throw new Exception\InvalidArgumentException(
                    'Domain not found, and default domain not set'
                );
            }
            else
            {
                $sm->setService(
                    'RedirectToDomain',
                    new RedirectionService(
                        $config['defaultDomain'],
                        'sub-domain not found',
                        false
                    )
                );
            }
        }

        return $db;
    }

}
