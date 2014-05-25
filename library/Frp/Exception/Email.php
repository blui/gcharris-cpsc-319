<?php

/**
 * Exception class for email related errors
 *
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Frp_Exception_Email extends Frp_Exception_Exception {

    const EMAIL_INVALID = 1;
    const EMAIL_ALREADY_IN_USE = 2;
    const INVALID_ATTACHMENT_TYPE = 3;
    const ATTACHMENT_TOO_LARGE = 4;
    const INVALID_EMAIL_OBJECT = 5;
    
    protected $_exception_type = 'email';

    /**
     * Return a standard error message based on the error code
     * @return array
     */
    public function getErrorMessage() {
        switch ($this->getCode()) {
            
            case self::EMAIL_ALREADY_IN_USE:
                return $this->returnExceptionMessage($this->getMessage() . " is already being used");
                break;

            case self::EMAIL_INVALID:
                return $this->returnExceptionMessage($this->getMessage() . " is an invalid email address.");
                break;
            default:
                return $this->returnExceptionMessage("An unknown email error occured");
                break;
        }
    }

}