<?php

/**
 * Row model for table GuestSpeaker
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Row_ProgramSession extends Zend_Db_Table_Row_Abstract {

    private $guest_speaker = null;
    private $program = null;

    /**
     * Gets guest speakers associated to program session
     * @return array guest_speaker
     */
    public function getGuestSpeakers() {
        if (!$this->guest_speaker) {
            $this->guest_speaker = $this->findDependentRowset('Application_Model_DbTable_GuestSpeaker');
        }

        return $this->guest_speaker;
    }

    /**
     * Gets program of program session
     * @return array program
     */
    public function getProgram() {
        if (!$this->program) {
            $this->program = $this->findParentRow('Application_Model_DbTable_Program');
        }

        return $this->program;
    }

}

?>