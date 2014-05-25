<?php

class Staff_AjaxController extends Frp_Controller_AjaxAction {

    private $user;
    private $staff;
    private $staff_hours;
    private $program;

    public function init() {
        $this->user = new Application_Model_User();
        $this->staff = new Application_Model_Staff();
        $this->staff_hours = new Application_Model_StaffHours();
        $this->program = new Application_Model_Program();
    }

    public function indexAction() {
        $id = $this->_request->getParam('id');

        if (isset($id)) {
            $user_account = $this->user->getFulltimeAccountByID($id);
            $data = array(
                'programs' => $this->program->getAllPrograms($this->getAuthInfo()),
                'user' => $user_account
            );
        } else {
            $data = $this->user->getFulltimeAccounts();

            $csv = $this->_request->getParam('csv');

            if ($csv === "1") {
                $this->getResponse()->setHeader("Content-type", "text/csv")
                        ->setHeader("Content-Disposition", 'attachment; filename="Staff.csv"')
                        ->setBody($this->array2csv($data));
                $this->_helper->Layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                return;
            }
        }

        $this->returnJSON($data, self::OPERATION_SUCCESS);
    }

    public function volunteersAction() {
        $id = $this->_request->getParam('id');

        if (isset($id)) {
            $data = $this->staff->getStaff($id);
        } else {
            $q = $this->_request->getParam('q');
            $p = $this->_request->getParam('p');
            $count = $this->_request->getParam('count');
            $csv = $this->_request->getParam('csv');

            if ($csv === "1") {
                $p = "1";
                $count = "-1";
            }

            $paginator = $this->staff->getVolunteerStaffPaginator($q);
            $paginator->setCurrentPageNumber($p);
            $paginator->setItemCountPerPage(!empty($count) ? $count : 10);
            $data['pagination'] = $paginator->getPages();
            $data['items'] = $paginator->getCurrentItems()->getArrayCopy();

            if ($csv === "1") {
                $this->getResponse()->setHeader("Content-type", "text/csv")
                        ->setHeader("Content-Disposition", 'attachment; filename="Volunteers.csv"')
                        ->setBody($this->array2csv($data['items']));
                $this->_helper->Layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                return;
            }
        }

        $this->returnJSON($data, self::OPERATION_SUCCESS);
    }

    public function contributionsAction() {
        $id = $this->_request->getParam('id');

        if (isset($id)) {
            $data['hours'] = $this->staff_hours->getStaffHoursByID($id);
            $data['programs'] = $this->program->getAllPrograms($this->getAuthInfo());
        } else {
            $data['programs'] = $this->program->getAllPrograms($this->getAuthInfo());

            $staff = $this->_request->getParam('staff');
            $programs = $this->_request->getParam('programs');
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

            $paginator = $this->staff_hours->getAllStaffHoursPaginator($this->getAuthInfo(), $staff, $programs, $start, $end, $sort, $dir);
            $paginator->setCurrentPageNumber($p);
            $paginator->setItemCountPerPage(!empty($count) ? $count : 10);
            $data['pagination'] = $paginator->getPages();
            $data['items'] = $paginator->getCurrentItems()->getArrayCopy();

            if ($csv === "1") {
                $this->getResponse()->setHeader("Content-type", "text/csv")
                        ->setHeader("Content-Disposition", 'attachment; filename="Volunteer Contributions.csv"')
                        ->setBody($this->array2csv($data['items']));
                $this->_helper->Layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                return;
            }
        }

        $this->returnJSON($data, self::OPERATION_SUCCESS);
    }

    /**
     * /users/ajax/createcontribution
     */
    public function createcontributionAction() {
        $staff_id = $this->_request->getParam('staff');
        $program_id = $this->_request->getParam('program');
        $date = date('Y-m-d', strtotime($this->_request->getParam('date')));
        $hours = $this->_request->getParam('hours');

        /*
         * Since we have data that could be invalid staff_id and permission_level
         * we wrap it in a try catch
         */
        try {
            $response = $this->staff_hours->createStaffHours($staff_id, $program_id, $date, $hours);

            //All good, send data back to front end
            $this->returnJSON($response, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {

            //There was a problem, return the error to the front end
            $this->returnJSON($e->getMessage(), self::OPERATION_FAILURE);
        }
    }

    /**
     * /users/ajax/editcontribution
     */
    public function editcontributionAction() {
        $id = $this->_request->getParam('id');

        $data = array(
            'program_id' => $this->_request->getParam('program'),
            'staff_id' => $this->_request->getParam('staff'),
            'date' => date('Y-m-d', strtotime($this->_request->getParam('date'))),
            'hours' => $this->_request->getParam('hours')
        );

        try {
            $response = $this->staff_hours->editStaffHours($id, $data);

            //All good, send data back to front end
            $this->returnJSON($response, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {

            //There was a problem, return the error to the front end
            $this->returnJSON($e->getMessage(), self::OPERATION_FAILURE);
        }
    }

    /**
     * /users/ajax/deletecontribution
     */
    public function deletecontributionAction() {
        $id = $this->_request->getParam('id');

        try {
            $this->returnJSON($this->staff_hours->deleteStaffHours($id), self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {
            $this->returnJSON($e->getMessage(), self::OPERATION_FAILURE);
        }
    }

    /**
     * /staff/ajax/create
     */
    public function createAction() {
        $this->returnJSON($this->program->getAllPrograms($this->getAuthInfo()), self::OPERATION_SUCCESS);
    }

    /**
     * /staff/ajax/createvolunteer
     */
    public function createvolunteerAction() {
        $data = array();

        $data['first_name'] = $this->_request->getParam('first_name');
        $data['last_name'] = $this->_request->getParam('last_name');
        $data['job_type'] = $job_type = $this->_request->getParam('job_type');
        $email = $this->_request->getParam('email');

        if ($data['job_type'] === "2") {
            $data['comments'] = "Volunteer";
        } elseif ($data['job_type'] === "3") {
            $data['comments'] = "Praticum student";
        }

        if (isset($email)) {
            $data['email'] = $email;
        }

        /*
         * Since we have data that could be invalid staff_id and permission_level
         * we wrap it in a try catch
         */
        try {
            $response = $this->staff->createVolunteer($data);

            //All good, send data back to front end
            $this->returnJSON($response, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {

            //There was a problem, return the error to the front end
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
    }

    /**
     * /staff/ajax/editVolunteer
     */
    public function editvolunteerAction() {
        $staff_id = $this->_request->getParam('id');

        $data = array(
            'first_name' => $this->_request->getParam('first_name'),
            'last_name' => $this->_request->getParam('last_name'),
            'email' => $this->_request->getParam('email'),
            'job_type' => $this->_request->getParam('job_type')
        );

        /*
         * Since we have data that could be invalid staff_id and permission_level
         * we wrap it in a try catch
         */
        try {
            $response = $this->staff->editStaffMemberInfo($staff_id, $data);

            //All good, send data back to front end
            $this->returnJSON($response, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {

            //There was a problem, return the error to the front end
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
    }

    /**
     * /staff/ajax/createUser
     */
    public function createuserAction() {
        $first_name = $this->_request->getParam('first_name');
        $last_name = $this->_request->getParam('last_name');
        $email = $this->_request->getParam('email');
        $permission_level = $this->_request->getParam('permission_level');
        $program_ids = $this->_request->getParam('programs');

        /*
         * Since we have data that could be invalid staff_id and permission_level
         * we wrap it in a try catch
         */
        try {
            $response = $this->user->createUser($first_name, $last_name, $email, $permission_level, $program_ids);

            //All good, send data back to front end
            $this->returnJSON($response, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {

            //There was a problem, return the error to the front end
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
    }

    /**
     * /staff/ajax/editUser
     */
    public function edituserAction() {
        $staff_id = $this->_request->getParam('id');

        $data = array(
            'first_name' => $this->_request->getParam('first_name'),
            'last_name' => $this->_request->getParam('last_name'),
            'email' => $this->_request->getParam('email')
        );

        $permission_level = $this->_request->getParam('permission_level');
        $program_ids = $this->_request->getParam('programs');

        /*
         * Since we have data that could be invalid staff_id and permission_level
         * we wrap it in a try catch
         */
        try {
            $response = $this->user->editAccount($staff_id, $data, $permission_level, $program_ids);

            //All good, send data back to front end
            $this->returnJSON($response, self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {

            //There was a problem, return the error to the front end
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
    }

    /**
     * /staff/ajax/deleteUser
     */
    public function deleteuserAction() {
        $id = $this->_request->getParam('id');

        try {
            $this->returnJSON($this->staff->deleteStaffMember($id), self::OPERATION_SUCCESS);
        } catch (Frp_Exception_Exception $e) {
            $this->returnJSON($e->getErrorMessage(), self::OPERATION_FAILURE);
        }
    }

    public function emailexistsAction() {
        $orig_value = $this->_request->getParam('value');

        $email_exists = preg_match('/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}/', $orig_value) ? $this->staff->getStaffByEmail($orig_value) : false;

        $this->_helper->json(array(
            'value' => $orig_value,
            'valid' => $email_exists ? $email_exists['id'] == $this->_request->getParam('id') : true,
            'message' => $email_exists ? 'Email address used by ' . $email_exists['first_name'] .  ' ' . $email_exists['last_name'] : ''
        ));
    }

}

