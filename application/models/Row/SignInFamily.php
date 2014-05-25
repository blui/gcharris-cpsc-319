<?php

/**
 * Row model for table SignInFamily
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Row_SignInFamily extends Zend_Db_Table_Row_Abstract {

    private $sign_in_child = null;

    /**
     * Gets guest speakers associated to program session
     * @return array guest_speaker
     */
    public function getSignedChildren() {
        if (!$this->sign_in_child) {
            $this->sign_in_child = $this->findDependentRowset('Application_Model_DbTable_SignInChild');
        }

        return $this->sign_in_child;
    }

}

?>