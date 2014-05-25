<?php

class Auth_LoginController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function preDispatch() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction() {
        
        $email = $this->_request->getParam('email');
        $password = $this->_request->getParam('password');

        $auth = new Application_Model_Auth();
        $authenticated = $auth->authenticate($email, $password);

        if ($authenticated === true) {
            $this->_helper->json(1);
        } else {
            $this->_helper->json(0);
        }
    }

}

