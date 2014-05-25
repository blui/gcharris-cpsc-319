<?php

class Participants_AjaxController extends Frp_Controller_AjaxAction {

    private $family;
    private $languages;
    private $countries;
    private $program;
    private $child;
    private $sign_in_family;
    private $sign_in_child;
    private $translation;
    private $program_session;

    public function init() {
        $this->family = new Application_Model_Family();
        $this->languages = new Application_Model_Language();
        $this->countries = new Application_Model_Country();
        $this->program = new Application_Model_Program();
        $this->child = new Application_Model_Child();
        $this->sign_in_family = new Application_Model_SignInFamily();
        $this->sign_in_child = new Application_Model_SignInChild();
        $this->hear_about_us = new Application_Model_HearAboutUs();
        $this->translation = new Application_Model_Translation();
        $this->program_session = new Application_Model_ProgramSession;
    }

    public function indexAction() {
        $id = $this->_request->getParam('id');

        if (isset($id)) {
            if (!empty($id)) {
                $data = $this->family->getFamilyMembers($id);
            }

            $data['languages'] = $this->languages->getAllLangCodes();
            $data['countries'] = $this->countries->getAllCountries();
            $data['hear_about_us'] = $this->hear_about_us->getAllHearAboutUs();
            $lang_code = $this->_request->getParam('lang');
            if (!empty($lang_code)) {
                $data['translation'] = Zend_Json::decode($this->translation->getTranslation($lang_code));
            }
        } else {
            $q = $this->_request->getParam('q');
            $programs = $this->_request->getParam('programs');
            $languages = $this->_request->getParam('languages');
            $countries = $this->_request->getParam('countries');
            $start = $this->_request->getParam('start_date');
            $end = $this->_request->getParam('end_date');
            $sort = $this->_request->getParam('sort');
            $dir = $this->_request->getParam('dir');
            $csv = $this->_request->getParam('csv');
            $p = $this->_request->getParam('p');
            $count = $this->_request->getParam('count');

            if ($csv === "1") {
                $p = "1";
                $count = "-1";
            }

            $paginator = $this->family->getFamiliesPaginator($this->getAuthInfo(), $q, $programs, $languages, $countries, $start, $end, $sort, $dir);
            $paginator->setCurrentPageNumber($p);

            $paginator->setItemCountPerPage(!empty($count) ? $count : 10);
            $data['pagination'] = $paginator->getPages();
            $data['items'] = $paginator->getCurrentItems()->getArrayCopy();
            if ($csv === "1") {
                $this->getResponse()->setHeader("Content-type", "text/csv")
                        ->setHeader("Content-Disposition", 'attachment; filename="Participants.csv"')
                        ->setBody($this->array2csv($data['items']));
                $this->_helper->Layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                return;
            }

            $first_run = $this->_request->getParam('firstrun');
            if ($first_run === '1') {
                $data['languages'] = $this->languages->getActiveLanguages();
                $data['countries'] = $this->countries->getActiveCountries();
                $data['programs'] = $this->program->getActivePrograms($this->getAuthInfo());
            }
        }

        $this->returnJSON($data, self::OPERATION_SUCCESS);
    }

    public function createAction() {
        $data = array(
            'allergies' => filter_var($this->_request->getParam('allergies'), FILTER_SANITIZE_STRING),
            'emerg_contact_first_name' => filter_var($this->_request->getParam('emerg_contact_first_name'), FILTER_SANITIZE_STRING),
            'emerg_contact_last_name' => filter_var($this->_request->getParam('emerg_contact_last_name'), FILTER_SANITIZE_STRING),
            'emerg_contact_phone' => $this->_request->getParam('emerg_contact_phone'),
            'emerg_contact_relation' => filter_var($this->_request->getParam('emerg_contact_relation'), FILTER_SANITIZE_STRING),
            'children' => $this->_request->getParam('children'),
            'guardian_email' => filter_var($this->_request->getParam('guardian_email'), FILTER_SANITIZE_EMAIL),
            'guardian_first_lang' => filter_var($this->_request->getParam('guardian_first_lang'), FILTER_SANITIZE_STRING),
            'guardian_first_name' => filter_var($this->_request->getParam('guardian_first_name'), FILTER_SANITIZE_STRING),
            'guardian_last_name' => filter_var($this->_request->getParam('guardian_last_name'), FILTER_SANITIZE_STRING),
            'guardian_origin_country' => filter_var($this->_request->getParam('guardian_origin_country'), FILTER_SANITIZE_STRING),
            'guardian_partner_first_name' => filter_var($this->_request->getParam('guardian_partner_first_name'), FILTER_SANITIZE_STRING),
            'guardian_partner_last_name' => filter_var($this->_request->getParam('guardian_partner_last_name'), FILTER_SANITIZE_STRING),
            'guardian_role' => filter_var($this->_request->getParam('guardian_role'), FILTER_SANITIZE_STRING),
            'hear_about_us' => filter_var($this->_request->getParam('hear_about_us'), FILTER_SANITIZE_NUMBER_INT),
            'phone_number' => $this->_request->getParam('phone_number'),
            'postal_3dig' => filter_var($this->_request->getParam('postal_3dig'), FILTER_SANITIZE_STRING),
            'registration_date' => date("Y/m/d")
        );

        if (empty($data['hear_about_us'])) {
            unset($data['hear_about_us']);
        }

        if (empty($data['guardian_first_lang'])) {
            unset($data['guardian_first_lang']);
        }

        if (empty($data['guardian_origin_country'])) {
            unset($data['guardian_origin_country']);
        }

        if (empty($data['guardian_email'])) {
            unset($data['guardian_email']);
        }

        $fad = $this->_request->getParam('first_attendance_date');
        if (!empty($fad)) {
            $data['first_attendance_date'] = DateTime::createFromFormat('m/d/Y', $fad)->format('Y-m-d');
        }

        $data['emerg_contact_phone'] = preg_replace("/[^\d]/", "", $data['emerg_contact_phone']);
        $data['phone_number'] = preg_replace("/[^\d]/", "", $data['phone_number']);

        try {
            $response = $this->family->createFamily($data);
            $program = $this->_request->getParam('program_id');
            $session_id = $this->_request->getParam('program_session_id');

            if (!empty($program)) {
                $auth = $this->getSessionNameSpace();
                if (empty($session_id)) {
                    $session = $this->program_session->getSession($program);
                    if (empty($session) && $auth->access_restricted !== 1) {
                        $tmp = $this->program_session->createSession($program);
                        $session_id = $tmp['id'];
                    } else {
                        $session_id = $session['session_id'];
                    }
                }

                if (!empty($session_id)) {
                    $response['sign_in_family'] = $this->sign_in_family->signInFamily($session_id, $response['family']['id'], 1, 1, !empty($response['children']));
                    if (!empty($response['children'])) {
                        $child_ids = array();
                        foreach ($response['children'] as $child) {
                            $child_ids[] = $child['id'];
                        }
                        $response['sign_in_child'] = $this->sign_in_child->signInChildren($response['sign_in_family']['id'], $child_ids);
                    } else {
                        $response['sign_in_child'] = array();
                    }
                } else {
                    $response['sign_in_family'] = array();
                    $response['sign_in_child'] = array();
                }
            }
            
            $response['program_session_id'] = $session_id;
            //All good, send data back to front end
            $this->returnJSON($response, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {
            //There was a problem, return the error to the front end
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
    }

    public function editAction() {
        $id = $this->_request->getParam('id');

        $data = array(
            'allergies' => $this->_request->getParam('allergies'),
            'emerg_contact_first_name' => $this->_request->getParam('emerg_contact_first_name'),
            'emerg_contact_last_name' => $this->_request->getParam('emerg_contact_last_name'),
            'emerg_contact_phone' => $this->_request->getParam('emerg_contact_phone'),
            'emerg_contact_relation' => $this->_request->getParam('emerg_contact_relation'),
            'children' => $this->_request->getParam('children'),
            'guardian_email' => $this->_request->getParam('guardian_email'),
            'guardian_first_lang' => $this->_request->getParam('guardian_first_lang'),
            'guardian_first_name' => $this->_request->getParam('guardian_first_name'),
            'guardian_last_name' => $this->_request->getParam('guardian_last_name'),
            'guardian_origin_country' => $this->_request->getParam('guardian_origin_country'),
            'guardian_partner_first_name' => $this->_request->getParam('guardian_partner_first_name'),
            'guardian_partner_last_name' => $this->_request->getParam('guardian_partner_last_name'),
            'guardian_role' => $this->_request->getParam('guardian_role'),
            'hear_about_us' => $this->_request->getParam('hear_about_us'),
            'phone_number' => $this->_request->getParam('phone_number'),
            'postal_3dig' => $this->_request->getParam('postal_3dig')
        );

        if (empty($data['hear_about_us'])) {
            $data['hear_about_us'] = NULL;
        }

        if (empty($data['guardian_first_lang'])) {
            $data['guardian_first_lang'] = NULL;
        }

        if (empty($data['guardian_origin_country'])) {
            $data['guardian_origin_country'] = NULL;
        }

        if (empty($data['guardian_email'])) {
            $data['guardian_email'] = NULL;
        }

        $fad = $this->_request->getParam('first_attendance_date');
        if (isset($fad)) {
            if (!empty($fad)) {
                $data['first_attendance_date'] = DateTime::createFromFormat('m/d/Y', $fad)->format('Y-m-d');
            } else {
                $data['first_attendance_date'] = NULL;
            }
        }

        $data['emerg_contact_phone'] = preg_replace("/[^\d]/", "", $data['emerg_contact_phone']);
        $data['phone_number'] = preg_replace("/[^\d]/", "", $data['phone_number']);

        try {
            $response = $this->family->updateFamily($id, $data);

            //All good, send data back to front end
            $this->returnJSON($response, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {
            //There was a problem, return the error to the front end
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
    }

    public function deleteAction() {
        $id = $this->_request->getParam('id');

        try {
            $response = $this->family->deleteFamily($id);

            //All good, send data back to front end
            $this->returnJSON($response, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {
            //There was a problem, return the error to the front end
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
    }

    public function uploadAction() {
        $this->returnJSON($this->uploadFiles(), self::OPERATION_SUCCESS);
    }

    public function browseAction() {
        $this->returnJSON("", self::OPERATION_SUCCESS);
    }

    public function searchAction() {
        $q = $this->_request->getParam('q');
        $page = $this->_request->getParam('p');
        $result = array();

        if (empty($q)) {
            $this->returnJSON($result, self::OPERATION_SUCCESS);
        }

        try {
            if (!ctype_digit(substr($q, 0, 1))) {
                $paginator = $this->family->getNamePaginator($q);
            } else {
                $q = preg_replace("/[^\d]/", "", $q);
                $paginator = $this->family->getPhonePaginator($q);
            }
            $paginator->setCurrentPageNumber($page);
            $result['pagination'] = $paginator->getPages();
            $result['items'] = $paginator->getCurrentItems()->getArrayCopy();
        } catch (Frp_Exception_Exception $e) {
            $this->returnJSON($e->getMessage(), self::OPERATION_FAILURE);
        }
        $this->returnJSON($result, self::OPERATION_SUCCESS);
    }

    private function keyExistsORNull($array, $key) {
        return (array_key_exists($key, $array)) ? $array[$key] : null;
    }

    public function emailAction() {
        if ($this->_request->getParam('useFilters') == 1) {
            $filter_array = array();
            parse_str($this->_request->getParam('filters'), $filter_array);

            $q = $this->keyExistsORNull($filter_array, 'q');
            $programs = $this->keyExistsORNull($filter_array, 'programs');
            $langauges = $this->keyExistsORNull($filter_array, 'langauges');
            $countries = $this->keyExistsORNull($filter_array, 'countries');
            $start = $this->keyExistsORNull($filter_array, 'start');
            $end = $this->keyExistsORNull($filter_array, 'end');

            $paginator = $this->family->getFamiliesPaginator($this->getAuthInfo(), $q, $programs, $langauges, $countries, $start, $end, null, null);
            $participants = $paginator->getCurrentItems()->getArrayCopy();

            $participants_array = array();

            foreach ($participants as $participant) {
                $participants_array[] = $participant['id'];
            }
        } else {
            $participants_array = $this->_request->getParam('recipients');
        }

        $subject = strip_tags($this->_request->getParam('subject'));
        $message = $this->_request->getParam('message');
        $uploads = $this->_request->getParam('uploads');

        //Copy to the logged in user's email
        if ($this->_request->getParam('mail_me') == 1) {
            //Copy to the logged in user's email
            $session = $this->getSessionNameSpace();
            $sender_cc = $session->user['email'];
        } else {
            $sender_cc = null;
        }
        $data = $this->family->sendEmailToParticipants($participants_array, $subject, $message, $sender_cc, $uploads);
        $this->returnJSON($data, self::OPERATION_SUCCESS);
    }

    public function childAction() {
        $id = $this->_request->getParam('id');
        $action = $this->_request->getParam('act');
        $family_id = $this->_request->getParam('family_id');
        $first = $this->_request->getParam('first_name');
        $last = $this->_request->getParam('last_name');
        $bday = $this->_request->getParam('birthday');
        $session_id = $this->_request->getParam('program_session_id');

        try {
            switch ($action) {
                case 'get':
                    if (empty($id)) {
                        $this->returnJSON("NO_ID", self::OPERATION_FAILURE);
                    }

                    $this->returnJSON($this->child->getChildByID($id), self::OPERATION_SUCCESS);
                    break;
                case 'add':
                    if (empty($family_id) || empty($first)) {
                        $this->returnJSON("NO_FAMILY_FIRST", self::OPERATION_FAILURE);
                    }

                    $data = array('family_id' => $family_id,
                        'first_name' => $first);
                    if (isset($last)) {
                        $data['last_name'] = $last;
                    }
                    if (isset($bday)) {
                        if (!empty($bday)) {
                            $data['birthday'] = DateTime::createFromFormat('m/d/Y', $bday)->format('Y-m-d');
                        } else {
                            $data['birthday'] = NULL;
                        }
                    }

                    $this->child->createChild($data);
                    break;
                case 'edit':
                    if (empty($id) && (isset($first) && empty($first))) {
                        $this->returnJSON("NO_ID_FIRST", self::OPERATION_FAILURE);
                    }

                    $data = array();
                    if (!empty($family_id)) {
                        $data['family_id'] = $family_id;
                    }
                    if (!empty($first)) {
                        $data['first_name'] = $first;
                    }
                    if (isset($last)) {
                        $data['last_name'] = $last;
                    }
                    if (isset($bday)) {
                        if (!empty($bday)) {
                            $data['birthday'] = DateTime::createFromFormat('m/d/Y', $bday)->format('Y-m-d');
                        } else {
                            $data['birthday'] = NULL;
                        }
                    }

                    $this->child->setChildData($id, $data);
                    break;
                case 'delete':
                    if (empty($id)) {
                        $this->returnJSON("NO_ID", self::OPERATION_FAILURE);
                    }

                    $this->child->deleteChild($id);
                    break;
            }

            if (!empty($family_id)) {
                $result = $this->family->getFamilyMembers($family_id);
                if (!empty($session_id)) {
                    $result['sign_in_family'] = $this->sign_in_family->getSignInFamily($session_id, $family_id);
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
        } catch (Frp_Exception_Exception $e) {
            $this->returnJSON($e->getMessage(), self::OPERATION_FAILURE);
        }
        $this->returnJSON($result, self::OPERATION_SUCCESS);
    }

    public function phoneexistsAction() {
        $orig_value = $this->_request->getParam('value');
        $value = preg_replace('/[^0-9]/', '', $orig_value);
        $auth = $this->getSessionNameSpace();

        $phone_exists = strlen($value) == 10 ? $this->family->phoneExists($value) : false;

        $data = array(
            'value' => $orig_value,
            'valid' => $phone_exists ? $phone_exists['id'] == $this->_request->getParam('id') : true
        );

        if ($auth->access_restricted !== 1) {
            $data['message'] = $phone_exists ? 'Phone number used by ' . $phone_exists['guardian_first_name'] . ' ' . $phone_exists['guardian_last_name'] : '';
        }

        $this->_helper->json($data);
    }

    public function emailexistsAction() {
        $orig_value = $this->_request->getParam('value');
        $auth = $this->getSessionNameSpace();

        $email_exists = preg_match('/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}/', $orig_value) ? $this->family->emailExists($orig_value) : false;

        $data = array(
            'value' => $orig_value,
            'valid' => $email_exists ? $email_exists['id'] == $this->_request->getParam('id') : true
        );

        if ($auth->access_restricted !== 1) {
            $data['message'] = $email_exists ? 'Email address used by ' . $email_exists['guardian_first_name'] . ' ' . $email_exists['guardian_last_name'] : '';
        }

        $this->_helper->json($data);
    }
}

