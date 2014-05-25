<?php

/**
 * Row model for table Resource
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Row_Resource extends Zend_Db_Table_Row_Abstract {

    private $sessionResource = null;

    /**
     * Method grabs sessionResource's session id
     * @return Model_Row_SessionResource
     * return session id of sessionResouce
     */
    public function getSessionID() {
        if (!$this->sessionResource) {
            $this->sessionResource = $this->findParentRow('Application_Model_DbTable_SessionResource');
        }

        return $this->sessionResource;
    }

    /**
     * Method grabs count(*) from sessionResource
     * @return Model_Row_SessionResource
     * return count(*) from sessionResource
     */
    public function getResourceCount() {
        if (!$this->sessionResource) {
            $this->sessionResource = $this->findParentRow('Application_Model_DbTable_SessionResource');
        }

        return $this->sessionResource;
    }

}
