<?php

/**
 * DbTable model for table StaffHours
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_DbTable_StaffHours extends Frp_Db_Table_Abstract {

    protected $_name = 'staff_hours';
    protected $_primary = 'id';
    protected $_columns = 'DATE_FORMAT(date,\'%m/%d/%Y\')';
    protected $_referenceMap = array(
        'Staff' => array(
            'columns' => 'staff_id',
            'refTableClass' => 'Application_Model_DbTable_Staff',
            'refColumns' => 'id'
        ),
        'Program' => array(
            'columns' => 'program_id',
            'refTableClass' => 'Application_Model_DbTable_Program',
            'refColumns' => 'id'
        )
    );

    /**
     * Method that returns hours associated staff_id
     * @param integer $staff_id
     * @return table with hours
     */
    public function getStaffHours($id) {
        $select = $this->select
                ->setIntegrityCheck(false)
                ->from(array('SH' => 'staff_hours'))
                ->join(array('S' => 'staff'), 'S.id = SH.staff_id')
                ->columns(array('date_formatted' => 'DATE_FORMAT(date,\'%m/%d/%Y\')', 'id' => 'SH.id'))
                ->where('SH.id = ?', $id);
        return $this->fetchAll($select)->current();
    }

    /**
     * Method that returns select statement for hours for all staff
     * @param array $staff
     * @param array $programs
     * @param date $start
     * @param date $end
     * @param string $sort
     * @param int $dir
     * @return select
     */
    public function getAllStaffHoursSelect($userSession, $staff, $programs, $start, $end, $sort, $dir) {
        $this->select->reset();
        $values = array(
            'id' => 'sh.id',
            'staff_id' => 'sh.staff_id',
            'first_name' => 's.first_name',
            'last_name' => 's.last_name',
            'program_id' => 'p.id',
            'program_name' => 'p.name',
            'hours' => 'sh.hours',
            'date_formatted' => 'DATE_FORMAT(sh.date,\'%m/%d/%Y\')',
            'date' => 'sh.date'
        );
        $select = $this->select
                ->setIntegrityCheck(false);
        if ($sort === "last_name") {
            $select = $select->from(array('s' => 'staff'), $values);
        } else {
            $select = $select->from(array('sh' => 'staff_hours'), $values);
        }

        $select = $select->where('s.job_type IN (?)', array(2, 3));

        if ($sort === "program_name") {
            $select = $select->join(array('p' => 'program'), 'sh.program_id = p.id', '')
                    ->join(array('s' => 'staff'), 'sh.staff_id = s.id', '');
        } elseif ($sort === "last_name") {
            $select = $select->join(array('sh' => 'staff_hours'), 'sh.staff_id = s.id', '')
                    ->join(array('p' => 'program'), 'sh.program_id = p.id', '');
        } else {
            $select = $select->joinLeft(array('p' => 'program'), 'sh.program_id = p.id', '')
                    ->joinLeft(array('s' => 'staff'), 'sh.staff_id = s.id', '');
        }

        $db = $this->getAdapter();

        if (!empty($staff)) {
            $select = $select->where('sh.staff_id IN (?)', $staff);
        }

        if (!empty($programs)) {
            $select = $select->where('sh.program_id IN (?)', $programs);
        }

        if ($userSession['permission_level'] === "1") {
            if (!empty($userSession['programs'])) {
                $select = $select->where('sh.program_id IN (?)', $userSession['programs']);
            } else {
                $select = $select->where('sh.program_id IS NULL');
            }
        }

        if (!(empty($start) || empty($end))) {
            $start = DateTime::createFromFormat('m/d/Y', $start)->format('Y-m-d');
            $end = DateTime::createFromFormat('m/d/Y', $end)->format('Y-m-d');
            $where = 'sh.date BETWEEN ? AND ?';
            $where = $db->quoteInto($where, $start, 'DATE', 1);
            $where = $db->quoteInto($where, $end, 'DATE', 1);
            $select = $select->where($where);
        }

        if (!empty($sort) && isset($dir)) {
            if ($dir === "0") {
                $dir = "ASC";
            } else {
                $dir = "DESC";
            }

            $select = $select->order($sort . " " . $dir);
        } else {
            $select = $select->order('sh.date DESC');
        }

        return $select;
    }

    /**
     * Method that returns hours for all staff
     * @param integer $staff_id
     * @return table with hours
     */
    public function getAllStaffHours($userSession, $staff, $program, $start, $end, $sort, $dir) {
        return $this->fetchAll($this->getAllStaffHoursSelect($userSession, $staff, $program, $start, $end, $sort, $dir));
    }

}

