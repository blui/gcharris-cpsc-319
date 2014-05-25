<?php
/**
 * Helper for automatically including CSS files in modules
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Zend_View_Helper_CSSHelper 
{ 
    function cssHelper() { 
        $request = Zend_Controller_Front::getInstance()->getRequest(); 
	$file_uri = 'css/' . $request->getModuleName()  .'/' . $request->getControllerName() . '/' . $request->getActionName() . '.css';         
        if (file_exists($file_uri)) { 
            $this->view->headLink()->appendStylesheet('/' . $file_uri); 
        } 
         
        return $this->view->headLink(); 
         
    } 
}
