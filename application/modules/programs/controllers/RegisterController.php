<?php

class Programs_RegisterController extends Frp_Controller_StaticAction {

    public function init() {
        $this->_helper->layout->disableLayout();
        /* Initialize action controller here */
    }

    public function indexAction() {
        $program_id = $this->_request->getParam('id');
        $session = $this->getSessionNameSpace();
        if ($session->access_restricted !== 1) {
            $program_session = new Application_Model_ProgramSession;
            $ps = $program_session->getSession($program_id);
            if (empty($ps)) {
                $program_session->createSession($program_id);
            }
            $session->access_restricted = 1;
        }

        $this->view->program_id = $program_id;
    }

}

