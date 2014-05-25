<?php
/**
 * Exception class for user related errors
 *
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Frp_Exception_User extends Frp_Exception_Exception {

    const USER_PERMISSION_LEVEL_INVALID = 1;
    const USER_CREATE_ERROR = 2;
    const NO_USER_FOR_STAFF_ID = 3;
    
    protected $_exception_type = 'user';


    /**
     * Return a standard error message based on the error code
     * @return array
     */
    function getErrorMessage() {

        switch ($this->getCode()) {
            case self::USER_PERMISSION_LEVEL_INVALID:
                return $this->returnExceptionMessage("User permission level invalid");
                break;

            case self::USER_CREATE_ERROR:
                return $this->returnExceptionMessage("User creation error");
                break;

            case self::NO_USER_FOR_STAFF_ID:
                return $this->returnExceptionMessage("No user for staff id");
                break;

            default:
                return $this->returnExceptionMessage("An unknown user error occured");
                break;
        }
    }

}