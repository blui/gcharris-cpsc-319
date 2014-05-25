<?php

/**
 * DbTable model for table PasswordReset
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_DbTable_PasswordReset extends Frp_Db_Table_Abstract {

    protected $_name = 'password_reset';
    protected $_primary = 'reset_code';
    var $where;

    public function makeResetCode($staff_id) {
        $reset_code = Frp_Hashing::generateResetString();

        $data = array('staff_id' => $staff_id,
            'reset_code' => $reset_code);

        return $this->insert($data);
    }

    /**
 * 
 * @param type $reset_code
 * @return row
 */
    public function getStaffIDFromReset($reset_code) {
        $this->select->reset();
        $this->where = $this->getAdapter()->quoteInto('reset_code = ?', $reset_code);
        $this->select->where($this->where);
        $this->select->from($this, array('staff_id'));
        return $this->fetchRow($this->select);

    }

    /**
     * Delete all attachments more than $days old
     * @param integer $days
     * @return array
     */
    public function deleteDaysOld($days) {
        $where = $this->getAdapter()->quoteInto("sent < now() - interval ? day", $days);
        return $this->delete($where);
    }
    
    /**
     * Delete all attachments more than $days old
     * @return array
     */
    public function deleteSelected() {
        return $this->delete($this->where);
    }

}