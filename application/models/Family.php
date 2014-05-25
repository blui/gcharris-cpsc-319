<?php

/**
 * Model for table Family
 * @package    Frp
 * @author     Mark Obad
 * @version    1.0
 */
class Application_Model_Family extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of family and child
     */
    private $family_table;
    private $child_table;

    function __construct() {
        $this->family_table = new Application_Model_DbTable_Family();
        $this->child_table = new Application_Model_DbTable_Child();
    }

    /**
     * Creates a family with the provided data
     * @param array $family_data
     * @return array
     */
    public function createFamily($data) {

        if (isset($data['guardian_email'])) {

            //Filter and validates emails that are false
            if (!filter_var($data['guardian_email'], FILTER_VALIDATE_EMAIL)) {
                throw new Frp_Exception_Email($data['guardian_email'], Frp_Exception_Email::EMAIL_INVALID);
            }
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $children = $data['children'];
            unset($data['children']);

            $result['family'] = $this->family_table->insert($data);

            if (!empty($children)) {
                // create children
                foreach ($children as $child) {
                    if (empty($child['first_name'])) {
                        continue;
                    }

                    $child['first_name'] = filter_var($child['first_name'], FILTER_SANITIZE_STRING);
                    $child['last_name'] = filter_var($child['last_name'], FILTER_SANITIZE_STRING);

                    if (!empty($child['birthday'])) {
                        $child['birthday'] = DateTime::createFromFormat('m/d/Y', $child['birthday'])->format('Y-m-d');
                    } else {
                        unset($child['birthday']);
                    }

                    $child['registration_date'] = date("Y/m/d");
                    $child['family_id'] = $result['family']['id'];
                    $result['children'][] = $this->child_table->insert($child);
                }
            }

            $db->commit();

            $frpCache = new Frp_Cache();
            $frpCache->getCache()->clean();

            return $result;
        } catch (Exception $e) {
            $db->rollBack();
            print_r($e->getMessage());
        }
    }

    /**
     * Gets the children and the guardian of the family
     * @param integer $family_id
     * @return array $members
     */
    public function getFamilyMembers($family_id) {
        $members = array();
        $family = $this->family_table->find($family_id)->current();
        if (!empty($family['first_attendance_date'])) {
            $family['first_attendance_date'] = DateTime::createFromFormat('Y-m-d', $family['first_attendance_date'])->format('m/d/Y');
        }
        $members['family'] = $this->rowsetToArray($family);
        $members['children'] = $this->rowsetToArray($this->family_table->getChildrenByFamily($family_id));

        return $members;
    }

    /**
     * Get all families filter paginator
     * @param string $q
     * @param array $programs
     * @param array $languages
     * @param array $countries
     * @param date $start
     * @param date $end
     * @param string $sort
     * @param int $dir
     * @return paginator
     */
    public function getFamiliesPaginator($userSession, $q, $programs, $languages, $countries, $start, $end, $sort, $dir) {
        $frpCache = new Frp_Cache();
        $cache = $frpCache->getCache();
        Zend_Paginator::setCache($cache);
        $adapter = new Zend_Paginator_Adapter_DbSelect($this->family_table->
                        getAllFamiliesSelect($userSession, $q, $programs, $languages, $countries, $start, $end, $sort, $dir));
        $adapter->setRowCount($this->family_table->
                        getAllFamiliesSelect($userSession, $q, $programs, $languages, $countries, $start, $end, $sort, $dir, TRUE));

        return new Zend_Paginator($adapter);
    }

    /**
     * Get children associated to family
     * @param type $family_id
     * @return array $children
     */
    public function getChildren($family_id) {
        $family = $this->family_table->find($family_id)->current();
        return $this->rowsetToArray($family->getChildren());
    }

    /**
     * Removes a family given a family ID
     * @param integer $family_id
     * @return array
     */
    public function deleteFamily($family_id) {
        $delete = $this->family_table->delete(array('id = ?' => $family_id));
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();
        return $delete;
    }

    /**
     * Updates a family with the provided data
     * @param integer $family_id
     * @param array $data
     * @return array
     */
    public function updateFamily($family_id, $data) {
        if (isset($data['guardian_email'])) {

            //Filter and validates emails that are false
            if (!filter_var($data['guardian_email'], FILTER_VALIDATE_EMAIL)) {
                throw new Frp_Exception_Email($data['guardian_email'], Frp_Exception_Email::EMAIL_INVALID);
            }
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $children = $data['children'];
            unset($data['children']);

            $result['family'] = $this->family_table->update($data, array('id = ?' => $family_id));

            $dbchild_ids = array();
            foreach ($this->getChildren($family_id) as $cid) {
                $dbchild_ids[] = $cid['id'];
            }

            if (!empty($children)) {
                foreach ($children as $child) {
                    if (!empty($child['birthday'])) {
                        $child['birthday'] = DateTime::createFromFormat('m/d/Y', $child['birthday'])->format('Y-m-d');
                    } else {
                        $child['birthday'] = NULL;
                    }

                    if (!empty($child['id'])) {
                        $id = $child['id'];
                        unset($child['id']);
                        $result['children'][] = $this->child_table->update($child, array('id = ?' => $id));
                        unset($dbchild_ids[array_search($id, $dbchild_ids)]);
                    } else {
                        if (empty($child['first_name'])) {
                            continue;
                        }
                        unset($child['id']);
                        $child['registration_date'] = date("Y/m/d");
                        $child['family_id'] = $family_id;
                        $result['children'][] = $this->child_table->insert($child);
                    }
                }
            }

            foreach ($dbchild_ids as $cid) {
                $this->child_table->delete(array('id = ?' => $cid));
            }

            $db->commit();

            $frpCache = new Frp_Cache();
            $frpCache->getCache()->clean();

            return $result;
        } catch (Exception $e) {
            $db->rollBack();
            print_r($e->getMessage());
        }
    }

    /**
     * Send an email message to the participants supplied
     * @param array $partner_array
     * @param string $subject
     * @param string $message
     * @return array
     */
    public function sendEmailToParticipants($participants_array, $subject, $message, $sender_cc = null, $uploads) {

        $mail_queue = new Application_Model_MailQueue();
        $config = Frp_Config::getFrpConfig();
        $families = $this->family_table->find($participants_array);

        $mail = new Application_Model_Mail($subject, $message, $config['name'], $config['email'], true, $uploads);

        if (isset($sender_cc)) {
            $mail->addRecipient("Staff Member", $sender_cc);
        }

        foreach ($families as $family) {

            $name = $family['guardian_first_name'] . " " . $family['guardian_last_name'];
            $mail->addRecipient($name, $family['guardian_email']);
        }

        $mail_queue->addToMailQueue($mail);
    }

    /**
     * Get phone search paginator
     * @param string $phone_number
     * @return paginator
     */
    public function getPhonePaginator($phone_number) {
        $frpCache = new Frp_Cache();
        $cache = $frpCache->getCache();
        Zend_Paginator::setCache($cache);
        $adapter = new Zend_Paginator_Adapter_DbSelect($this->family_table->getFamiliesByPhone($phone_number));
        return new Zend_Paginator($adapter);
    }

    /**
     * Get name search paginator
     * @param string $name
     * @return paginator
     */
    public function getNamePaginator($name) {
        $frpCache = new Frp_Cache();
        $cache = $frpCache->getCache();
        Zend_Paginator::setCache($cache);
        $adapter = new Zend_Paginator_Adapter_DbSelect($this->family_table->getFamiliesByName($name));
        return new Zend_Paginator($adapter);
    }

    /**
     * Checks if a phone number exists
     * @param string $phone_number
     * @return family
     */
    public function phoneExists($phone_number) {
        $family = $this->family_table->fetchAll($this->family_table->select()->from($this->family_table)->where('phone_number = ?', $phone_number))->current();
        return $this->rowsetToArray($family);
    }

    /**
     * Checks if an email exists
     * @param string $email
     * @return family
     */
    public function emailExists($email) {
        $family = $this->family_table->fetchAll($this->family_table->select()->from($this->family_table)->where('guardian_email = ?', $email))->current();
        return $this->rowsetToArray($family);
    }

}

