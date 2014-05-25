<?php

/**
 * DbTable model for table Child
 * @package    Frp
 * @author     Mark Obad
 * @version    1.0
 */
class Application_Model_DbTable_Child extends Frp_Db_Table_Abstract {

    protected $_name = 'child';
    protected $_primary = 'id';
    protected $_rowClass = 'Application_Model_Row_Child';
    protected $_referenceMap = array(
        'Family' => array(
            'columns' => 'family_id',
            'refTableClass' => 'Application_Model_DbTable_Family',
            'refColumns' => 'id'
        )
    );

    /**
     * Retrieves all children that contain a given name in the
     * concatenation of their first and last name
     * @param varchar $name
     * @return 2 dimensional associative array of all children
     * that have the provided name
     */
    public function getChildrenByName($name) {
        $this->select->from($this);
        $this->select->where("C.child_full_name LIKE ?", '%' . $name . '%');

        return $this->fetchAll($this->select);
    }

}

