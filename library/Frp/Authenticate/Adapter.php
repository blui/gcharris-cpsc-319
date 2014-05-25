<?php

/**
 * Authentication mechanism for logging in users
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Frp_Authenticate_Adapter implements Zend_Auth_Adapter_Interface {

    /**
     * Store the passed in email for login
     * @var type 
     */
    protected $_email;

    /**
     * Store the passed in password for login
     * @var type 
     */
    protected $_password;

    /**
     * Stores the row returned from the user model when looking up a user to auth
     * @var type 
     */
    protected $row;

    public function __construct($email, $password) {
        $this->_email = $email;
        $this->_password = $password;
        $this->row = array();
    }

    /**
     * Authenticate the user
     * @return Zend_Auth_Result
     */
    public function authenticate() {

        $table = new Application_Model_User();
        $this->row = $table->getUserFromEmail($this->_email);

        //No rows in DB means no user exists with $email
        if ($this->row == null) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_email);
        }

        $hash = stripslashes($this->row['pass_hash']);

        //Compute a hash using the supplied password and the stored salt, 
        //compare to the stored hash for the specified email
        if (crypt($this->_password, $this->row['pass_salt']) != $hash) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_email);
        } else {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_email);
        }
    }

    /**
     * Return the result row from authentication
     * @return array 
     */
    public function getResultRowArray() {
        return $this->row;
    }

}
