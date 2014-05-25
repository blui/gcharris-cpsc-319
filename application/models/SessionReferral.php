<?php

/**
 * Model for SessionReferral
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_SessionReferral extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of session_referral, referral and program_session
     */
    private $session_referral_table;
    private $referral_table;
    private $program_session_table;

    function __construct() {
        $this->session_referral_table = new Application_Model_DbTable_SessionReferral();
        $this->referral_table = new Application_Model_DbTable_Referral();
        $this->program_session_table = new Application_Model_DbTable_ProgramSession();
    }

    /**
     * Method create a new referral or edit the current one
     * @param integer $program_session_id
     * @param integer $referral_id
     * @param string $field
     * @param string $value
     * @return table row of new session_referral 
     */
    public function createOrEditSessionReferral($session_id, $referral_id, $field, $value) {
        $session_referral = $this->session_referral_table->find($session_id, $referral_id)->current();

        if (empty($session_referral)) {
            $referralArray = array(
                'program_session_id' => $session_id,
                'referral_id' => $referral_id,
                $field => $value
            );

            return $this->session_referral_table->insert($referralArray);
        } else {
            return $this->editSessionReferral($session_id, $referral_id, $field, $value);
        }
    }

    /**
     * Method that deletes a referral on a program session using their referral id
     * @param integer $session_id
     * @param integer $referral_id
     * return session_referral_table with specific referral deleted
     */
    public function deleteSessionReferral($session_id, $referral_id) {
        return $this->session_referral_table->delete(array(
            'program_session_id = ?' => $session_id,
            'referral_id = ?' => $referral_id
        ));
    }

    /**
     * Method that deletes a referral on a program session using their referral id
     * @param integer $session_id
     * @param integer $referral_id
     * return session_referral_table with specific referral deleted
     */
    public function editSessionReferral($session_id, $referral_id, $field, $value) {
        $data = array($field => $value);
        return $this->session_referral_table->update($data, array(
            'program_session_id = ?' => $session_id,
            'referral_id = ?' => $referral_id
        ));
    }

    /**
     * Method that gets referrals for a program session
     * @param integer $session_id
     * @return array ression referrals
     */
    public function getSessionReferrals($session_id, $program_id) {
        return $this->rowsetToArray($this->session_referral_table->getSessionReferrals($session_id, $program_id));
    }

}

