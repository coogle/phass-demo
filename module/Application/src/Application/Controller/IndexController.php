<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;

class IndexController extends AbstractController
{
    use \Application\Log\LoggerTrait;
    
    public function indexAction()
    {
        $client = $this->getServiceLocator()->get('Phass\Service\GlassService');
        
        $timelineItems = $client->execute('timeline::list');
        
        return new ViewModel(compact('timelineItems'));
    }
}
