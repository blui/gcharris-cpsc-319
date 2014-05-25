<?php

/**
 * Model for table Referral
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Referral extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of referral and session_referral
     */
    private $referral_table;
    private $session_referral_table;

    function __construct() {
        $this->referral_table = new Application_Model_DbTable_Referral();
        $this->session_referral_table = new Application_Model_DbTable_SessionReferral();
    }

    /**
     * Method creatues new referral given name
     * @param text $name
     * return table with new row for referral with specified name
     */
    public function createReferral($name, $program_id) {
        $data = array('name' => $name,
            'program_id' => $program_id);
        return $this->referral_table->insert($data);
    }

    /**
     * Method returns list of all referrals
     * return list of all available referrals in table
     */
    public function getAllReferrals() {
        return $this->rowsetToArray($this->referral_table->fetchAll());
    }

    /**
     * Method allows editing of a row of referral given referral's id and edit data
     * @param int $referral_id
     * @param text $data
     * return table with updated data for referral id
     */
    public function setReferralData($referral_id, $name) {
        $data = array('name'=>$name);
        return $this->referral_table->update($data, array('id = ?'=>$referral_id));
    }

    /**
     * Method deletes row from table for specified referral id
     * @param int $referral_id
     * return table without deleted referral id row
     */
    public function deleteReferral($referral_id) {
        return $this->referral_table->delete(array('id = ?'=>$referral_id));
    }

}
