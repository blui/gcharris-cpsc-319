<?php

/**
 * Row model for table SessionReferral
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Row_SessionReferral extends Zend_Db_Table_Row_Abstract {

    private $session_id = null;
    private $referral_id = null;
    private $count = null;

    /**
     * Method that grabs list of referal id's from table associated to Referral 
     * @return Model_Row_Referrals
     * return referral id matching particular sessionReferral referral id
     */
    public function getReferralID() {
        if (!$this->referral_id) {
            $this->referral_id = $this->findDependentRowset('Application_Model_DbTable_Referral')->current();
        }

        return $this->referral_id;
    }

}
