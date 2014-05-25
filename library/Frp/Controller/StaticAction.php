<?php

/**
 * Abstract class to be extended by all Static controllers
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 * @abstract
 */
abstract class Frp_Controller_StaticAction extends Frp_Controller_Action {

    /**
     * We have the pre-dispatch in here, because we want to always run the 
     * authentication page before we do anything else in the controller
     */
    public function preDispatch() {
        $this->setAuthIdentity();
        $session = $this->getSessionNameSpace();
        
        //Check to see if the user is logged in
        if (!$this->authenticatedIdentity || ($session->access_restricted == 1 && $this->getRequest()->getControllerName() != "register")) {
            $this->_forward("index", "index", "auth");
        }else{

         $this->view->staff_id =  $session->user['id'];
         $this->view->permission_level =  $session->permission_level;
         $this->view->logged_in_user = $session->user['first_name'] . " " . $session->user['last_name'];
        }
    }

}

