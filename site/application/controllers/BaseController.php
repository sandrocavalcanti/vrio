<?php
class BaseController extends Zend_Controller_Action
{

	public function init ()
    {
    }
    public function getBaseUrl ()
    {
        return Zend_Controller_Front::getInstance()->getBaseUrl();
    }
    public function getPathAbsoluto ()
    {
       $config = Zend_Registry::get('config');
       return $config->app->config->pathFisicoAbsoluto;
    }
    public function getDb(){
    	$_db = Zend_Registry::get('db');
    	return $_db;
    }
}