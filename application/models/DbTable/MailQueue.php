<?php

/**
 * DbTable model for table MailQueue
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_DbTable_MailQueue extends Frp_Db_Table_Abstract {

    protected $_name = 'mail_queue';
    protected $_primary = 'id';

    /**
     * Since the mail messages could be quite alrge, only grab the mail id's 
     * @return table 
     */
    public function getMessageObject($id) {
        $this->select->reset();
        $this->select->where('id = ?', $id);
        $this->select->from($this, array('message_obj'));
        return $this->fetchRow($this->select);
    }

    /**
     * Grabs all the ids associated with messages
     * @return table
     */
    public function getMessageIds() {
        $this->select->reset();
        $this->select->from($this, array('id'));
        return $this->fetchAll($this->select);
    }

    /**
     * Grabs a number returned in queue for mail?
     * @return table
     */
    public function getNumberInQueue() {
        $this->select->reset();
        $this->select->from($this, 'count(*) as COUNT');
        return $this->fetchRow($this->select);
    }

}