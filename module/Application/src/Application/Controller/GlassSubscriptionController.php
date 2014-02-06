<?php

namespace Application\Controller;

use Phass\Controller\AbstractSubscriptionController;
use Phass\Entity\Timeline\MenuItem;

class GlassSubscriptionController extends AbstractSubscriptionController
{
    use \Application\Log\LoggerTrait;
    
    public function onDeleteAction()
    {
        $item = $this->getServiceLocator()->get('Phass\Timeline\Item');
        
        $menuItem = clone $this->getServiceLocator()->get('Phass\Timeline\MenuItem');
         
        $menuItem->setId('how_rude')
                 ->setAction(MenuItem::DELETE);
         
        $item->getMenuItems()->append($menuItem);
        
        $item->setText("How Rude!")
             ->setDefaultNotification()
             ->insert();
    }
}