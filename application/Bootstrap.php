<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	protected function _initDatabase()
	{
		$this->bootstrap('db');
		$db = $this->getResource('db');
		Zend_Registry::set('db', $db);
		Zend_Db_Table::setDefaultAdapter($db);
	}

	protected function _initRestRoute()
	{
		$this->bootstrap('frontController');
		$frontController = Zend_Controller_Front::getInstance();
		$restRoute = new Zend_Rest_Route($frontController);
		$frontController->getRouter()->addRoute('default', $restRoute);
	}

}

