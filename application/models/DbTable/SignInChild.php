<?php

/**
 * DbTable model for table SignInChild
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_DbTable_SignInChild extends Frp_Db_Table_Abstract {

    protected $_name = 'sign_in_child';
    protected $_primary = array('sign_in_family_id', 'child_id');
    protected $_rowClass = 'Application_Model_Row_SignInChild';
    protected $_referenceMap = array(
        'SignInFamily' => array(
            'columns' => 'sign_in_family_id',
            'refTableClass' => 'Application_Model_DbTable_SignInFamily',
            'refColumns' => 'id'
        ),
        'Child' => array(
            'columns' => 'child_id',
            'refTableClass' => 'Application_Model_DbTable_Child',
            'refColumns' => 'id'
        )
    );

    /**
     * Delete signed in children for program session and family
     * @param integer $session_id
     * @param integer $family_id
     * @return array affected children
     */
    public function deleteSignedInChildren($sign_in_family_id) {
        $sql = "DELETE SC
                FROM sign_in_child AS SC
                WHERE SC.sign_in_family_id = ?";
        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $sign_in_family_id, 'INTEGER', 1);
        
        return $db->query($sql);
    }
    
    /**
     * Gets children by id
     * @param integer $sign_in_family_id
     * @return array of sign in children
     */
    public function getChildrenByID($sign_in_family_id) {
        return $this->fetchAll(array('sign_in_family_id = ?'=>$sign_in_family_id));
    }
}

