<?php

/**
 * DbTable model for table User
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_DbTable_User extends Frp_Db_Table_Abstract {

    protected $_name = 'user';
    protected $_primary = 'staff_id';
    protected $_rowClass = 'Application_Model_Row_User';
    protected $_referenceMap = array(
        'User' => array(
            'columns' => 'staff_id',
            'refTableClass' => 'Application_Model_DbTable_Staff',
            'refColumns' => 'id'
        )
    );

    /**
     * Method grabs all accounts associated to coordinators
     * @return table with all coordinator account details
     */
    public function getCoordinatorAccounts() {
        $this->select->reset();
        $select = $this->select
                ->setIntegrityCheck(false)
                ->from(array('s' => 'staff'), array('id', 'first_name', 'last_name'))
                ->join(array('u' => 'user'), 's.id = u.staff_id', array('permission_level'))
                ->where('permission_level = ' . Application_Model_User::USER_COORDINATOR);
        return $this->fetchAll($select);
    }

    /**
     * Method grabs all full-time staff account details
     * @return table with details of full-time staff accounts
     */
    public function getFulltimeAccounts() {
        $this->select->reset();
        $select = $this->select
                ->setIntegrityCheck(false)
                ->from(array('s' => 'staff'), array('id', 'first_name', 'last_name', 'email', 'job_type'))
                ->join(array('u' => 'user'), 's.id = u.staff_id', array('permission_level'))
                ->joinLeft(array('pst' => 'program_staff'), 'pst.staff_id = s.id', '')
                ->joinLeft(array('p' => 'program'), 'p.id = pst.program_id', array('programs' => 'GROUP_CONCAT(p.name SEPARATOR ", ")'))
                ->order(array('permission_level'))
                ->group('s.id');
        return $this->fetchAll($select);
    }

    /**
     * Method grabs all full-time accounts associated with given id
     * @param integer $id
     * @return table with details of full-time accounts assoicated with id
     */
    public function getFulltimeAccountByID($id) {
        $this->select->reset();
        $select = $this->select
                ->setIntegrityCheck(false)
                ->from(array('s' => 'staff'), array('id', 'first_name', 'last_name', 'email', 'job_type'))
                ->join(array('u' => 'user'), 's.id = u.staff_id', array('permission_level'))
                ->joinLeft(array('pst' => 'program_staff'), 'pst.staff_id = s.id', array('programs' => 'GROUP_CONCAT(pst.program_id SEPARATOR ",")'))
                ->where('s.id = ?', $id)
                ->group('s.id');
        return $this->fetchAll($select);
    }

}

