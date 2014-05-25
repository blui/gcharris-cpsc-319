<?php

class Auth_LogoutController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function preDispatch() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction() {
        $auth = new Application_Model_Auth();
        $this->_helper->json($auth->logout());
    }

}

