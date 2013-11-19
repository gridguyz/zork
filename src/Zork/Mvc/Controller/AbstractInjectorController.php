<?php

namespace Zork\Mvc\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Zork\Mvc\Controller\AbstractInjectorController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractInjectorController extends AbstractActionController
{

    /**
     * Inject (& filter) components in navigaton data
     *
     * @param   array                               $pages
     * @param   \Zend\Mvc\Router\RouteMatch         $routeMatch
     * @param   \Zend\Mvc\Router\RouterInterface    $router
     * @param   string                              $locale
     * @param   \Zend\I18n\Translator\Translator    $translator
     * @param   \Grid\User\Model\Permissions\Model  $permissions
     * @return  array
     */
    protected function injectComponents( & $pages,
                                         $routeMatch,
                                         $router,
                                         $locale,
                                         $translator    = null,
                                         $permissions   = null )
    {
        $serviceLocator = $this->getServiceLocator();

        if ( null === $translator )
        {
            $translator = $serviceLocator->get( 'translator' );
        }

        if ( null === $permissions )
        {
            $permissions = $serviceLocator->get( 'Grid\User\Model\Permissions\Model' );
        }

        foreach ( $pages as $key => & $page )
        {
            if ( isset( $page['resource'] ) )
            {
                $privilege = isset( $page['privilege'] )
                    ? $page['privilege']
                    : null;

                if ( ! $permissions->isAllowed( $page['resource'],
                                                $privilege ) )
                {
                    unset( $pages[$key] );
                    continue;
                }
            }

            if ( isset( $page['dependencies'] ) )
            {
                foreach ( (array) $page['dependencies'] as $serviceName => $dependency )
                {
                    if ( isset( $dependency['service'] ) )
                    {
                        $serviceName = (string) $dependency['service'];
                    }

                    $service = $this->getServiceLocator()
                                    ->get( $serviceName );

                    if ( isset( $dependency['method'] ) )
                    {
                        $method = array( $service, $dependency['method'] );
                    }
                    else
                    {
                        $method = $service;
                    }

                    if ( isset( $dependency['arguments'] ) )
                    {
                        $args = (array) $dependency['arguments'];
                    }
                    else
                    {
                        $args = array();
                    }

                    $result = call_user_func_array( $method, $args );

                    if ( isset( $dependency['result'] ) )
                    {
                        $enabled = $result == $dependency['result'];
                    }
                    else
                    {
                        $enabled = (bool) $result;
                    }

                    if ( ! $enabled )
                    {
                        unset( $pages[$key] );
                        continue 2;
                    }
                }
            }

            if ( isset( $page['pages'] ) )
            {
                $page['pages'] = $this->injectComponents(
                    $page['pages'],
                    $routeMatch,
                    $router,
                    $locale,
                    $translator,
                    $permissions
                );
            }

            if ( ! empty( $page['parentOnly'] ) && empty( $page['pages'] ) )
            {
                unset( $pages[$key] );
                continue;
            }

            $page['label'] = $translator->translate(
                $page['label'],
                $page['textDomain']
            );

            if ( isset( $page['title'] ) )
            {
                $page['title'] = $translator->translate(
                    $page['title'],
                    $page['textDomain']
                );
            }

            if ( isset( $page['action'] ) ||
                 isset( $page['controller'] ) ||
                 isset( $page['route'] ) )
            {
                if ( ! isset( $page['routeMatch'] ) && $routeMatch )
                {
                    $page['routeMatch'] = $routeMatch;
                }

                if ( ! isset( $page['router'] ) )
                {
                    $page['router'] = $router;
                }

                $page = $page + array( 'params' => array() );
                $page['params'] = $page['params'] + array( 'locale' => $locale );
            }
            elseif ( isset( $page['uri'] ) )
            {
                $page['uri'] = str_replace( '%locale%', $locale, $page['uri'] );
            }
        }

        return $pages;
    }

}
