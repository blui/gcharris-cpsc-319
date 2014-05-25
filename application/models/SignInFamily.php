<?php

/**
 * Model for SignInFamily
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_SignInFamily extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of family, sign_in_family and 
     * program_session
     */
    private $sign_in_family_table;
    private $sign_in_child;

    function __construct() {
        $this->sign_in_family_table = new Application_Model_DbTable_SignInFamily();
        $this->sign_in_child = new Application_Model_SignInChild();
    }

    /**
     * Method to sign in a family to a program session id
     * @param integer $program_session_id
     * @param integer $family_id
     * @return table row of new sign_in_family
     */
    public function signInFamily($session_id, $family_id, $adult_count, $parent_present, $children) {
        $family = $this->sign_in_family_table->getSignInFamilyByID($session_id, $family_id);
        $result = array();

        if (empty($family) && ($children || $adult_count != "0")) {
            $familyArray = array(
                'program_session_id' => $session_id,
                'family_id' => $family_id,
                'adult_count' => $adult_count,
                'parent_present' => $parent_present
            );
            $result = $this->sign_in_family_table->insert($familyArray);
        } else {
            if ($adult_count == "0" && !$children) {
                $result[] = $this->sign_in_child->deleteSignedInChildren($family['id']);
                $result[] = $this->sign_in_family_table->delete(array('id = ?' => $family['id']));
            } else {
                $family->adult_count = $adult_count;
                $family->parent_present = $parent_present;
                $family->save();
            }
            $result = $this->rowsetToArray($family);
        }
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();
        return $result;
    }

    /**
     * Method that deletes a mistake sign in of a family
     * @param integer $family_id
     * @param integer $program_session_id
     * @return updated table with specific sign in deleted
     */
    public function deleteSignInFamily($family_id, $program_session_id) {
        $delete = $this->sign_in_child_family->delete(array('family_id = ?' => $family_id,
            'program_session_id = ?' => $program_session_id));
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();

        return $delete;
    }

    /**
     * Method to get sign in family id
     * @param integer $program_session_id
     * @param integer $family_id
     * @return table row of new sign_in_family
     */
    public function getSignInFamily($session_id, $family_id) {
        return $this->rowsetToArray($this->sign_in_family_table->getSignInFamilyByID($session_id, $family_id));
    }

}

?>