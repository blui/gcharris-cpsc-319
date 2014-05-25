<?php

/**
 * Row model for table Referral
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Row_Program extends Zend_Db_Table_Row_Abstract {

    private $referrals = null;
    private $resources = null;

    /**
     * Gets referrals associated to program
     * @return array Application_Model_DbTable_Referral
     */
    public function getReferrals() {
        if (!$this->referrals) {
            $this->referrals = $this->findDependentRowset('Application_Model_DbTable_Referral');
        }

        return $this->referrals;
    }

    /**
     * Gets resources associated to program
     * @return array Application_Model_DbTable_Referral
     */
    public function getResources() {
        if (!$this->resources) {
            $this->resources = $this->findDependentRowset('Application_Model_DbTable_Resource');
        }

        return $this->resources;
    }
}
