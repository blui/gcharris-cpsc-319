<?php

/**
 * Row model for table Referral
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Row_Referral extends Zend_Db_Table_Row_Abstract {

    private $session_referral = null;

    /**
     * Method that grabs session id
     * @return Model_Row_SessionReferrals
     * Extra note: getAllReferralsFromSession($session_id)->count()
     * return session id associated with referral
     */
    public function getSessionID() {
        if (!$this->session_referral) {
            $this->session_referral = $this->findParentRow('Application_Model_DbTable_SessionReferral');
        }

        return $this->session_referral;
    }

    /**
     * Method that grabs parent class' referral count(*)
     * @return Model_Row_SessionReferrals
     * return count(*) associated with session the referral belongs in
     */
    public function getReferralCount() {
        if (!$this->session_referral) {
            $this->session_referral = $this->findParentRow('Application_Model_DbTable_SessionReferral');
        }

        return $this->session_referral;
    }

}
