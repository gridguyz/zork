<?php

namespace Zork\Mvc\Controller;

use Zend\Mvc\MvcEvent;
use Zend\Navigation\Navigation;
use Zend\Stdlib\ResponseInterface;
use Zend\Authentication\AuthenticationService;

/**
 * Zork\Mvc\Controller\AbstractAdminController
 *
 * @author Sipi
 */
abstract class AbstractAdminController extends AbstractInjectorController
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
