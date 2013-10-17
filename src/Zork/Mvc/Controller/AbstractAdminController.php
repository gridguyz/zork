<?php

namespace Zork\Mvc\Controller;

use Zend\Mvc\MvcEvent;
use Zend\Navigation\Navigation;
use Zend\Stdlib\ResponseInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Zork\Mvc\Controller\AbstractAdminController
 *
 * @author Sipi
 */
abstract class AbstractAdminController extends AbstractActionController
{

    /**
     * @var string
     */
    const NOT_LOGGED_REDIRECT_URL       = 'Grid\User\Authentication\Login';

    /**
     * @var string
     */
    const NOT_ALLOWED_REDIRECT_URL      = 'Grid\Core\Admin\NotAllowed';

    /**
     * @var array
     */
    const ALLOWED_NOT_LOGGED_ACTIONS    = 'not-allowed';

    /**
     * @var array
     * @abstract
     */
    protected $allowedNotLoggedActions  = array(
        self::ALLOWED_NOT_LOGGED_ACTIONS
    );

    /**
     * @var array
     * @abstract
     */
    protected $disableLayoutActions     = array();

    /**
     * @var string
     * @abstract
     */
    protected $notLoggedRedirectUrl     = self::NOT_LOGGED_REDIRECT_URL;

    /**
     * @var string
     * @abstract
     */
    protected $notAllowedRedirectUrl    = self::NOT_ALLOWED_REDIRECT_URL;

    /**
     * @var array
     * @abstract
     */
    protected $aclRights               = array();

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authenticationService    = null;

    /**
     * @var \User\Model\Permissions\Model
     */
    protected $permissionsModel         = null;

    /**
     * @var string
     */
    protected $adminLocale              = null;

    /**
     * @param array $pages
     * @param Router\RouteMatch $routeMatch
     * @param Router\RouterInterface $router
     * @param string $locale
     * @param \User\Model\Permissions\Model $permissions
     * @return array
     */
    protected function injectComponents( & $pages,
                                         $routeMatch,
                                         $router,
                                         $locale,
                                         $translator,
                                         $permissions )
    {
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

    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws \Zend\Mvc\Exception\DomainException
     */
    public function onDispatch( MvcEvent $event )
    {
        if ( isset( $this->aclRights['']['admin'] ) &&
             ! is_array( $this->aclRights['']['admin'] ) )
        {
            $this->aclRights['']['admin'] = (array) $this->aclRights['']['admin'];
        }

        $this->aclRights['']['admin'][] = 'ui';

        $redirect = $this->redirectWhenNotAllowed( $event );

        if ( $redirect instanceof ResponseInterface )
        {
            return $redirect;
        }

        // Navigation
        $routeMatch = $event->getRouteMatch();
        $layout     = $this->plugin( 'layout' );
        $pages      = $this->getServiceLocator()
                           ->get( 'Configuration' )
                                [ 'modules'       ]
                                [ 'Grid\Core'     ]
                                [ 'navigation'    ];

        if ( ! $this->isLayoutDisabled( $event ) )
        {
            $layout->setMiddleLayout( 'layout/middle/admin', array(
                'request'           => $this->getRequest(),
                'action'            => $routeMatch->getParam( 'action', 'not-found' ),
                'controller'        => $routeMatch->getParam( 'controller', 'NotFound' ),
                'hasIdentity'       => $this->getAuthenticationService()
                                            ->hasIdentity(),
                'navigationPages'   => new Navigation(
                    $this->injectComponents(
                        $pages,
                        $routeMatch,
                        $event->getRouter(),
                        (string) $this->locale(),
                        $this->getServiceLocator()
                             ->get( 'translator' ),
                        $this->getPermissionsModel()
                    )
                ),
                'adminLocaleForm'   => $this->getServiceLocator()
                                            ->get( 'Form' )
                                            ->create( 'Grid\Core\AdminLocale', array(
                                                'adminLocale' => $this->getAdminLocale()
                                            ) )
            ) );
        }

        return parent::onDispatch( $event );
    }

    /**
     * @return string
     */
    public function getAdminLocale()
    {
        return $this->getServiceLocator()
                    ->get( 'AdminLocale' )
                    ->getCurrent();
    }

    /**
     * @param string $locale
     * @return \Zork\Mvc\Controller\AbstractAdminController
     */
    public function setAdminLocale( $locale )
    {
        $this->getServiceLocator()
             ->get( 'AdminLocale' )
             ->setCurrent( $locale );

        return $this;
    }

    /**
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthenticationService()
    {
        if ( null === $this->authenticationService )
        {
            $this->authenticationService = $this->getServiceLocator()
                                                ->get( 'Zend\Authentication\AuthenticationService' );
        }

        return $this->authenticationService;
    }

    /**
     * @param \Zend\Authentication\AuthenticationService $service
     * @return \Zork\Mvc\Controller\AbstractAdminController
     */
    public function setAuthenticationService( AuthenticationService $service = null )
    {
        $this->authenticationService = $service;
        return $this;
    }

    /**
     * @return \User\Model\Permissions\Model
     */
    public function getPermissionsModel()
    {
        if ( null === $this->permissionsModel )
        {
            $this->permissionsModel = $this->getServiceLocator()
                                           ->get( 'Grid\User\Model\Permissions\Model' );
        }

        return $this->permissionsModel;
    }

    /**
     * @param string $action
     * @return bool|string
     */
    protected function checkActionRights( $action )
    {
        $permissionModel = $this->getPermissionsModel();

        if ( ! empty( $this->aclRights[$action] ) )
        {
            foreach ( $this->aclRights[$action] as $resource => $privileges )
            {
                if ( empty( $resource ) )
                {
                    $resource = null;
                }

                foreach ( (array) $privileges as $privilege )
                {
                    if ( empty( $privilege ) )
                    {
                        $privilege = null;
                    }

                    if ( ! $permissionModel->isAllowed( $resource, $privilege ) )
                    {
                        return $this->notAllowedRedirectUrl;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param \Zend\Mvc\MvcEvent $event
     * @return null|\Zend\Http\Response
     */
    protected function redirectWhenNotAllowed( MvcEvent $event )
    {
        $action = $event->getRouteMatch()
                        ->getParam( 'action', 'not-found' );

        if ( ! in_array( $action, $this->allowedNotLoggedActions ) )
        {
            $redirect = false;

            if ( $this->getAuthenticationService()->hasIdentity() )
            {
                $redirect = $this->checkActionRights( '' );

                if ( $redirect === false )
                {
                    $redirect = $this->checkActionRights( $action );
                }
            }
            else
            {
                $redirect = $this->notLoggedRedirectUrl;
            }

            if ( $redirect !== false )
            {
                return $this->redirect()
                            ->toRoute( $redirect, array(
                                'locale' => (string) $this->locale(),
                            ) );
            }
        }

        return null;
    }

    /**
     * @param \Zend\Mvc\MvcEvent $event
     * @return bool
     */
    public function isLayoutDisabled( MvcEvent $event )
    {
        $action = $event->getRouteMatch()
                        ->getParam( 'action', 'not-found' );

        return ! empty( $this->disableLayoutActions[$action] );
    }

}
