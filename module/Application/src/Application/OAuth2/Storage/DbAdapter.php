<?php

namespace Application\OAuth2\Storage;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use GoogleGlass\Entity\OAuth2\Token;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use GoogleGlass\OAuth2\Storage\StorageInterface;

class DbAdapter implements StorageInterface, FactoryInterface, ServiceLocatorAwareInterface
{
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;
    
    /**
     * @var unknown
     */
    protected $_userId;
    
    /**
	 * @return the $_userId
	 */
	public function getUserId() {
		return $this->_userId;
	}

	/**
	 * @param  $_userId
	 * @return self
	 */
	public function setUserId($_userId) {
		$this->_userId = $_userId;
		return $this;
	}

	public function createService(ServiceLocatorInterface $serviceLocator)
    {
        
    }
    
    public function store(Token $token)
    {
        
    }
    
    public function retrieve()
    {
        
    }
    
    public function destroy()
    { 
        
    }
}