<?php

namespace Application\Db\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Application\Db\Entity\Credential;

class CredentialsTable implements FactoryInterface
{
    /**
     * @var \Zend\Db\TableGateway\TableGateway
     */
    protected $_tableGateway;
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        
        $resultSetProtoType = new ResultSet();
        $resultSetProtoType->setArrayObjectPrototype(new Credential());
        
        $tg = new TableGateway('credentials', $dbAdapter, null, $resultSetProtoType);
        
        $retval = new static();
        $retval->setTableGateway($tg);
        
        return $retval;
    }
    
    public function save(Credential $cred)
    {
        $data = $cred->toArray();
        
        if($this->findByUserId($data['user_id'])) {
            return $this->getTableGateway()->update($data, array('user_id' => $data['user_id']));
        } 
        
        return $this->getTableGateway()->insert($data);
    }
    
    public function fetchAll()
    {
        return $this->getTableGateway()->select();
    }
    
    public function findByTimelineGuid($guid)
    {
        $row  = $this->getTableGateway()->select(array('user_timeline_guid' => $guid));
        return $row->current();
    }
    
    public function findByLocationGuid($guid)
    {
        $row = $this->getTableGateway()->select(array('user_location_guid' => $guid));
        return $row->current();
    }
    
    public function findByUserId($id)
    {
        $row = $this->getTableGateway()
                     ->select(array('user_id' => $id));
        
        return $row->current();
    }
    
	/**
	 * @return \Zend\Db\TableGateway\TableGateway
	 */
	public function getTableGateway() {
		return $this->_tableGateway;
	}

	/**
	 * @param \Zend\Db\TableGateway\TableGateway $_tableGateway
	 * @return self
	 */
	public function setTableGateway(\Zend\Db\TableGateway\TableGateway $_tableGateway) {
		$this->_tableGateway = $_tableGateway;
		return $this;
	}

}
