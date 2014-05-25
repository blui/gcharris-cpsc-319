<?php

class Application_Model_Row_User extends Zend_Db_Table_Row_Abstract {

    private $staff = null;

    /**
     * Get staff member corresponding to the user
     * @return Model_Row_Staff
     */
    public function getStaffMember() {
        if (!$this->staff) {
            $this->staff = $this->findParentRow('Model_DbTable_Staff');
        }

        return $this->staff;
    }

}
