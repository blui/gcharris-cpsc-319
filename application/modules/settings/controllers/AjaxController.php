<?php

class Settings_AjaxController extends Frp_Controller_AjaxAction {

    var $user_table;

    public function init() {
        $this->user_table = new Application_Model_User();
    }

    public function indexAction() {
         $session = $this->getSessionNameSpace();
         $this->returnJSON($session->user, self::OPERATION_SUCCESS);
    }

    public function editAction() {
        $session = $this->getSessionNameSpace();
        $user_id = $session->user['id'];

        $data = array(
            'first_name' => $this->_request->getParam('first_name'),
            'last_name' => $this->_request->getParam('last_name'),
            'email' => $this->_request->getParam('email'),
            'my_account' => TRUE
        );

        $password = $this->_request->getParam('new_password');
        $password2 = $this->_request->getParam('new_password2');
        $oldpassword = $this->_request->getParam('old_password');

        if ($password && $password == $password2) {

            //Copy to the logged in user's email
            $user_account = $this->user_table->getUserFromStaffId($user_id);

            $pw_hash = crypt($oldpassword, $user_account['pass_salt']);

            if ($pw_hash == $user_account['pass_hash']) {
                $data['password'] = $password;
            }
        }

        /*
         * Since we have data that could be invalid staff_id and permission_level
         * we wrap it in a try catch
         */
        try {

            $response = $this->user_table->editAccount($session->user['id'], $data);
            $this->reloadSessionData();
            //All good, send data back to front end
            $this->returnJSON($response, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {

            //There was a problem, return the error to the front end
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
    }

}

