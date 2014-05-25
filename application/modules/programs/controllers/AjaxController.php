<?php

class Programs_AjaxController extends Frp_Controller_AjaxAction {

    private $program;
    private $program_session;
    private $session_resource;
    private $session_referral;
    private $sign_in_family;
    private $sign_in_child;
    private $guest_speaker;
    private $family;
    private $referral;
    private $resource;

    public function init() {
        $this->program = new Application_Model_Program();
        $this->program_session = new Application_Model_ProgramSession();
        $this->session_resource = new Application_Model_SessionResource();
        $this->session_referral = new Application_Model_SessionReferral();
        $this->sign_in_family = new Application_Model_SignInFamily();
        $this->sign_in_child = new Application_Model_SignInChild();
        $this->guest_speaker = new Application_Model_GuestSpeaker();
        $this->family = new Application_Model_Family();
        $this->referral = new Application_Model_Referral();
        $this->resource = new Application_Model_Resource();
    }

    public function indexAction() {
        $user = new Application_Model_User();

        $id = $this->_request->getParam('id');
        if (isset($id)) {
            $data = $this->program->getProgramStaffByID($id);
        } else {
            $data = $this->program->getAllProgramData($this->getAuthInfo());
        }

        $data = array_merge(array('coordinators' => $user->getCoordinatorAccounts()), array('programs' => $data));
        $this->returnJSON($data, self::OPERATION_SUCCESS);
    }

    /**
     * 
     */
    public function createAction() {
        $name = $this->_request->getParam('name');
        $coordinators = $this->_request->getParam('coordinators');

        try {
            $result = $this->program->createProgram($name, is_array($coordinators) ? $coordinators : array());
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
            $this->returnJSON($this->program->deleteProgram($id), self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {
            echo $e->getErrorMessage();
        }
    }

    /**
     * 
     */
    public function editAction() {
        $id = $this->_request->getParam('id');

        $name = $this->_request->getParam('name');
        $coordinators = $this->_request->getParam('coordinators');

        try {
            $result = $this->program->editProgram($id, $name, is_array($coordinators) ? $coordinators : array());
        } catch (Frp_Exception_Exception $e) {
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }

        $this->returnJSON($result, self::OPERATION_SUCCESS);
    }

    public function sessionAction() {
        try {
            $get = $this->_request->getParam('get');
            $edit = $this->_request->getParam('edit');
            $delete = $this->_request->getParam('delete');
            $create = $this->_request->getParam('create');
            $program_id = $this->_request->getParam('program_id');
            $program_session_id = $this->_request->getParam('program_session_id');
            $previous = $this->_request->getParam('previous');
            $result = array();

            if (empty($program_id)) {
                $this->returnJSON('NO_PROGRAM_ID', self::OPERATION_FAILURE);
            }

            if ($previous !== "1") {
                $dbSession = $this->program_session->getSession($program_id);
                if (!empty($dbSession)) {
                    if (!empty($program_session_id)) {
                        if ($dbSession['session_id'] != $program_session_id) {
                            $this->returnJSON("INVALID_SESSION", self::OPERATION_FAILURE);
                        }
                    } else {
                        $program_session_id = $dbSession['session_id'];
                    }
                } elseif (!empty($program_session_id)) {
                    $this->returnJSON("INVALID_SESSION", self::OPERATION_FAILURE);
                }
            }

            if (isset($get)) {
                switch ($get) {
                    case 'referrals':
                        if (empty($program_session_id)) {
                            $result = $this->program->getReferralsByProgram($program_id);
                        } else {
                            $result = $this->session_referral->getSessionReferrals($program_session_id, $program_id);
                        }
                        break;
                    case 'resources':
                        if (empty($program_session_id)) {
                            $result = $this->program->getResourcesByProgram($program_id);
                        } else {
                            $result = $this->session_resource->getSessionResources($program_session_id, $program_id);
                        }
                        break;
                    case 'session':
                        if (!empty($program_session_id)) {
                            $result = $this->program_session->getSessionByID($program_session_id);
                        }
                        if (!isset($result['program'])) {
                            $result['program'] = $this->program->getProgramByID($program_id);
                        }

                        if (isset($result['date'])) {
                            $result['date'] = DateTime::createFromFormat('Y-m-d', $result['date'])->format("M j, Y");
                        } else {
                            $result['date'] = date_format(new DateTime(), "M j, Y");
                        }
                        break;
                    case 'family':
                        $id = $this->_request->getParam('id');
                        if (!empty($id)) {
                            $result = $this->family->getFamilyMembers($id);
                            if (!empty($program_session_id)) {
                                $result['sign_in_family'] = $this->sign_in_family->getSignInFamily($program_session_id, $id);
                                if (isset($result['sign_in_family']['id'])) {
                                    $result['sign_in_child'] = $this->sign_in_child->getSignInChildren($result['sign_in_family']['id']);
                                } else {
                                    $result['sign_in_child'] = array();
                                }
                            } else {
                                $result['sign_in_family'] = array();
                                $result['sign_in_child'] = array();
                            }
                        }
                        break;
                    case 'checkin':
                        if (!empty($program_session_id)) {
                            $result = $this->program_session->getCheckin($program_session_id);
                        }
                        break;
                }
            } elseif (isset($edit)) {
                $field = $this->_request->getParam('field');
                $value = $this->_request->getParam('value');
                $id = $this->_request->getParam('id');

                if (empty($program_session_id)) {
                    $tmp = $this->program_session->createSession($program_id);
                    $program_session_id = $tmp['id'];
                }

                switch ($edit) {
                    case 'referrals':
                        if (empty($id) || empty($field) || !isset($value)) {
                            break;
                        }
                        if ($field == "count" && $value == 0) {
                            $result = $this->session_referral->deleteSessionReferral($program_session_id, $id);
                        } else {
                            $result = $this->session_referral->createOrEditSessionReferral($program_session_id, $id, $field, $value);
                        }
                        $result = array($result);
                        break;
                    case 'resources':
                        if (empty($id) || empty($field) || !isset($value)) {
                            break;
                        }
                        if ($field == "count" && $value == 0) {
                            $result = $this->session_resource->deleteSessionResource($program_session_id, $id);
                        } else {
                            $result = $this->session_resource->createOrEditSessionResource($program_session_id, $id, $field, $value);
                        }
                        $result = array($result);
                        break;
                    case 'session':
                        if (empty($field) || !isset($value)) {
                            break;
                        }

                        if ($field === "guest_speakers") {
                            $this->guest_speaker->deleteGuestSpeakers($program_session_id);
                            $result = $this->guest_speaker->createGuestSpeakers(explode(",", $value), $program_session_id);
                        } else {
                            $result = $this->program_session->editSession($program_session_id, $field, $value);
                            $result = array($result);
                        }
                        break;
                    case 'family':
                        $child_ids = $this->_request->getParam('child_ids');
                        $adult_count = $this->_request->getParam('adult_count');
                        $parent_present = $this->_request->getParam('parent_present');
                        if (!empty($id)) {
                            $result['family'] = $this->sign_in_family->signInFamily($program_session_id, $id, $adult_count, $parent_present, !empty($child_ids));
                            if (!empty($result['family']['id'])) {
                                $result['children'] = $this->sign_in_child->signInChildren($result['family']['id'], $child_ids);
                            }
                        }
                        break;
                }
                $result['program_session_id'] = $program_session_id;
            } elseif (isset($delete)) {
                if ($delete === "session") {
                    if (!empty($program_session_id)) {
                        $result = $this->program_session->deleteSession($program_session_id);
                    }
                }
            }
            $this->returnJSON($result, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {
            $this->returnJSON($e->getErrorMessageMessage(), self::OPERATION_FAILURE);
        }
    }

    public function propertiesAction() {
        try {
            $type = $this->_request->getParam('type');
            $action = $this->_request->getParam('act');
            $id = $this->_request->getParam('id');
            $name = $this->_request->getParam('name');
            $program_id = $this->_request->getParam('program_id');

            $result = array();

            if (empty($type) || empty($action)) {
                $this->returnJSON('NO_TYPE_OR_ACTION', self::OPERATION_FAILURE);
            }

            switch ($action) {
                case 'add':
                    if (empty($name) || empty($program_id)) {
                        $this->returnJSON('NO_NAME_OR_PROGRAM_ID', self::OPERATION_FAILURE);
                    }
                    if ($type === 'referral') {
                        $result = $this->referral->createReferral($name, $program_id);
                    } elseif ($type === 'resource') {
                        $result = $this->resource->createResource($name, $program_id);
                    }
                    break;
                case 'edit':
                    if (empty($id) || empty($name)) {
                        $this->returnJSON('NO_ID_OR_NAME', self::OPERATION_FAILURE);
                    }
                    if ($type === 'referral') {
                        $result = $this->referral->setReferralData($id, $name);
                    } elseif ($type === 'resource') {
                        $result = $this->resource->setResourceData($id, $name);
                    }
                    break;
                case 'delete':
                    if (empty($id)) {
                        $this->returnJSON('NO_ID', self::OPERATION_FAILURE);
                    }
                    if ($type === 'referral') {
                        $result = $this->referral->deleteReferral($id);
                    } elseif ($type === 'resource') {
                        $result = $this->resource->deleteResource($id);
                    }
                    break;
            }
            $this->returnJSON($result, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
    }

    public function sessionsAction() {
        $programs = $this->_request->getParam('programs');
        $families = $this->_request->getParam('families');
        $start = $this->_request->getParam('start_date');
        $end = $this->_request->getParam('end_date');
        $sort = $this->_request->getParam('sort');
        $dir = $this->_request->getParam('dir');
        $page = $this->_request->getParam('p');
        $count = $this->_request->getParam('count');
        $csv = $this->_request->getParam('csv');

        if ($csv === "1") {
            $page = "1";
            $count = "-1";
        }

        try {
            $paginator = $this->program_session->
                    getProgramSessionPaginator($this->getAuthInfo(), $programs, $families, $start, $end, $sort, $dir);
            $paginator->setCurrentPageNumber($page);
            $paginator->setItemCountPerPage(!empty($count) ? $count : 10);
            $data['pagination'] = $paginator->getPages();
            $data['items'] = $paginator->getCurrentItems()->getArrayCopy();

            if ($csv === "1") {
                $this->getResponse()->setHeader("Content-type", "text/csv")
                        ->setHeader("Content-Disposition", 'attachment; filename="Previous Sessions.csv"')
                        ->setBody($this->array2csv($data['items']));
                $this->_helper->Layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                return;
            }

            $data['programs'] = $this->program->getActivePrograms($this->getAuthInfo());
        } catch (Frp_Exception_Exception $e) {
            $this->returnJSON($e->getMessage(), self::OPERATION_FAILURE);
        }
        $this->returnJSON($data, self::OPERATION_SUCCESS);
    }

}

