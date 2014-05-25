<?php

/**
 * Row model for table Resource
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Row_SessionResource extends Zend_Db_Table_Row_Abstract {

    private $session_id = null;
    private $resource_id = null;
    private $count = null;

    /**
     * Method to grab resource id from Resource table
     * @return Model_Row_Resource
     * return resource id in Resource table matching sessionResource's resource id
     */
    public function getResourceID() {
        if (!$this->resource_id) {
            $this->resource_id = $this->findDependentRowset('Application_Model_DbTable_Resource')->current();
        }

        return $this->resource_id;
    }

}
