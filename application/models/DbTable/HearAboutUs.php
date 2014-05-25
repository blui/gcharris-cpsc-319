<?php

/**
 * DbTable model for table HearAboutUs
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_DbTable_HearAboutUs extends Frp_Db_Table_Abstract {

    protected $_name = 'hear_about_us';
    protected $_primary = 'id';
    protected $_referenceMap = array(
        'Referral' => array(
            'columns' => 'id',
            'refTableClass' => 'Application_Model_DbTable_Family',
            'refColumns' => 'hear_about_us'
        )
    );

    /**
     * Method returns text "hear about us" given id
     * @param integer $id
     * @return row of detailing "hear about us" associated with given id
     */
    public function getHearAboutUsByID($id) {
        $this->select->from($this);
        $this->select->where('id = ?', $id);
        return $this->fetchRow($this->select);
    }

}

