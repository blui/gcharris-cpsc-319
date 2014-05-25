<?php

/**
 * Row model for table GuestSpeaker
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Row_SignInChild extends Zend_Db_Table_Row_Abstract {

    private $sign_in_family = null;

    /**
     * Gets guest speakers associated to program session
     * @return array guest_speaker
     */
    public function getSignInFamily() {
        if (!$this->sign_in_family) {
            $this->sign_in_family = $this->findParentRowset('Application_Model_DbTable_SignInFamily');
        }

        return $this->sign_in_family;
    }

}

?>