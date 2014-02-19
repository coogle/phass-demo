<?php

namespace Application\Controller;

use Phass\Controller\AbstractSubscriptionController;
use Phass\Entity\Timeline\MenuItem;

class GlassSubscriptionController extends AbstractSubscriptionController
{
    use \Application\Log\LoggerTrait;
    
    public function onDeleteAction()
    {
        $this->logEvent("Got onDelete Action");
        
        $item = $this->getServiceLocator()->get('Phass\Timeline\Item');
        
        $menuItem = clone $this->getServiceLocator()->get('Phass\Timeline\MenuItem');
         
        $menuItem->setId('how_rude')
                 ->setAction(MenuItem::DELETE);
         
        $item->getMenuItems()->append($menuItem);
        
        /*
        $item->setText("How Rude!")
             ->setDefaultNotification()
             ->insert();
        */
    }
    
    public function onShareAction()
    {
        try {
            $this->logEvent("ONSHARE", 'DEBUG');
            $item = $this->getNotificationTimelineItem();
            
            $attachment = $item->getAttachment();
            
            if(!$attachment instanceof \Phass\Entity\Timeline\Attachment) {
                $this->logEvent(get_class($attachment), "WARN");
                $this->logEvent("Was shared something but no attachments", "WARN");
                return;
            }
            
            if($attachment->getMimeType() != 'image/jpeg') {
                $this->logEvent("Skipping attachment as it wasn't an image/jpeg", "DEBUG");
                return;
            }
            
            $this->logEvent("Downloading Image from Share", "DBEUG");
            $imageContent = $attachment->downloadContent()->getContent();
            
            $this->logEvent("Image downloaded and is " . strlen($imageContent) . " bytes long");
            
            
            $item = $this->getServiceLocator()->get('Phass\Timeline\Item');
            $item->setDefaultNotification();
             
            $menuItem = clone $this->getServiceLocator()->get('Phass\Timeline\MenuItem');
             
            $menuItem->setId($this->getServiceLocator()->get('Phass\Service\GlassService')->generateGuid())
                     ->setAction(MenuItem::DELETE);
             
            $item->getMenuItems()->append($menuItem);
            
            $memeGenerator = $this->getServiceLocator()->get('Meme\Generator');
            
            $newImage = $memeGenerator->createFromImageString($imageContent, "This is a test");
            
            $attachment = $this->getServiceLocator()->get('Phass\Timeline\Attachment');
            
            $attachment->setContent($newImage);
            $attachment->setMimeType('image/jpeg');
            
            $item->getAttachments()->append($attachment);
            $item->insert();
            
        } catch(\Exception $e) {
            $this->logEvent("Exception Caught: {$e->getMessage()}", "ERR");
        }
    } 
}