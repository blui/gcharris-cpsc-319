<?php

class Partners_AjaxController extends Frp_Controller_AjaxAction {

    private $model;

    public function init() {
        $this->model = new Application_Model_Partner();
    }

    /**
     * 
     */
    public function indexAction() {
        $id = $this->_request->getParam('id');
        if (isset($id)) {
            $data = $this->model->getPartnerByID($id);
        } else {
            $data = $this->model->getAllPartners();
            $csv = $this->_request->getParam('csv');

            if ($csv === "1") {
                $this->getResponse()->setHeader("Content-type", "text/csv")
                        ->setHeader("Content-Disposition", 'attachment; filename="Partners.csv"')
                        ->setBody($this->array2csv($data));
                $this->_helper->Layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                return;
            }
        }
        $this->returnJSON($data, self::OPERATION_SUCCESS);
    }

    /**
     * 
     */
    public function createAction() {

        $name = $this->_request->getParam('name');
        $email = $this->_request->getParam('email');
        $organization = $this->_request->getParam('organization');
        $comments = $this->_request->getParam('comments');

        try {
            $result = $this->model->createPartner($name, $email, $organization, $comments);
        } catch (Frp_Exception_Exception $e) {
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
        $this->returnJSON($result, self::OPERATION_SUCCESS);
    }

    /**
     * 
     */
    public function deleteAction() {

        $id = $this->_request->getParam('id');

        try {
            $this->returnJSON($this->model->deletePartner($id), self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * 
     */
    public function editAction() {

        $id = $this->_request->getParam('id');

        $data = array(
            'name' => $this->_request->getParam('name'),
            'email' => $this->_request->getParam('email'),
            'organization' => $this->_request->getParam('organization'),
            'comments' => $this->_request->getParam('comments')
        );

        try {
            $result = $this->model->editPartnerInfo($id, $data);
        } catch (Frp_Exception_Exception $e) {
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }

        $this->returnJSON($result, self::OPERATION_SUCCESS);
    }

    public function uploadAction() {
        $this->returnJSON($this->uploadFiles(), self::OPERATION_SUCCESS);
    }

    public function emailAction() {

        $partner_array = $this->_request->getParam('recipients');
        $subject = strip_tags($this->_request->getParam('subject'));
        $message = $this->_request->getParam('message');
        $uploads = $this->_request->getParam('uploads');


        if ($this->_request->getParam('mail_me') == 1) {
        //Copy to the logged in user's email
        $session = $this->getSessionNameSpace();
        $sender_cc = $session->user['email'];
        } else {
            $sender_cc = null;
        }

        $data = $this->model->sendEmailToPartners($partner_array, $subject, $message, $sender_cc, $uploads);
        $this->returnJSON($data, self::OPERATION_SUCCESS);
    }

}

