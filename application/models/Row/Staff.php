<?php

/**
 * Model Row DbTable Staff
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_Row_Staff extends Zend_Db_Table_Row_Abstract {

    /**
     * User rowset
     */
    private $user = null;

    /**
     * Hours rowset
     */
    private $hours = null;

    /**
     * Programs rowset 
     */
    private $programs = null;

    /**
     * Gets user corresponding to the staff member
     * @return Model_Row_User
     */
    public function getUser() {
        if (!$this->user) {
            $this->user = $this->findDependentRowset('Application_Model_DbTable_User')->current();
        }

        return $this->user;
    }

    /**
     * Gets staff member hours
     * optional sql to limit results
     * @return Model_Row_StaffHours
     */
    public function getStaffHours($sql = null) {
        if (!$this->hours) {
            $this->hours = $this->findDependentRowset('Application_Model_DbTable_StaffHours', null, $sql);
        }

        return $this->hours;
    }

    /**
     * Gets staff programs
     * @return Model_Row_ProgramStaff
     */
    public function getStaffPrograms() {
        if (!$this->programs) {
            $this->programs = $this->findDependentRowset('Application_Model_DbTable_ProgramStaff');
        }

        return $this->programs;
    }

}
