<?php

/**
 * DbTable model for table SessionReferral
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_DbTable_SessionReferral extends Frp_Db_Table_Abstract {

    protected $_name = 'session_referral';
    protected $_primary = array('program_session_id', 'referral_id');
    protected $_rowClass = 'Application_Model_Row_SessionReferral';
    protected $_referenceMap = array(
        'ProgramSession' => array(
            'columns' => 'program_session_id',
            'refTableClass' => 'Application_Model_DbTable_ProgramSession',
            'refColumns' => 'id'
        ),
        'Referral' => array(
            'columns' => 'referral_id',
            'refTableClass' => 'Application_Model_DbTable_Referral',
            'refColumns' => 'id'
        )
    );

    /**
     * Method that gets referrals for a program session
     * @param integer $session_id
     * @return array session referrals
     */
    public function getSessionReferrals($session_id, $program_id) {
        $this->select->reset();
        $where = $this->getAdapter()->quoteInto('SR.referral_id = R.id AND SR.program_session_id = ?', $session_id);
        $select = $this->select->setIntegrityCheck(false)
                ->from(array('R' => 'referral'))
                ->joinLeft(array('SR' => 'session_referral'), $where)
                ->where('R.program_id = ?', $program_id)
                ->order('R.name');
        return $this->fetchAll($select);
    }

}

