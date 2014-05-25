<?php

/**
 * Model for authentication of user logins
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_Auth {

    /**
     * A reference to the singleton instance of Zend_Auth 
     */
    protected $auth;

    function __construct() {
        // Get a reference to the singleton instance of Zend_Auth
        $this->auth = Zend_Auth::getInstance();
    }

    /**
     * Given an email, return a password reset request
     * @param string $email
     * @return type
     * @throws Frp_Exception_Email
     */
    public function sendResetMail($email) {
        //Filter and validates emails that are false
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Frp_Exception_Email($email, Frp_Exception_Email::EMAIL_INVALID);
        }

        $staff = new Application_Model_Staff();
        $staff_member = $staff->getStaffByEmail($email);

        if (!empty($staff_member)) {
            $reset_table = new Application_Model_DbTable_PasswordReset();
            $reset_row = $reset_table->makeResetCode($staff_member['id']);
            $this->sendPasswordResetEmail($staff_member, $reset_row['reset_code']);
        }
        /*
         * Return an empty array since we don't want the user to know if the 
         * email is used in the system or not. If its invalid it should silently fail
         */

        return array();
    }

    /**
     * 
     * @param type $reset_code
     */
    public function resetPasswordFromCode($reset_code) {
        $reset_table = new Application_Model_DbTable_PasswordReset();
        $user_model = new Application_Model_User();
        $staff_model = new Application_Model_Staff();

        $staff = $reset_table->getStaffIDFromReset($reset_code);
        //Make sure the code exists
        if (isset($staff['staff_id'])) {
            $password = Frp_Hashing::generatePassword();
            $user_model->changePassword($staff['staff_id'], $password);

            $staff_member = $staff_model->getStaff($staff['staff_id']);
            $this->sendPasswordResetConfirmedEmail($staff_member, $password);
            
            //Clear out the used reset code
            $reset_table->deleteSelected();
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Delete any reset emails older than $days
     * @param integer $days
     * @return array
     */
    public function deleteResetEmailDaysOld($days) {
        $reset_table = new Application_Model_DbTable_PasswordReset();
        return $reset_table->deleteDaysOld($days);
    }

    /**
     * 
     * @param type $staff_member
     * @param type $password
     * @return type
     */
    private function sendPasswordResetConfirmedEmail($staff_member, $password) {
        $name = $staff_member['first_name'] . ' ' . $staff_member['last_name'];
        $mail = new Frp_Mail();

        $config = Frp_Config::getFrpConfig();

        $params = array(
            'to' => $staff_member['email'],
            'password' => $password,
            'name' => $name,
            'frp_url' => $config['url'],
            'frp_name' => $config['name']);

        $mail->addTo($staff_member['email']);

        $mail->setFrom($config['email'], $config['name']);
        $mail->setSubject("Your password has been reset");

        $mail->setBodyByView('password_reset', $params);

        return $mail->send();
    }

    /**
     * Sends a user an email when their user is created
     * @param integer $staff_id
     * @return array 
     */
    private function sendPasswordResetEmail($staff_member, $reset_string) {
        $name = $staff_member['first_name'] . ' ' . $staff_member['last_name'];
        $mail = new Frp_Mail();

        $config = Frp_Config::getFrpConfig();

        $params = array(
            'reset_string' => $reset_string,
            'name' => $name,
            'frp_url' => $config['url'],
            'frp_name' => $config['name']);

        $mail->addTo($staff_member['email']);

        $mail->setFrom($config['email'], $config['name']);
        $mail->setSubject("Password reset request");

        $mail->setBodyByView('password_reset_request', $params);

        return $mail->send();
    }

    /**
     * Using a username and password, authenticate the user
     * @param varchar $email
     * @param text $password
     * @return boolean
     */
    public function authenticate($email, $password) {

        // Set up the authentication adapter
        $authAdapter = new Frp_Authenticate_Adapter($email, $password);

        // Attempt authentication, saving the result
        $result = $this->auth->authenticate($authAdapter);

        if (!$result->isValid()) {
            return false;
        } else {
            
            $user = $authAdapter->getResultRowArray();

            $staff = new Application_Model_Staff();
            $staff_member = $staff->getStaff($user['staff_id']);

            $session = $this->getSessionNamespace();
            $session->permission_level = $user['permission_level'];
            $session->user = $staff_member;
            $session->access_restricted = 0;
            return true;
        }
    }
    
    public function getSessionNamespace(){
        return new Zend_Session_Namespace('FRP');
    }

    /**
     * Reload the session based off of the permission level of staff member
     * @return reloaded session
     */
    public function reloadSessionData() {
            $session = $this->getSessionNamespace();
            $staff = new Application_Model_Staff();
            $staff_member = $staff->getStaff($session->user['id']);
            $session->user = $staff_member;
    }

    /**
     * Return the identity of the currently authenticated user
     * @return boolean
     */
    public function getAuthIdentity() {
        if ($this->auth->hasIdentity()) {
            // Identity exists; get it
            return $this->auth->getIdentity();
        } else {
            return false;
        }
    }

    /**
     * Logs the user out
     * @return boolean
     */
    public function logout() {
        $this->auth->clearIdentity();
        return true;
    }

}