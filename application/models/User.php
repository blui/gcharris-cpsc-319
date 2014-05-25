
<?php

/**
 * Model for User
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_User extends Frp_Model_Abstract {

    const USER_DIRECTOR = 0;
    const USER_COORDINATOR = 1;

    /**
     * Stores an instance of the user db table
     * @var Application_Model_DbTable_User 
     */
    private $user_table;

    /**
     * Stores an instance of the staff db table
     * @var Application_Model_DbTable_Staff 
     */
    private $staff_table;

    function __construct() {
        $this->user_table = new Application_Model_DbTable_User();
        $this->staff_table = new Application_Model_DbTable_Staff();
        $this->program_staff_table = new Application_Model_DbTable_ProgramStaff();
    }

    /**
     * Return a user based on their email
     * @param string $email
     * @return array
     */
    public function getUserFromEmail($email) {
        $staff = $this->staff_table->getStaffByEmail($email);

        if ($staff instanceof Application_Model_Row_Staff) {
            $user = $staff->getUser();
            return $this->rowsetToArray($user);
        } else {
            return array();
        }
    }

    /**
     * Given an array of data, add a new user to the database
     * @param integer $permission_level
     * @return array
     * @throws Frp_Exception_User
     */
    public function createUser($first_name, $last_name, $email, $permission_level, $program_ids) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        $staff_table = new Application_Model_Staff();
        $staff = $staff_table->createStaffMember($first_name, $last_name, $email, Application_Model_Staff::FULLTIME);

        $staff_id = $staff['id'];

        //If we are given an invalid permission level
        if ($permission_level != self::USER_COORDINATOR && $permission_level != self::USER_DIRECTOR) {
            throw new Frp_Exception_User($permission_level, Frp_Exception_User::USER_PERMISSION_LEVEL_INVALID);
        }

        //Create a salt for hashing the password
        $pw_salt = Frp_Hashing::generateSalt();

        //Generate a random password
        $password = Frp_Hashing::generatePassword();

        $pw_hash = crypt($password, $pw_salt);

        $userArray = array(
            'staff_id' => $staff_id,
            'pass_hash' => $pw_hash,
            'pass_salt' => $pw_salt,
            'permission_level' => $permission_level
        );

        $result = $this->user_table->insert($userArray);

        $result['password'] = $password;

        // Add user to specified programs
        if ($permission_level == self::USER_COORDINATOR && is_array($program_ids)) {
            foreach ($program_ids as $program_id) {
                $data = array(
                    'staff_id' => $staff_id,
                    'program_id' => $program_id
                );

                $this->program_staff_table->insert($data);
            }
        }


        //We only want to send the email if the user has been successfully created
        if (array_key_exists("staff_id", $result)) {
            $this->sendAddUserEmail($staff_id, $password);
            $db->commit();
            return $result;
        } else {
            $db->rollBack();
            throw new Frp_Exception_User($staff_id, Frp_Exception_User::USER_CREATE_ERROR);
        }
    }

    /**
     * Change the password of a user
     * @param integer $staff_id
     * @param string $new_password
     * @return array
     */
    public function changePassword($staff_id, $new_password) {

        $user = $this->getUserFromStaffId($staff_id);

        if (array_key_exists('pass_salt', $user)) {
            $pw_hash = crypt($new_password, $user['pass_salt']);

            $data = array(
                'pass_hash' => $pw_hash
            );

            return $this->user_table->update($data, array('staff_id = ?' => $staff_id));
        } else {
            throw new Frp_Exception_User($staff_id, Frp_Exception_User::NO_USER_FOR_STAFF_ID);
        }
    }

    /**
     * Change the permission level of a user
     * @param integer $staff_id
     * @param integer $permission_level
     * @return array
     */
    public function changePermissionLevel($staff_id, $permission_level) {

        $data = array(
            'permission_level' => $permission_level
        );

        return $this->user_table->update($data, array('staff_id = ?' => $staff_id));
    }

    /**
     * Return all fulltime accounts
     * @return array
     */
    public function getFulltimeAccounts() {
        return $this->rowsetToArray($this->user_table->getFulltimeAccounts());
    }

    /**
     * Given a staff_id return the fulltime account
     * @param integer $staff_id
     * @return array
     */
    public function getFulltimeAccountByID($staff_id) {
        return $this->rowsetToArray($this->user_table->getFulltimeAccountByID($staff_id)->current());
    }

    /**
     * Return all coordinator accounts
     * @return array
     */
    public function getCoordinatorAccounts() {
        return $this->rowsetToArray($this->user_table->getCoordinatorAccounts());
    }

    /**
     * 
     * @param type $staff_id
     * @param type $data
     * @param type $permission_level
     * @param type $program_ids
     * @return type
     * @throws Frp_Exception_User
     */
    public function editAccount($staff_id, $data, $permission_level = NULL, $program_ids = NULL) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $userArray = array();

            $user = $this->getUserFromStaffId($staff_id);
            
            if (isset($data['my_account'])) {
                $my_account = TRUE;
                unset($data['my_account']);
            } else {
                $my_account = FALSE;
            }
            
            if (isset($data['password'])) {
                $userArray['pass_hash'] = crypt($data['password'], $user['pass_salt']);
                unset($data['password']);
            }

            $staff_table = new Application_Model_Staff();
            $staff_table->editStaffMemberInfo($staff_id, $data);

            if (isset($permission_level)) {
                //If we are given an invalid permission level
                if ($permission_level != self::USER_COORDINATOR && $permission_level != self::USER_DIRECTOR) {
                    throw new Frp_Exception_User($permission_level, Frp_Exception_User::USER_PERMISSION_LEVEL_INVALID);
                }

                $userArray['permission_level'] = $permission_level;
            }
            //Fixed for now... refactor
            if (count($userArray) > 0) {
                $usertbl = new Application_Model_DbTable_User();
                $result = $usertbl->update($userArray, array('staff_id = ?' => $staff_id));
            }

            if (!$my_account && ($permission_level == self::USER_COORDINATOR || $permission_level != $user['permission_level'])) {
                $this->program_staff_table->delete(array('staff_id = ?' => $staff_id));

                // Add user to specified programs
                if ($permission_level == self::USER_COORDINATOR && is_array($program_ids)) {
                    foreach ($program_ids as $program_id) {
                        $data = array(
                            'staff_id' => $staff_id,
                            'program_id' => $program_id
                        );

                        $this->program_staff_table->insert($data);
                    }
                }
            }

            $db->commit();

            return true;
        } catch (Exception $e) {

            $db->rollBack();
            print_r($e->getMessage());
        }
    }

    /**
     * Get a user from a staff id
     * @param integer $staff_id
     * @return array 
     */
    public function getUserFromStaffId($staff_id) {
        return $this->rowsetToArray($this->user_table->find($staff_id)->current());
    }

    /**
     * Sends a user an email when their user is created
     * @param integer $staff_id
     * @return array 
     */
    private function sendAddUserEmail($staff_id, $password) {
        $staff = new Application_Model_Staff();
        $staff_member = $staff->getStaff($staff_id);
        $mail = new Frp_Mail();

        $config = Frp_Config::getFrpConfig();

        $params = array(
            'to' => $staff_member['email'],
            'password' => $password,
            'name' => $staff_member['first_name'] . " " . $staff_member['last_name'],
            'frp_url' => $config['url'],
            'frp_name' => $config['name']);

        $mail->addTo($staff_member['email']);

        $mail->setFrom($config['email'], $config['name']);
        $mail->setSubject("An account has been created for you");

        $mail->setBodyByView('new_user', $params);

        return $mail->send();
    }

}
