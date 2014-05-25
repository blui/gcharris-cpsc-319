<?php

/**
 * Row model for table Child
 * @package    Frp
 * @author     Mark Obad
 * @version    1.0
 */
class Application_Model_Row_Child extends Zend_Db_Table_Row_Abstract {

    private $family = null;

    /**
     * Gets Family of Child
     * @return Application_Model_DbTable_Family
     */
    public function getFamily() {
        if (!$this->family) {
            $this->family = $this->findParentRow('Application_Model_DbTable_Family')->current();
        }

        return $this->family;
    }

}
