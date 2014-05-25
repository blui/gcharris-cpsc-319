<?php

/**
 * Model for SignInChild
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_SignInChild extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of child, sign_in_child and 
     * program_session
     */
    private $child_table;
    private $sign_in_child_table;
    private $program_session_table;

    function __construct() {
        $this->child_table = new Application_Model_DbTable_Child();
        $this->sign_in_child_table = new Application_Model_DbTable_SignInChild();
        $this->program_session_table = new Application_Model_DbTable_ProgramSession();
    }

    /**
     * Delete signed in children for program session and family
     * @param integer $session_id
     * @param integer $family_id
     * @return array affected children
     */
    public function deleteSignedInChildren($sign_in_family_id) {
        $delete = $this->sign_in_child_table->deleteSignedInChildren($sign_in_family_id);
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();
        return $delete;
    }

    /**
     * Method to sign in a child to a program session id
     * @param integer $session_id
     * @param integer $child_id
     * @return table row of new sign_in_child 
     */
    public function signInChildren($sign_in_family_id, $child_ids) {
        $result = array();
        $result[] = $this->deleteSignedInChildren($sign_in_family_id);
        if (empty($child_ids)) {
            return $result;
        }

        foreach ($child_ids as $child_id) {
            $childArray = array(
                'sign_in_family_id' => $sign_in_family_id,
                'child_id' => $child_id
            );

            $result[] = $this->sign_in_child_table->insert($childArray);
        }

        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();
        return $result;
    }

    /**
     * Method that deletes a mistake sign in of a child???
     * @param integer $child_id
     * @param integer $program_session_id
     * return updated table with specific sign in deleted
     */
    public function deleteSignInChild($child_id, $program_session_id) {
        if (($where = $this->sign_in_child_table->getAdapter()->quoteInto('child_id = ?', $child_id)) && ($where = $this->sign_in_child_table->getAdapter()->quoteInto('program_session_id = ?', $program_session_id))) {
            $delete = $this->sign_in_child_table->delete($where);
            $frpCache = new Frp_Cache();
            $frpCache->getCache()->clean();
            return $delete;
        }
    }

    /**
     * Method to get sign_in_children
     * @param integer $sign_in_family_id
     * @return array signed in children
     */
    public function getSignInChildren($sign_in_family_id) {
        return $this->rowsetToArray($this->sign_in_child_table->getChildrenByID($sign_in_family_id));
    }

}

?>