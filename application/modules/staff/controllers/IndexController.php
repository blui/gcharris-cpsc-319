<?php

class Staff_IndexController extends Frp_Controller_StaticAction
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $session = $this->getSessionNameSpace();
        $this->view->sidebar_data = array();

        if ($session->permission_level == 0) {
            $this->view->sidebar_data['User Accounts'] = array(
                'Accounts' => ''
            );
        }

        $this->view->sidebar_data['Volunteers'] = array(
            'Volunteers' => 'volunteers',
            'Volunteer Hours' => 'contributions'
        );

    }


}

