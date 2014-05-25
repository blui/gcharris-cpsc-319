<?php

/**
 * Model for Mailing. Serializes a message, stores it in the db, and unserializes it
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_MailQueue extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of mail_queue
     */
    private $mail_queue;

    function __construct() {
        $this->mail_queue = new Application_Model_DbTable_MailQueue();
        $this->mail_attachment = new Application_Model_DbTable_MailAttachment();
    }

    /**
     * Get the number of mail messages in the queue
     * @return int size of queue
     */
    public function getSize() {
        $array = $this->mail_queue->getNumberInQueue();
        return $array['COUNT'];
    }

    /**
     * Delete old attachments in both database and filesystem
     * @param type $days
     * @return array
     */
    public function deleteDaysOld($days) {
        $path = APPLICATION_PATH . "/../tmp/";
        $this->mail_attachment->selectDaysOld($days);
        $files = $this->rowsetToArray($this->mail_attachment->getSelected());
        $this->mail_attachment->deleteSelected();
        
        foreach($files as $file){
            unlink($path . $file['id']);
        }
        
    }

    /**
     * 
     * @param type $name
     * @param type $type
     * @param type $file
     * @return type
     */
    public function addAttachmentToMailQueue($name, $type) {
        $data = array(
            'type' => $type,
            'name' => $name);
        return $this->mail_attachment->insert($data);
        
    }

    /**
     * 
     * @param Frp_Mail $mail_object
     * @return array
     * @throws Frp_Exception_Email
     */
    public function addToMailQueue($mail_object) {
        if (!$mail_object instanceof Application_Model_Mail) {
            throw new Frp_Exception_Email($mail_object, Frp_Exception_Email::INVALID_EMAIL_OBJECT);
        }
        $serialized = serialize($mail_object);
        return $this->mail_queue->insert(array('message_obj' => $serialized));
    }

    /**
     * When executed loads all mail messages from the db queue and loops through 
     * them, sending the generated mails
     * Note: This function should typically only be run by a cron job
     * @return int total number of mails sent 
     */
    public function sendMailInQueue() {

        //Only run the rest of we actually have mail to send
        $sent = 0;
        if ($this->getSize() > 0) {
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $message_ids = $this->mail_queue->getMessageIds();

            foreach ($message_ids as $message_id) {
                $db->beginTransaction();
                $row = $this->mail_queue->getMessageObject($message_id->id);
                $this->deleteMessage($message_id->id);
                $db->commit();
                $mail = unserialize($row->message_obj);
                $sent = $mail->buildAndSendMail();
            }
        }
        return $sent;
    }

    /**
     * Delete a given message
     * @param int $id
     * @return array
     */
    public function deleteMessage($id) {
        $where = $this->mail_queue->getAdapter()->quoteInto('id = ?', $id);
        return $this->mail_queue->delete($where);
    }

}
