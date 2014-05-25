<?php

/**
 * DbTable model for table Program
 * @package    Frp
 * @author     Grant Harris and Sammie Chan
 * @version    1.0
 */
class Application_Model_DbTable_Program extends Frp_Db_Table_Abstract {

    protected $_name = 'program';
    protected $_primary = 'id';
    protected $_rowClass = 'Application_Model_Row_Program';

    /**
     * Gets all data associated with program
     * @return array
     */
    public function getAllProgramData($userSession) {
        $this->select->reset();
        $select = $this->select
                ->setIntegrityCheck(false)
                ->from(array('p' => 'program'), array('program_id' => 'p.id', 'program_name' => 'p.name'))
                ->joinLeft(array('pst' => 'program_staff'), 'pst.program_id = p.id', '')
                ->joinLeft(array('s' => 'staff'), 's.id = pst.staff_id', array('coordinators' => 'GROUP_CONCAT(CONCAT(first_name, " ", last_name) SEPARATOR ", ")'))
                ->joinLeft(array('u' => 'user'), 'u.staff_id = s.id', '')
                ->joinLeft(array('ps' => 'program_session'), 'ps.program_id = p.id AND ps.date = DATE(NOW()) AND ps.running = 1', array('session_id' => 'ps.id'))
                ->order(array('session_id DESC', 'program_name'))
                ->group('p.id');
        if ($userSession['permission_level'] === "1") {
            if (!empty($userSession['programs'])) {
                $select = $select->where('p.id IN (?)', $userSession['programs']);
            } else {
                $select = $select->where('p.id IS NULL');
            }
        }

        return $this->fetchAll($select);
    }

    /**
     * Gets the program associated with given id
     * @param integer $id
     * @return array
     */
    public function getProgramStaffByID($id) {
        $this->select->reset();
        $select = $this->select
                ->setIntegrityCheck(false)
                ->from(array('p' => 'program'), array('program_id' => 'p.id', 'program_name' => 'p.name'))
                ->joinLeft(array('pst' => 'program_staff'), 'pst.program_id = p.id', 'staff_id')
                ->where('p.id = ?', $id);
        return $this->fetchAll($select);
    }

    /**
     * Gets active programs
     * @return array programs
     */
    public function getActivePrograms($userSession) {
        $this->select->reset();
        $select = $this->select->setIntegrityCheck(false)
                ->from(array('P' => 'program'))
                ->where('EXISTS(SELECT * FROM program_session WHERE program_id = P.id)')
                ->order('P.name');
        if ($userSession['permission_level'] === "1") {
            if (!empty($userSession['programs'])) {
                $select = $select->where('P.id IN (?)', $userSession['programs']);
            } else {
                $select = $select->where('P.id IS NULL');
            }
        }

        return $this->fetchAll($select);
    }

    /**
     * Gets all programs
     * @return array programs
     */
    public function getAllPrograms($userSession) {
        $this->select->reset();
        $select = $this->select->from(array('P' => 'program'))
                ->order('P.name');
        if ($userSession['permission_level'] === "1") {
            if (!empty($userSession['programs'])) {
                $select = $select->where('P.id IN (?)', $userSession['programs']);
            } else {
                $select = $select->where('P.id IS NULL');
            }
        }

        return $this->fetchAll($select);
    }

}

