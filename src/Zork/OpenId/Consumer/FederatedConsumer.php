<?php

namespace Zork\OpenId\Consumer;

use ZendOpenId\Consumer\GenericConsumer;

/**
 * Patch for ZendOpenId\Consumer\GenericConsumer to work with Google federated login
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 *
 * @codeCoverageIgnore
 */
class FederatedConsumer extends GenericConsumer
{

    /**
     * Performs discovery of identity and finds OpenID URL, OpenID server URL
     * and OpenID protocol version. Returns true on succees and false on
     * failure.
     *
     * @param string &$id OpenID identity URL
     * @param string &$server OpenID server URL
     * @param float &$version OpenID protocol version
     * @return bool
     * @todo OpenID 2.0 (7.3) XRI and Yadis discovery
     */
    protected function _discovery( &$id, &$server, &$version )
    {
        $realId = $id;

        if ( $this->_storage
                  ->getDiscoveryInfo( $id, $realId, $server,
                                      $version, $expire ) )
        {
            $id = $realId;
            return true;
        }

        $response = $this->_httpRequest( $id, 'GET', array(), $status );

        if ( $status != 200 || ! is_string( $response ) )
        {
            return false;
        }

        if ( preg_match( '/<link[^>]*rel=(["\'])[ \t]*(?:[^ \t"\']+[ \t]+)*?'
                         . 'openid2.provider[ \t]*[^"\']*\\1[^>]*'
                         . 'href=(["\'])([^"\']+)\\2[^>]*\/?>/i',
                         $response, $r ) )
        {
            $version = 2.0;
            $server = $r[3];
        }
        else if ( preg_match( '/<link[^>]*href=(["\'])([^"\']+)\\1[^>]*'
                              . 'rel=(["\'])[ \t]*(?:[^ \t"\']+[ \t]+)*?'
                              . 'openid2.provider[ \t]*[^"\']*\\3[^>]*\/?>/i',
                              $response, $r ) )
        {
            $version = 2.0;
            $server = $r[2];
        }
        else if ( preg_match( '/<link[^>]*rel=(["\'])[ \t]*(?:[^ \t"\']+[ \t]+)*'
                              . '?openid.server[ \t]*[^"\']*\\1[^>]*'
                              . 'href=(["\'])([^"\']+)\\2[^>]*\/?>/i',
                              $response, $r ) )
        {
            $version = 1.1;
            $server = $r[3];
        }
        else if ( preg_match( '/<link[^>]*href=(["\'])([^"\']+)\\1[^>]*'
                              . 'rel=(["\'])[ \t]*(?:[^ \t"\']+[ \t]+)*?'
                              . 'openid.server[ \t]*[^"\']*\\3[^>]*\/?>/i',
                              $response, $r ) )
        {
            $version = 1.1;
            $server = $r[2];
        }
        else if ( preg_match( '/<URI>([^<]+)<\/URI>/i', $response, $r ) )
        {
            $version = 2.0;
            $server = $r[1];
        }
        else
        {
            return false;
        }

        if ( $version >= 2.0 )
        {
            if ( preg_match( '/<link[^>]*rel=(["\'])[ \t]*(?:[^ \t"\']+[ \t]+)*?'
                             . 'openid2.local_id[ \t]*[^"\']*\\1[^>]*'
                             . 'href=(["\'])([^"\']+)\\2[^>]*\/?>/i',
                             $response, $r ) )
            {
                $realId = $r[3];
            }
            else if ( preg_match( '/<link[^>]*href=(["\'])([^"\']+)\\1[^>]*'
                                  . 'rel=(["\'])[ \t]*(?:[^ \t"\']+[ \t]+)*?'
                                  . 'openid2.local_id[ \t]*[^"\']*\\3[^>]*\/?>/i',
                                  $response, $r ) )
            {
                $realId = $r[2];
            }
        }
        else
        {
            if ( preg_match( '/<link[^>]*rel=(["\'])[ \t]*(?:[^ \t"\']+[ \t]+)*?'
                             . 'openid.delegate[ \t]*[^"\']*\\1[^>]*'
                             . 'href=(["\'])([^"\']+)\\2[^>]*\/?>/i',
                             $response, $r ) )
            {
                $realId = $r[3];
            }
            else if ( preg_match( '/<link[^>]*href=(["\'])([^"\']+)\\1[^>]*'
                                  . 'rel=(["\'])[ \t]*(?:[^ \t"\']+[ \t]+)*?'
                                  . 'openid.delegate[ \t]*[^"\']*\\3[^>]*\/?>/i',
                                  $response, $r ) )
            {
                $realId = $r[2];
            }
        }

        $expire = time() + 60 * 60;

        $this->_storage
             ->addDiscoveryInfo( $id, $realId, $server,
                                 $version, $expire );

        $id = $realId;
        return true;
    }

}
