<?php

namespace Application\Controller;


use Phass\Entity\Timeline\MenuItem;
use Phass\Entity\Timeline\Attachment;

class TimelineController extends AbstractController
{
    public function viewAction()
    {
    }
    
    public function insertAction()
    {
        
        $item = $this->getServiceLocator()->get('Phass\Timeline\Item');
        
        $request = $this->getRequest();
        $item->setTitle($request->getPost('title', null))
             ->setBundleId($request->getPost('bundleId', null))
             ->setCanonicalUrl($request->getPost('canonicalUrl', null))
             ->setHtml($request->getPost('html', null))
             ->setText($request->getPost('text', null))
             ->setBundleCover((bool)$request->getPost('isBundleCover', false))
             ->setDefaultNotification();
        
        $menuItems = $request->getPost('menuItems', array());
        
        $menuItemArray = array();
        
        foreach($menuItems as $menuItem) {
            $menuItemObj = $this->getServiceLocator()->get('Phass\Timeline\MenuItem');
            $menuItemObj->setAction($menuItem);
            
            $menuItemArray[] = clone $menuItemObj;
        }
        
        $item->setMenuItems($menuItemArray);
        
        $this->getGlassService()->execute('timeline::insert', $item);
        
        return $this->redirect()->toUrl("/");
    }
    
    protected function generateDemoItem()
    {
        $item = $this->getServiceLocator()->get('Phass\Timeline\Item');
         
        $item->setText("Hello From Phass!")
        ->setDefaultNotification();
         
        $menuItem = clone $this->getServiceLocator()->get('Phass\Timeline\MenuItem');
         
        $menuItem->setId($this->getGlassService()->generateGuid())
                 ->setAction(MenuItem::DELETE);
         
        $item->getMenuItems()->append($menuItem);
        
        return $item;
    }
    
    public function demoAction()
    {
        $type = $this->getRequest()->getQuery('type', 'simple');
        
        $item = $this->generateDemoItem();
        
        switch($type) {
            default:
            case 'simple':
                $item->insert();
                break;
            case 'image':
                
                $attachment = $this->getServiceLocator()->get('Phass\Timeline\Attachment');
                
                $imageFile = APPLICATION_ROOT . '/public/images/saturn-eclipse.jpg';
                
                $attachment->setContent(file_get_contents($imageFile));
                $attachment->setMimeType('image/jpeg');
                
                $item->getAttachments()->append($attachment);
                
                $item->insert();
                break;
            case 'video':
                
                $item->setText("Pentwater, MI Beach");
                $snapshotUrl = "http://pentwater-mears.cooglenet.com/snapshot_3gp.jpg";
                
                $attachment = $this->getServiceLocator()->get('Phass\Timeline\Attachment');
                
                $imageContent = file_get_contents($snapshotUrl);
                
                $attachment->setContent($imageContent)
                           ->setMimeType('image/jpeg');
                
                $item->getAttachments()->append($attachment);
                
                $menuItem = clone $this->getServiceLocator()->get('Phass\Timeline\MenuItem');
                
                $menuItem->setId($this->getGlassService()->generateGuid())
                         ->setAction(MenuItem::PLAY_VIDEO)
                         ->setPayload("https://s3.amazonaws.com/phass-demo-videos/kittens.3gp");
                
                $item->getMenuItems()->append($menuItem);
                
                $item->insert();
                break;
        }
        
        return $this->redirect()->toUrl("/");
    }
}