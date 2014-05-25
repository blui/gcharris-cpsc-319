<?php

/**
 * Model for Staff
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_Staff extends Frp_Model_Abstract {

    const FULLTIME = 1;
    const VOLUNTEER = 2;
    const STUDENT = 3;

    /**
     * Stores an instance of the staff db table
     * @var Application_Model_DbTable_Staff 
     */
    private $staff_table;

    function __construct() {
        $this->staff_table = new Application_Model_DbTable_Staff();
    }

    /**
     * Return an array with all staff members
     * @return array
     */
    public function getStaffMembers() {
        return $this->rowsetToArray($this->staff_table->findAll());
    }

    /**
     * Return a specifc staff member
     * @param integer $staff_id
     * @return array
     */
    public function getStaff($staff_id) {
        return $this->rowsetToArray($this->staff_table->find($staff_id)->current());
    }

    /**
     * Get all full time staff
     * @return array
     */
    public function getFulltimeStaff() {
        return $this->rowsetToArray($this->staff_table->getStaffByJobType(self::FULLTIME));
    }

    /**
     * Get only volunteer staff members paginator
     * @return paginator
     */
    public function getVolunteerStaffPaginator($q) {
        $adapter = new Zend_Paginator_Adapter_DbSelect($this->staff_table->getVolunteerStaffSelect($q));
        return new Zend_Paginator($adapter);
    }

    /**
     * Get only volunteer staff members
     * @return array
     */
    public function getVolunteerStaff() {
        return $this->rowsetToArray($this->staff_table->getStaffByJobType(array(self::VOLUNTEER, self::STUDENT)));
    }

    /**
     * Given an email, return the staff member associated with it
     * @param string $email
     * @return array
     */
    public function getStaffByEmail($email) {
        return $this->rowsetToArray($this->staff_table->getStaffByEmail($email));
    }

    /**
     * Get a list of the staff members hours from a staff id
     * @param integer $staff_id
     * @return array
     */
    public function getStaffHours($staff_id) {
        $staff_member = $this->staff_table->find($staff_id)->current();
        $rows = $staff_member->getStaffHours();
        return $this->rowsetToArray($rows);
    }

    /**
     * Get a list of all the programs associated with a specifc staff id
     * @param integer $staff_id
     * @return array
     */
    public function getStaffPrograms($staff_id) {
        $staff_member = $this->staff_table->find($staff_id)->current();
        $rows = $staff_member->getStaffPrograms();
        return $this->rowsetToArray($rows);
    }

    /**
     * Make a new Volunteer
     * @param string $first_name
     * @param string $last_name
     * @param string $email
     * @param integer $job_type
     * @return array
     * @throws Frp_Exception_Email
     * @throws Frp_Exception_Staff
     */
    public function createVolunteer($data) {

        //Make sure the email is valid if supplied
        if (strlen($data['email']) > 0 && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Frp_Exception_Email($data['email'], Frp_Exception_Email::EMAIL_INVALID);
        }

        switch ($data['job_type']) {
            case self::VOLUNTEER:
            case self::STUDENT:
                break;

            default:
                throw new Frp_Exception_Staff($data['job_type'], Frp_Exception_Staff::STAFF_JOB_TYPE_INVALID);
                break;
        }

        $result = $this->staff_table->insert($data);
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();
        return $result;




//            if ($e->getCode() == 23000) {
//                throw new Frp_Exception_Staff($email, Frp_Exception_Staff::EMAIL_ALREADY_EXISTS);
//            }
    }

    /**
     * Make a new staff member
     * @param string $first_name
     * @param string $last_name
     * @param string $email
     * @param integer $job_type
     * @return array
     * @throws Frp_Exception_Email
     * @throws Frp_Exception_Staff
     */
    public function createStaffMember($first_name, $last_name, $email, $job_type) {

        //Make sure the email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Frp_Exception_Email($email, Frp_Exception_Email::EMAIL_INVALID);
        }

        switch ($job_type) {
            case self::FULLTIME:
            case self::VOLUNTEER:
            case self::STUDENT:
                break;

            default:
                throw new Frp_Exception_Staff($job_type, Frp_Exception_Staff::STAFF_JOB_TYPE_INVALID);
                break;
        }

        $data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'job_type' => $job_type
        );

        $result = $this->staff_table->insert($data);
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();
        return $result;




//            if ($e->getCode() == 23000) {
//                throw new Frp_Exception_Staff($email, Frp_Exception_Staff::EMAIL_ALREADY_EXISTS);
//            }
    }

    /**
     * Given a staff id, delete the record
     * @param integer $staff_id
     * @return array
     */
    public function deleteStaffMember($staff_id) {
        $result = $this->staff_table->delete(array('id = ?' => $staff_id));
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();

        return $result;
    }

    /**
     * Given a staff id, update the staff info
     * @param integer $staff_id
     * @return array
     */
    public function editStaffMemberInfo($staff_id, $data) {

        if (isset($data['email'])) {
            //Make sure the email is valid
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Frp_Exception_Email($data['email'], Frp_Exception_Email::EMAIL_INVALID);
            }
        }
        $result = $this->staff_table->update($data, array('id = ?' => $staff_id));
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();

        return $result;
    }

}

