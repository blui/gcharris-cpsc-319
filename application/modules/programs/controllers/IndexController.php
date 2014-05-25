<?php

class Programs_IndexController extends Frp_Controller_StaticAction {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $this->view->sidebar_data = array(
            'Programs' => array(
                'My Programs' => '',
                'Previous Sessions' => 'sessions'
            )
        );
    }

}

