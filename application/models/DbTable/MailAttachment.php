<?php

/**
 * DbTable model for table MailQueue
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_DbTable_MailAttachment extends Frp_Db_Table_Abstract {

    protected $_name = 'mail_attachment';
    protected $_primary = 'id';
    var $where;

    /**
     * Since the mail messages could be quite alrge, only grab the mail id's 
     * @return table 
     */
    public function getAttachmentInfo($id) {
        $this->select->reset();
        $this->where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->select->where($this->where);
        $this->select->from($this, array('type', 'name'));
        return $this->fetchRow($this->select);
    }
    /**
     * Set the select to within a certain number of days past
     * @param integer $days
     */
    public function selectDaysOld($days){
        $this->select->reset();
        $this->where = $this->getAdapter()->quoteInto("time < now() - interval ? day", $days);
        $this->select->where($this->where);
    }
    /**
     * Return rows based on the current select
     * @return rowset
     */
    public function getSelected(){
        return $this->fetchAll($this->select);
    }
    /**
     * Delete all attachments more than $days old
     * @return array
     */
    public function deleteSelected() {
        return $this->delete($this->where);
    }

}
