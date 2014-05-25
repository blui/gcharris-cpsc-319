<?php

/**
 * Controller abstract for use by all controllers
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 * @abstract
 */
abstract class Frp_Controller_Action extends Zend_Controller_Action {

    protected $_authenticatedIdentity;

    /**
     * Should be called in preDispatch to set the identity of the logged in user
     */
    protected function setAuthIdentity() {
        if ($this->getRequest()->getActionName() != "login") {
            $auth = new Application_Model_Auth();
            $this->authenticatedIdentity = $auth->getAuthIdentity();
        } else {
            $this->authenticatedIdentity = null;
        }
    }

    /**
     * 
     * @return type
     */
    function getSessionNameSpace() {
        $auth = new Application_Model_Auth();
        return $auth->getSessionNamespace();
    }

    public function getAuthInfo() {
        $staff = new Application_Model_Staff();
        $session = $this->getSessionNameSpace();
        $data['id'] = $session->user['id'];
        $data['permission_level'] = $session->permission_level;

        $data['programs'] = array();
        $programs = $staff->getStaffPrograms($data['id']);
        if (!empty($programs)) {
            foreach ($programs as $program) {
                $data['programs'][] = $program['program_id'];
            }
        }

        return $data;
    }

    public function returnInvalidPermission() {
        $e = new Frp_Exception_User("", Frp_Exception_User::USER_PERMISSION_LEVEL_INVALID);
        $this->returnJSON($e->getErrorMessage(), 0);
    }

    public function checkValidPermission($program_id = 0) {
        $userSession = $this->getAuthInfo();
        if ($userSession['permission_level'] !== 0) {
            if (empty($program_id)) {
                $this->returnInvalidPermission();
            }

            if (is_array($program_id)) {
                foreach ($program_id as $id) {
                    if (!in_array($id, $userSession['programs'], TRUE)) {
                        $this->returnInvalidPermission();
                    }
                }
            } else {
                if (!in_array($program_id, $userSession['programs'], TRUE)) {
                    $this->returnInvalidPermission();
                }
            }
        }
    }

    /**
     * 
     */
    public function reloadSessionData() {
        $auth = new Application_Model_Auth();
        $auth->reloadSessionData();
    }

    public function array2csv(array &$array) {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

}
