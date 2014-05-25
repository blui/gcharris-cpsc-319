<?php

/**
 * DbTable model for table Staff
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_DbTable_Staff extends Frp_Db_Table_Abstract {

    protected $_name = 'staff';
    protected $_primary = 'id';
    protected $_rowClass = 'Application_Model_Row_Staff';

    /**
     * Given a email return staff details
     * @param varchar $email
     * @return table with details of staff
     */
    public function getStaffByEmail($email) {
        $this->select->reset();
        $this->select->from($this);
        $this->select->where('email = ?', $email);
        return $this->fetchRow($this->select);
    }

    /**
     * Given a job type array, return select of all staff with type
     * @param array or string $job_types
     * @return select
     */
    public function getStaffByJobTypeSelect($job_types) {
        $this->select->reset();
        $this->select->from($this);

        if (is_array($job_types)) {
            $first = true;
            foreach ($job_types as $job_type) {
                if ($first == true) {
                    $this->select->where('job_type = ?', $job_type);
                } else {
                    $this->select->orWhere('job_type = ?', $job_type);
                }
                $first = false;
            }
        } else {
            $this->select->where('job_type = ?', $job_types);
        }

        return $this->select;
    }

    /**
     * Return volunteers with matching names
     * @param string $q
     * @return select
     */
    public function getVolunteerStaffSelect($q) {
        $db = $this->getAdapter();

        $this->select->reset();
        $this->select->from($this);
        $this->select->where('job_type IN (2,3)');

        if (!empty($q)) {
            $nameWhere = $db->quoteInto("full_name LIKE ? OR last_name LIKE ?", $q . '%');
            $this->select->where($nameWhere);
        }

        return $this->select;
    }

    /**
     * Given a job type array, return all staff with type
     * @param array or string $job_types
     * @return table with details of staff 
     */
    public function getStaffByJobType($job_types) {
        $select = $this->getStaffByJobTypeSelect($job_types);
        return $this->fetchAll($select);
    }

}

