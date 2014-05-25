<?php

/**
 * Model for Partner
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Partner extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of partner
     */
    private $partner_table;

    function __construct() {
        $this->partner_table = new Application_Model_DbTable_Partner();
    }

    /**
     * Method create a new partner providing the following parameters
     * @param text $name
     * @param text $email
     * @param text $organization
     * @param text $comments
     * @return table row of new partner 
     * @exception throws exception when an invalid email format is thrown in as a parameter
     */
    public function createPartner($name, $email, $organization, $comments) {

        //Filter and validates emails that are false
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            throw new Frp_Exception_Email($email, Frp_Exception_Email::EMAIL_INVALID);
        }

        //Throw parameters into an array
        $userArray = array(
            'name' => $name,
            'email' => $email,
            'organization' => $organization,
            'comments' => $comments
        );

        return $this->partner_table->insert($userArray);
    }

    /**
     * Method that deletes a partner using their id
     * @param integer $id
     * @return table with specific partner deleted
     */
    public function deletePartner($id) {
        $where = $this->partner_table->getAdapter()->quoteInto('id = ?', $id);
        return $this->partner_table->delete($where);
    }

    /**
     * Method that edits a partner using their id
     * @param integer $id
     * @param text $data
     * @return table with specific partner with data edited
     */
    public function editPartnerInfo($id, $data) {
        if (array_key_exists('email', $data)) {

            //Filter and validates emails that are false
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Frp_Exception_Email($data['email'], Frp_Exception_Email::EMAIL_INVALID);
            }
        }
        $where = $this->partner_table->getAdapter()->quoteInto('id = ?', $id);
        return $this->partner_table->update($data, $where);
    }

    /**
     * Method that returns the list of all partners
     * @return an array of all the partners
     */
    public function getAllPartners() {
        return $this->rowsetToArray($this->partner_table->fetchAll(null, 'organization ASC'));
    }

    /**
     * Method that returns a specifc partner using an id
     * @param text $id
     * @return partner associated with id
     */
    public function getPartnerByID($id) {
        return $this->rowsetToArray($this->partner_table->find($id));
    }

    /**
     * Method that returns a partner associated with given email
     * @param text $email
     * @return partner associated with email
     */
    public function getPartnerByEmail($email) {
        return $this->rowsetToArray($this->partner_table->getPartnerByEmail($email));
    }

    /**
     * Method that returns a partner associated with given name
     * @param text $name
     * @return partner associated with name
     */
    public function getPartnerByName($name) {
        return $this->rowsetToArray($this->partner_table->getPartnerByEmail($name));
    }

    /**
     * Method that returns a partner associated with given organization
     * @param text $organization
     * @return partner associated with organization
     */
    public function getPartnerByOrganization($organization) {
        return $this->rowsetToArray($this->partner_table->getPartnerByOrganization($organization));
    }

    /**
     * Send an email message to the partners supplied
     * @param array $partner_array
     * @param string $subject
     * @param string $message
     * @param string $sender_cc
     * @param array $uploads
     * @return array
     */
    public function sendEmailToPartners($partner_array, $subject, $message, $sender_cc = null, $uploads) {

        $mail_queue = new Application_Model_MailQueue();
        $config = Frp_Config::getFrpConfig();
        $partners = $this->getPartnerByID($partner_array);

        $mail = new Application_Model_Mail($subject, $message, $config['name'], $config['email'], true, $uploads);

        if (isset($sender_cc)) {
            $mail->addRecipient("Staff Member", $sender_cc);
        }

        foreach ($partners as $partner) {
            $mail->addRecipient($partner['name'], $partner['email']);
        }

        return $mail_queue->addToMailQueue($mail);

    }

}
