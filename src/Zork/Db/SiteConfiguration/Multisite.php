<?php

namespace Zork\Db\SiteConfiguration;

use Zork\Db\SiteInfo;
use Zork\Db\SiteConfigurationInterface;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\ServiceManager\Exception;
use Zork\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Multisite
 *
 * @author pozs
 */
class Multisite implements SiteConfigurationInterface
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
        $domain     = isset( $_SERVER['HTTP_HOST'] )
            ? $_SERVER['HTTP_HOST']
            : $_SERVER['SERVER_NAME'];

        $query = $db->query( '
            SELECT *
              FROM ' . $platform->quoteIdentifier( 'fulldomain' ) . '
             WHERE ' . $platform->quoteIdentifier( 'fulldomain' ) . '
                 = ' . $driver->formatParameterName( 'fulldomain' ) . '
        ' );

        $result = $query->execute( array(
            'fulldomain' => $domain
        ) );

        if ( $result->getAffectedRows() > 0 )
        {
            foreach ( $result as $data )
            {
                $info = new SiteInfo( $data );
                $sm->setService( 'SiteInfo', $info );

                $driver->getConnection()
                       ->setCurrentSchema( $info->getSchema() );

                return $db;
            }
        }
        else
        {
            $parts = explode( '.', $domain );

            if ( count( $parts ) > 2 )
            {
                array_shift( $parts );
                $mainDomain = implode( '.', $parts );

                $result = $query->execute( array(
                    'fulldomain' => $mainDomain
                ) );
            }
            else
            {
                $mainDomain = false;
            }

            if ( $mainDomain && $result->getAffectedRows() > 0 )
            {
                $sm->setService(
                    'RedirectToDomain',
                    new RedirectionService(
                        $mainDomain,
                        'sub-domain not found',
                        true
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
                            'domain not found',
                            false
                        )
                    );
                }
            }
        }

        return $db;
    }

}
