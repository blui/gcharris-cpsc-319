<?php
/**
* Configuration related functions
*
* @package    Frp
* @author     Grant Harris
* @version    1.0
*/
class Frp_Config {
    
    
    /**
     * Returns an array of frp variables stored in the application.ini file
     * @return array
     */
    static public function getFrpConfig(){
	$frontController = Zend_Controller_Front::getInstance();
	return $frontController->getParam('bootstrap')->getOption('frp');
    }    

}

