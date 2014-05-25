<?php

/**
 * DbTable model for table SignInFamily
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_DbTable_SignInFamily extends Frp_Db_Table_Abstract {

    protected $_name = 'sign_in_family';
    protected $_primary = array('id', 'program_session_id', 'family_id');
    protected $_rowClass = 'Application_Model_Row_SignInFamily';
    protected $_referenceMap = array(
        'ProgramSession' => array(
            'columns' => 'program_session_id',
            'refTableClass' => 'Application_Model_DbTable_ProgramSession',
            'refColumns' => 'id'
        ),
        'Family' => array(
            'columns' => 'family_id',
            'refTableClass' => 'Application_Model_DbTable_Family',
            'refColumns' => 'id'
        )
    );

    /**
     * Method to get sign in family id
     * @param integer $program_session_id
     * @param integer $family_id
     * @return table row of new sign_in_family
     */
    public function getSignInFamilyByID($session_id, $family_id) {
        $this->select->reset();

        $select = $this->select
                ->from('sign_in_family')
                ->where('program_session_id = ?', $session_id)
                ->where('family_id = ?', $family_id);

        return $this->fetchAll($select)->current();
    }

}

?>