<?php
namespace Application\Db\Entity;

class Notification
{
    /**
     * @var string
     */
    protected $_collection;
    
    /**
     * @var string
     */
    protected $_itemId;
    
    /**
     * @var string
     */
    protected $_userToken;
    
    /**
     * @var string
     */
    protected $_operation;
    
    /**
     * @var \DateTime
     */
    protected $_created;
    
    public function exchangeArray(array $input)
    {
        $retval = new static();
        
        $retval->setCollection(isset($input['collection']) ? $input['collection'] : null)
               ->setItemId(isset($input['item_id']) ? $input['item_id'] : null)
               ->setUserToken(isset($input['user_token']) ? $input['user_token'] : null)
               ->setOperation(isset($input['operation']) ? $input['operation'] : null);
        
        if(isset($input['created'])) {
            $retval->setCreated(\DateTime::createFromFormat(\DateTime::ISO8601, $input['created']));
        }
        
        return $retval;
    }
    
    public function toArray()
    {
        $retval = array(
            'collection' => $this->getCollection(),
            'item_id' => $this->getItemId(),
            'user_token' => $this->getUserToken(),
            'operation' => $this->getOperation(),
        );
        
        $created = $this->getCreated();
        
        if($created instanceof \DateTime)
        {
            $retval['created'] = $created->format(\DateTime::ISO8601);
        }
        
        return $retval;
    }
    
	/**
	 * @return the $_collection
	 */
	public function getCollection() {
		return $this->_collection;
	}

	/**
	 * @return the $_itemId
	 */
	public function getItemId() {
		return $this->_itemId;
	}

	/**
	 * @return the $_userToken
	 */
	public function getUserToken() {
		return $this->_userToken;
	}

	/**
	 * @return the $_operation
	 */
	public function getOperation() {
		return $this->_operation;
	}

	/**
	 * @return the $_created
	 */
	public function getCreated() {
		return $this->_created;
	}

	/**
	 * @param string $_collection
	 * @return self
	 */
	public function setCollection($_collection) {
		$this->_collection = $_collection;
		return $this;
	}

	/**
	 * @param string $_itemId
	 * @return self
	 */
	public function setItemId($_itemId) {
		$this->_itemId = $_itemId;
		return $this;
	}

	/**
	 * @param string $_userToken
	 * @return self
	 */
	public function setUserToken($_userToken) {
		$this->_userToken = $_userToken;
		return $this;
	}

	/**
	 * @param string $_operation
	 * @return self
	 */
	public function setOperation($_operation) {
		$this->_operation = $_operation;
		return $this;
	}

	/**
	 * @param DateTime $_created
	 * @return self
	 */
	public function setCreated($_created) {
		$this->_created = $_created;
		return $this;
	}

}