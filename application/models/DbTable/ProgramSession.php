<?php

/**
 * DbTable model for table ProgramSession
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_DbTable_ProgramSession extends Frp_Db_Table_Abstract {

    protected $_name = 'program_session';
    protected $_primary = 'id';
    protected $_rowClass = 'Application_Model_Row_ProgramSession';
    protected $_referenceMap = array(
        'Program' => array(
            'columns' => 'program_id',
            'refTableClass' => 'Application_Model_DbTable_Program',
            'refColumns' => 'id'
        )
    );

    /**
     * Get people checked in to session
     * @param integer $session_id
     * @return array of people checked in
     */
    public function getCheckin($session_id) {
        $this->select->reset();
        $select = $this->select->setIntegrityCheck(false)
                ->from(array('PS' => 'program_session'), array('child_count' => 'COUNT(DISTINCT C.id)'))
                ->joinLeft(array('SF' => 'sign_in_family'), 'SF.program_session_id = PS.id', array('SF.adult_count', 'SF.parent_present'))
                ->joinLeft(array('SC' => 'sign_in_child'), 'SC.sign_in_family_id = SF.id', '')
                ->joinLeft(array('F' => 'family'), 'F.id = SF.family_id', array('guardian_name' => 'F.guardian_full_name', 'family_id' => 'F.id', 'allergies' => 'F.allergies'))
                ->joinLeft(array('C' => 'child'), 'C.id = SC.child_id', array('children_name' => 'GROUP_CONCAT(C.child_full_name SEPARATOR ", ")'))
                ->where('PS.id = ?', $session_id)
                ->group(array('SF.id'))
                ->order(array('guardian_name', 'children_name'));

        return $this->fetchAll($select);
    }

    /**
     * Get latest session
     * @param integer $session_id
     * @return array 
     */
    public function getSession($program_id) {
        $this->select->reset();
        $select = $this->select->setIntegrityCheck(false)
                ->from(array('PS' => 'program_session'), '')
                ->join(array('P' => 'program'), 'P.id = PS.program_id', array('session_id' => 'PS.id'))
                ->where('P.id = ?', $program_id)
                ->where('PS.running = 1')
                ->where('DATE(PS.date) = DATE(NOW())')
                ->order('PS.id DESC')
                ->limit(1);
        return $this->fetchAll($select)->current();
    }

    /**
     * Get program session filter select
     * @param array $programs
     * @param array $families
     * @param date $start
     * @param date $end
     * @param string $sort
     * @param int $dir
     * @return select
     */
    public function getProgramSessionSelect($userSession, $programs, $families, $start, $end, $sort, $dir) {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from(array('PS' => 'program_session'), array(
                    'date_formatted' => 'DATE_FORMAT(PS.date, "%m/%d/%Y")',
                    'date' => 'PS.date',
                    'total' => 'PS.count_total',
                    'adults' => 'PS.count_adult',
                    'children' => 'PS.count_child',
                    'program_id' => 'PS.program_id',
                    'session_id' => 'PS.id'))
                ->join(array('P' => 'program'), 'P.id = PS.program_id', 'P.name')
                ->where('PS.running = 0');

        $db = $this->getAdapter();

        if ($userSession['permission_level'] === '1') {
            if (empty($programs)) {
                $programs = $userSession['programs'];
            }

            $programs = array_intersect($userSession['programs'], $programs);

            if (empty($programs)) {
                $select = $select->where('0');
            }
        }

        if (!empty($programs)) {
            $select = $select->where('PS.program_id IN (?)', $programs);
        }

        if (!empty($families)) {
            $select = $select->where('SF.family_id IN (?)', $families);
        }

        if (!(empty($start) || empty($end))) {
            $start = DateTime::createFromFormat('m/d/Y', $start)->format('Y-m-d');
            $end = DateTime::createFromFormat('m/d/Y', $end)->format('Y-m-d');
            $where = 'PS.date BETWEEN ? AND ?';
            $where = $db->quoteInto($where, $start, 'DATE', 1);
            $where = $db->quoteInto($where, $end, 'DATE', 1);
            $select = $select->where($where);
        }

        if (!empty($sort) && isset($dir)) {
            if ($dir === '0') {
                $dir = 'ASC';
            } else {
                $dir = 'DESC';
            }

            $select = $select->order($sort . ' ' . $dir);
        } else {
            $select = $select->order('PS.id DESC');
        }

        return $select;
    }

}

?>