<?php

namespace Application\Db\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Application\Db\Entity\Notification;

class NotificationTable implements FactoryInterface
{
    /**
     * @var \Zend\Db\TableGateway\TableGateway
     */
    protected $_tableGateway;
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        
        $resultSetProtoType = new ResultSet();
        $resultSetProtoType->setArrayObjectPrototype(new Notification());
        
        $tg = new TableGateway('notifications', $dbAdapter, null, $resultSetProtoType);
        
        $retval = new static();
        $retval->setTableGateway($tg);
        
        return $retval;
    }
    
    /**
     * @return the $_tableGateway
     */
    public function getTableGateway() {
        return $this->_tableGateway;
    }

    /**
     * @param \Zend\Db\TableGateway\TableGateway $_tableGateway
     * @return self
     */
    public function setTableGateway($_tableGateway) {
        $this->_tableGateway = $_tableGateway;
        return $this;
    }

    public function create(array $data = array())
    {
        $retval = new Notification();
        $retval->exchangeArray($data);
        return $retval;
    }
    
    public function save(Notification $notification)
    {
        $data = $notification->toArray();
        
        if($this->findByItemIdAndUserToken($data['item_id'], $data['user_token'])) {
            return $this->getTableGateway()->update($data, array('item_id' => $data['item_id'], 'user_token' => $data['user_token']));
        } else {
            return $this->getTableGateway()->insert($data);
        }
    }
    
    public function findByItemIdAndUserToken($itemId, $userToken)
    {
        $row = $this->getTableGateway()->select(array(
           'item_id' => $itemId,
            'user_token' => $userToken
        ));
        
        if(!$row) {
            return null;
        }
        
        return $row;
    }
}