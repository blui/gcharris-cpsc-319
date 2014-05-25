<?php

class Auth_ForgotController extends Zend_Controller_Action {
    var $model_auth;

    public function init() {
        $this->_helper->layout->setLayout('front');
        $this->model_auth = $auth = new Application_Model_Auth();
    }

    public function indexAction() {
        $code = $this->_request->getParam('code');
        if(isset($code)){
           $this->view->code_valid = $this->model_auth->resetPasswordFromCode($code);
            $this->renderScript('forgot/code_check.phtml');

        }
        
    }

    public function recoverAction() {

        $email = $this->_request->getParam('email');
        try {
            $this->model_auth->sendResetMail($email);
            $this->_helper->json(1);
        } catch (Frp_Exception_Exception $e) {
            $this->_helper->json(0);
        }
    }

}
