<?php

class UsersController extends Zend_Rest_Controller
{
	/**
	 * Model for current Controller
	 * @var Zend_Db_Table
	 */
	protected $_model;
	
	/**
	 * Request "data" parameter JSON decoded
	 * @var string
	 */
	protected $_requestData;
	
	/**
	 * Get requestData
	 */
	public function getRequestData()
	{
		if (empty($this->_requestData)) {
			$this->setRequestData(Zend_Json::decode($this->getRequest()->getParam("data"), Zend_Json::TYPE_OBJECT));
		}
		
		return $this->_requestData;
	}
	
	/**
	 * Sets requestData
	 * @param $requestData string
	 * @return UsersController
	 */
	public function setRequestData($requestData)
	{
		$this->_requestData = $requestData;
		return $this;
	}
	
	/**
	 * Get model
	 * 
	 * @return Zend_Db_Table
	 */
	public function getModel()
	{
		if (empty($this->_model)) {
			$this->setModel();
		}
		
		return $this->_model;
	}
	
	/**
	 * Set model
	 * 
	 * @param $model Zend_Db_Table
	 * @return UsersController
	 */
	public function setModel($model)
	{
		if (empty($model)) {
			$model = new Application_Model_DbTable_Users();
		}
		
		$this->_model = $model;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_helper->viewRenderer->setNoRender(true);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Rest/Zend_Rest_Controller::indexAction()
	 */
	public function indexAction()
	{
		$response = new stdClass();
		$requestData = $this->getRequestData();
		
		try {
			if (empty($requestData->start)) $requestData->start = 0;
			if (empty($requestData->count)) $requestData->count = 50;
			
			$result = $this->getModel()->fetchAll(null, null, $requestData->count, $requestData->start);
			$response->data = $result->toArray();
			$response->success = true;
		}
		catch (Exception $e) {
			$response->success = false;
			$repsonse->messages = array($e->getMessage());
		}
		
		$this->getResponse()->appendBody(Zend_Json::encode($response));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Rest/Zend_Rest_Controller::getAction()
	 */
	public function getAction()
	{
		$response = new stdClass();
		$requestData = $this->getRequestData();
		
		try {
			if (empty($requestData->id)) throw new Exception("msg_error_entry_id_must_be_provided");
			
			$entry = $this->getModel()->find($requestData->id);
			
			if (empty($entry)) throw new Exception("msg_error_entry_not_found");
			
			$response->data = array($entry);
			$response->success = true;
		}
		catch (Exception $e) {
			$response->success = false;
			$response->messages = array($e->getMessage());
		}
		
		$this->getResponse()->appendBody(Zend_Json::encode($response));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Rest/Zend_Rest_Controller::postAction()
	 */
	public function postAction()
	{
		$response = new stdClass();
		$requestData = $this->getRequestData();
		
		try {
			/* TODO: Check with staff what kind of validation must be done here */
			if (empty($requestData->entry)) throw new Exception("msg_error_entry_must_be_provided");
			$requestData->entry->id = $this->getModel()->insert(get_object_vars($requestData->entry));
			
			$response->affectedRows = 1;
			$response->data = array($requestData->entry);
			$response->success = true;
		}
		catch (Exception $e) {
			$response->success = false;
			$response->messages = array($e->getMessage());
		}
		
		$this->getResponse()->appendBody(Zend_Json::encode($response));
	}
	
	/**
	 * Update a record
	 */
	public function putAction()
	{
		$response = new stdClass();
		$requestData = $this->getRequestData();
		
		try {
			/* TODO: Check with staff what kind of validation must be done here */
			if (empty($requestData->entry)) throw new Exception("msg_error_entry_must_be_provided");
			if (empty($requestData->entry->id)) throw new Exception("msg_error_entry_id_must_be_provided");
			
			$response->affectedRows = $this->getModel()->update(get_object_vars($requestData->entry), array("id = ?" => $requestData->entry->id));
			$response->data = array($requestData->entry);
			$response->success = true;
		}
		catch (Exception $e) {
			$response->success = false;
			$response->messages = array($e->getMessage());
		}
		
		$this->getResponse()->appendBody(Zend_Json::encode($response));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Rest/Zend_Rest_Controller::deleteAction()
	 */
	public function deleteAction()
	{
		$response = new stdClass();
		$requestData = $this->getRequestData();
		
		try {
			if (empty($requestData->id)) throw new Exception("msg_error_entry_id_must_be_provided");
			
			$response->affectedRows = $this->getModel()->delete(array("id = ?" => $requestData->id));
			$response->success = true;
		}
		catch (Exception $e) {
			$response->success = false;
			$response->messages = array($e->getMessage());
		}
		
		$this->getResponse()->appendBody(Zend_Json::encode($response));
	}
}