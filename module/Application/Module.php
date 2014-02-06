<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\SharedEventManager;
use Phass\PhassEvents as Events;
use Phass\Service\GlassService;
use Application\Db\Entity\Credential;
use OAuth2\OAuth2Events;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $application = $e->getApplication();
        
        $config = $application->getServiceManager()->get('Config');
        
        date_default_timezone_set($config['app']["timezone"]);

        $sharedEventManager = $eventManager->getSharedManager();
        
        /**
         * We attach to the Resolver user event here, which is used to resolve a user token (which should have been saved
         * when we called GlassService::subscribe()) to a real user and OAuth2 Token object. This is neccessary to restore
         * context during a notification callback so that we can do things like insert timeline items when we get a
         * subscription ping.
         */
        $sharedEventManager->attach(GlassService::EVENT_IDENTIFIER, Events::EVENT_SUBSCRIPTION_RESOLVE_USER, function($event) use ($application) {
            $userToken = $event->getParam('userToken', null);
            $tokenType = $event->getParam('tokenType', null);
            
            if(is_null($userToken) || is_null($tokenType)) {
                throw new \UnexpectedValueException("Event triggered but user token and token type not found");
            }
            
            $credentialsTable = $application->getServiceManager()->get('Application\Db\Credentials');
            
            $user = null;
            
            switch($tokenType)
            {
                case GlassService::COLLECTION_LOCATIONS:
                    $user = $credentialsTable->findByLocationGuid($userToken);
                    break;
                case GlassService::COLLECTION_TIMELINE:
                    $user = $credentialsTable->findByTimelineGuid($userToken);
                    break;
            }
            
            if(is_null($user)) {
                return;
            }
            
            $token = $application->getServiceManager()->get('OAuth2\Token');
            
            $token->setAccessToken($user->getAuthToken())
                  ->setRefreshToken($user->getRefreshToken());
                  
            return $token;
        });
        
        $sharedEventManager->attach(OAuth2Events::EVENT_IDENTIFIER, OAuth2Events::EVENT_NEW_AUTH_TOKEN, function($event) use ($application) {
            $token = $event->getParam('token', null);
            
            if(!$token instanceof \OAuth2\Entity\Token) {
                throw new \UnexpectedValueException("Could not retrieve token for new auth token event");
            }

            $uniqueId = $token->getJwt()->getUniqueId();

            $credentialsTable = $application->getServiceManager()->get('Application\Db\Credentials');
            
            $cred = $credentialsTable->findByUserId($uniqueId);
            
            if(!$cred) {
                $cred = new Credential();
                $cred->setUserId($uniqueId);
            }
            
            $cred->setRefreshToken($token->getRefreshToken())
                 ->setAuthToken($token->getAccessToken());
            
            $credentialsTable->save($cred);
        });
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Db\Notifications' => 'Application\Db\Table\NotificationTable',
                'Application\Db\Credentials' => 'Application\Db\Table\CredentialsTable',
            )
        );
    }
    
    public function getControllerConfig()
    {
        return array(
            'invokables' => array(
                'Application\Controller\GlassSubscription' => 'Application\Controller\GlassSubscriptionController'
            ),
            'factories' => array(
                'Application\Controller\Index' => 'Application\Controller\IndexController',
                'Application\Controller\Subscriptions' => 'Application\Controller\SubscriptionsController',
                'Application\Controller\Timeline' => 'Application\Controller\TimelineController',
                'Application\Controller\Contact' => 'Application\Controller\ContactController'
            )
        );
    }
}
