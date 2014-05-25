<?php
/**
 * Exception class for Staff related errors
 *
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Frp_Exception_Staff extends Frp_Exception_Exception{

    const STAFF_ID_INVALID = 1;
    const STAFF_JOB_TYPE_INVALID = 2;
    const EMAIL_ALREADY_EXISTS = 3;

    protected $_exception_type = 'staff';

        
    /**
     * Return a standard error message based on the error code
     * @return array
     */
    function getErrorMessage() {

        switch ($this->getCode()) {
            case self::STAFF_ID_INVALID:
                return $this->returnExceptionMessage("Staff id invalid");
                break;

            case self::STAFF_JOB_TYPE_INVALID:
                return $this->returnExceptionMessage("Staff job type invalid");
                break;
            default:
                return $this->returnExceptionMessage("An unknown staff error occured");
                break;
        }
    }

}