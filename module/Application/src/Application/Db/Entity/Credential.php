<?php

namespace Application\Db\Entity;

class Credential
{
    /**
     * @var string
     */
    protected $_userId;
    
    /**
     * @var string
     */
    protected $_authToken;
    
    /**
     * @var string
     */
    protected $_refreshToken;
    
    /**
     * @var string
     */
    protected $_locationGuid;
    
    /**
     * @var string
     */
    protected $_timelineGuid;
    
    /**
	 * @return the $_refreshToken
	 */
	public function getRefreshToken() {
		return $this->_refreshToken;
	}

	/**
	 * @param string $_refreshToken
	 * @return self
	 */
	public function setRefreshToken($_refreshToken) {
		$this->_refreshToken = $_refreshToken;
		return $this;
	}

	public function exchangeArray(array $input)
    {
        $this->setAuthToken(isset($input['auth_token']) ? $input['auth_token'] : null)
               ->setLocationGuid(isset($input['user_location_guid']) ? $input['user_location_guid'] : null)
               ->setTimelineGuid(isset($input['user_timeline_guid']) ? $input['user_timeline_guid'] : null)
               ->setRefreshToken(isset($input['refresh_token']) ? $input['refresh_token'] : null)
               ->setUserId(isset($input['user_id']) ? $input['user_id'] : null);
    }
    
    public function toArray()
    {
        return array(
            'user_id' => $this->getUserId(),
            'auth_token' => $this->getAuthToken(),
            'refresh_token' => $this->getRefreshToken(),
            'user_location_guid' => $this->getLocationGuid(),
            'user_timeline_guid' => $this->getTimelineGuid()
        );
    }
    
	/**
	 * @return the $_userId
	 */
	public function getUserId() {
		return $this->_userId;
	}

	/**
	 * @return the $_authToken
	 */
	public function getAuthToken() {
		return $this->_authToken;
	}

	/**
	 * @return the $_locationGuid
	 */
	public function getLocationGuid() {
		return $this->_locationGuid;
	}

	/**
	 * @return the $_timelineGuid
	 */
	public function getTimelineGuid() {
		return $this->_timelineGuid;
	}

	/**
	 * @param string $_userId
	 * @return self
	 */
	public function setUserId($_userId) {
		$this->_userId = $_userId;
		return $this;
	}

	/**
	 * @param string $_authToken
	 * @return self
	 */
	public function setAuthToken($_authToken) {
		$this->_authToken = $_authToken;
		return $this;
	}

	/**
	 * @param string $_locationGuid
	 * @return self
	 */
	public function setLocationGuid($_locationGuid) {
		$this->_locationGuid = $_locationGuid;
		return $this;
	}

	/**
	 * @param string $_timelineGuid
	 * @return self
	 */
	public function setTimelineGuid($_timelineGuid) {
		$this->_timelineGuid = $_timelineGuid;
		return $this;
	}

    
}