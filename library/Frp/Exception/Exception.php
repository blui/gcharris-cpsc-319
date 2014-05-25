<?php

/**
 * Base Frp exception class
 *
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 * @abstract
 */
abstract class Frp_Exception_Exception extends Exception {

    var $exceptionType;

    /**
     * Return a standard message for exceptions from Frp
     * @param type $message
     * @return array
     */
    protected function returnExceptionMessage($string) {
        $message = array();
        if(isset($this->_exception_type)){
            $message['type'] = $this->_exception_type;
        }
        $message['code'] = $this->getCode();
        $message['message'] = $string;
        
        return $message;
    }

    /**
     * Return a standard error message based on the error code
     * @return array
     * @abstract
     */
    abstract function getErrorMessage();
}

