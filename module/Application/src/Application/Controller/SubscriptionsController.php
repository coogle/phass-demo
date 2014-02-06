<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Phass\Service\GlassService;

class SubscriptionsController extends AbstractController
{
    public function indexAction()
    {
        $subscriptions = $this->getGlassService()->execute('subscriptions::list');
        
        return new ViewModel(compact('subscriptions'));
    }
    
    public function unsubscribeAction()
    {
       $id = $this->getRequest()->getQuery('id', false);
       
       if(!$id) {
           throw new \InvalidArgumentException("You must provide an ID to delete");
       }
       
       $result = $this->getGlassService()->unsubscribe($id);
       
       return $this->redirect()->toUrl('/subscriptions');
    }
    
    public function subscribeAction()
    {
        $ops = $this->getRequest()->getQuery('op', array());
        $id = $this->getRequest()->getQuery('id', false);
        
        if(!$id) {
            throw new \InvalidArgumentException("Subscription ID required");
        }
        
        $guid = $this->getGlassService()->subscribe($id, $ops);
        
        $credentialsTable = $this->getServiceLocator()->get('Application\Db\Credentials');
        $token = $this->getServiceLocator()->get('Phass\OAuth2\Token');
        
        $creds = $credentialsTable->findByUserId($token->getJwt()->getUniqueId());
        
        switch($id) {
            case GlassService::COLLECTION_LOCATIONS:
                $creds->setLocationGuid($guid);
                break;
            case GlassService::COLLECTION_TIMELINE:
                $creds->setTimelineGuid($guid);
                break;
        }
        
        $credentialsTable->save($creds);
        
        return $this->redirect()->toUrl('/subscriptions');
    }
}