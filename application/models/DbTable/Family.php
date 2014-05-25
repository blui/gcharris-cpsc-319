<?php

/**
 * DbTable model for table Family
 * @package    Frp
 * @author     Mark Obad
 * @version    1.0
 */
class Application_Model_DbTable_Family extends Frp_Db_Table_Abstract {

    protected $_name = 'family';
    protected $_primary = 'id';
    protected $_rowClass = 'Application_Model_Row_Family';

    /**
     * Retrieves all families that contain a given name in the
     * concatenation of their first and last name
     * @param varchar $name
     * @return 2 dimensional associative array of all children
     * that have the provided name
     */
    public function getFamiliesByName($name) {
        $db = $this->getAdapter();
        $this->select->reset();
        $famWhere = $db->quoteInto("FSE.guardian_full_name LIKE ? OR FSE.guardian_last_name LIKE ?", $name . '%');
        $childWhere = $db->quoteInto("CSE.child_full_name LIKE ? OR CSE.last_name LIKE ?", $name . '%');
        $famSel = $this->select()->setIntegrityCheck(false)
                ->from(array('FSE' => 'family'), array('family_id' => 'FSE.id'))
                ->where($famWhere);
        $this->select->reset();
        $childSel = $this->select()->setIntegrityCheck(false)
                ->from(array('CSE' => 'child'), array('family_id' => 'CSE.family_id'))
                ->where($childWhere);
        $this->select->reset();
        $unionSel = $this->select()->setIntegrityCheck(false)
                ->union(array($famSel, $childSel));

        $this->select->reset();
        $select = $this->select()->setIntegrityCheck(false)
                ->from(array('SE' => new Zend_Db_Expr('(' . $unionSel . ')')), array('family_id' => 'F.id',
                    'guardian_name' => 'F.guardian_full_name',
                    'F.phone_number',
                    'children_name' => '(SELECT GROUP_CONCAT(child.child_full_name SEPARATOR ", ") FROM child WHERE child.family_id = SE.family_id GROUP BY child.family_id)'))
                ->join(array('F' => 'family'), 'F.id = SE.family_id', '')
                ->order('F.id DESC');

        return $select;
    }

    /**
     * Get family with matching phone number
     * @param varchar $phonenumber
     * @return array of families and children with matching phones numbers
     */
    public function getFamiliesByPhone($phone_number) {
        $this->select->reset();
        $select = $this->select->setIntegrityCheck(false)
                ->from(array('F' => 'family'), array('family_id' => 'F.id',
                    'guardian_name' => 'F.guardian_full_name',
                    'phone_number',
                    'children_name' => '(SELECT GROUP_CONCAT(child.child_full_name SEPARATOR ", ") FROM child WHERE child.family_id = F.id GROUP BY child.family_id)'))
                ->where('F.phone_number LIKE ?', '%' . $phone_number . '%')
                ->order('F.id DESC');

        return $select;
    }

    /**
     * Get children by family
     * @param integer $family_id
     * @return array children
     */
    public function getChildrenByFamily($family_id) {
        $this->select->reset();
        $select = $this->select->setIntegrityCheck(false)
                ->from(array('C' => 'child'), array('*', 'age' =>
                    '(YEAR(CURDATE())-YEAR(birthday)) - (RIGHT(CURDATE(),5)<RIGHT(birthday,5))',
                    'birthday' => 'IFNULL(DATE_FORMAT(birthday, "%m/%d/%Y"),"")'))
                ->where('C.family_id = ?', $family_id);

        return $this->fetchAll($select);
    }

    /**
     * Get all families filter select
     * @param string $q
     * @param array $programs
     * @param array $languages
     * @param array $countries
     * @param date $start
     * @param date $end
     * @param string $sort
     * @param int $dir
     * @return select
     */
    public function getAllFamiliesSelect($userSession, $q, $programs, $languages, $countries, $start, $end, $sort, $dir, $count = FALSE) {
        $db = $this->getAdapter();

        if (!$count) {
            $selectCols = array(
                'f.id',
                'f.phone_number',
                'f.guardian_first_name',
                'f.guardian_last_name',
                'f.guardian_email',
                'language' => 'f.guardian_first_lang_name',
                'country' => 'f.guardian_origin_country_name',
                'programs' => '(SELECT GROUP_CONCAT(program.name SEPARATOR ", ") FROM program JOIN program_session ON program_session.program_id = program.id JOIN sign_in_family_first_visit ON sign_in_family_first_visit.program_session_id = program_session.id JOIN sign_in_family ON sign_in_family.id = sign_in_family_first_visit.sign_in_family_id WHERE sign_in_family.family_id = f.id ORDER BY program.name)',
                'children' => '(SELECT GROUP_CONCAT(child.child_full_name SEPARATOR ", ") FROM child WHERE child.family_id = f.id GROUP BY child.family_id ORDER BY child.first_name)',
                'registration_date_formatted' => 'DATE_FORMAT(f.registration_date, "%m/%d/%Y")',
                'registration_date' => 'f.registration_date'
            );
        } else {
            $selectCols = array(Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'COUNT(DISTINCT f.id)');
        }

        $select = $this->select()
                ->setIntegrityCheck(false);

        if (!empty($q)) {
            if (!ctype_digit(substr($q, 0, 1))) {

                $famWhere = $db->quoteInto("FSE.guardian_full_name LIKE ? OR FSE.guardian_last_name LIKE ?", $q . '%');
                $childWhere = $db->quoteInto("CSE.child_full_name LIKE ? OR CSE.last_name LIKE ?", $q . '%');
                $famSel = $this->select()->setIntegrityCheck(false)
                        ->from(array('FSE' => 'family'), array('family_id' => 'FSE.id'))
                        ->where($famWhere);

                $childSel = $this->select()->setIntegrityCheck(false)
                        ->from(array('CSE' => 'child'), array('family_id' => 'CSE.family_id'))
                        ->where($childWhere);

                $unionSel = $this->select()->setIntegrityCheck(false)
                        ->union(array($famSel, $childSel));

                $select = $select->from(array('SE' => new Zend_Db_Expr('(' . $unionSel . ')')), $selectCols)
                        ->join(array('f' => 'family'), 'f.id = SE.family_id', '');
            } else {
                $q = preg_replace("/[^\d]/", "", $q);
                $select = $select->from(array('f' => 'family'), $selectCols)
                        ->where('f.phone_number LIKE ?', '%' . $q . '%');
            }
        } else {
            $select = $select->from(array('f' => 'family'), $selectCols);
        }

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
            if (!$count) {
                $select = $select->where('EXISTS(SELECT ps.program_id FROM `sign_in_family` AS `sif`
 JOIN `program_session` AS `ps` ON ps.id = sif.program_session_id AND ps.program_id IN (?) WHERE sif.family_id = f.id)', $programs);
            } else {
                $select = $select->join(array('sif' => 'sign_in_family'), 'sif.family_id = f.id', '')
                        ->join(array('ps' => 'program_session'), 'ps.id = sif.program_session_id', '')
                        ->where('ps.program_id IN (?)', $programs);
            }
        }

        if (!empty($languages)) {
            $select = $select->where('f.guardian_first_lang IN (?)', $languages);
        }

        if (!empty($countries)) {
            $select = $select->where('f.guardian_origin_country IN (?)', $countries);
        }

        if (!(empty($start) || empty($end))) {
            $start = DateTime::createFromFormat('m/d/Y', $start)->format('Y-m-d');
            $end = DateTime::createFromFormat('m/d/Y', $end)->format('Y-m-d');
            $where = 'f.registration_date BETWEEN ? AND ?';
            $where = $db->quoteInto($where, $start, 'DATE', 1);
            $where = $db->quoteInto($where, $end, 'DATE', 1);
            $select = $select->where($where);
        }

        if (!$count) {
            if (!empty($sort) && isset($dir)) {
                if ($dir === "0") {
                    $dir = "ASC";
                } else {
                    $dir = "DESC";
                }

                $select = $select->order($sort . " " . $dir);
            } else {
                $select = $select->order('f.id DESC');
            }
        }

        return $select;
    }

}

