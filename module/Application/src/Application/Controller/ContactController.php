<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;

class ContactController extends AbstractController
{
    public function indexAction()
    {
        $client = $this->getServiceLocator()->get('Phass\Service\GlassService');
        $contacts = $client->execute('contacts::list');
        
        return new ViewModel(compact('contacts'));
    }
    
    public function deleteAction()
    {
        $id = $this->getRequest()->getQuery('id', false);
        
        if(!$id) {
            throw new \InvalidArgumentException("Invalid Input");
        }
        
        $this->getGlassService()->execute('contacts::delete', $id);
        
        return $this->redirect()->toUrl('/contacts');
    }
    
    public function insertAction()
    {
        $input = $this->getRequest()->getPost()->getArrayCopy();
        
        if(!isset($input['displayName']) || empty($input['displayName'])) {
            throw new \InvalidArgumentException("Display Name Required");
        }
        
        $input['imageUrls'] = array();
        $input['id'] = $this->getGlassService()->generateGuid();
        $input['acceptTypes'] = array();
        $input['priority'] = (int)$input['priority'];
        
        $contact = $this->getServiceLocator()->get('Phass\Contact');
        
        $contact->setId($input['id'])
                ->setDisplayName($input['displayName'])
                ->setType($input['type'])
                ->setAcceptCommands($input['acceptCommands'])
                ->setSpeakableName($input['speakableName'])
                ->setAcceptTypes($input['acceptTypes'])
                ->setImageUrls($input['imageUrls'])
                ->setPriority($input['priority']);
        
        $result = $this->getGlassService()->execute('contacts::insert', $contact);
        
        return $this->redirect()->toUrl("/contacts");
    }
}