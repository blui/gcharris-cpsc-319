<?php

/**
 * Row model for table Family
 * @package    Frp
 * @author     Mark Obad
 * @version    1.0
 */
class Application_Model_Row_Family extends Zend_Db_Table_Row_Abstract {

    private $children = null;

    /**
     * Gets children of family
     * @return Application_Model_DbTable_Child
     */
    public function getChildren() {
        if (!$this->children) {
            $this->children = $this->findDependentRowset('Application_Model_DbTable_Child');
        }

        return $this->children;
    }

}
