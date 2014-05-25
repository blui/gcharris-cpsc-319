<?php

/**
 * Model for table GuestSpeaker
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_GuestSpeaker extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of guest_speaker and program_session 
     */
    private $guest_speaker_table;
    private $program_session_table;

    function __construct() {
        $this->guest_speaker_table = new Application_Model_DbTable_GuestSpeaker();
        $this->program_session_table = new Application_Model_DbTable_ProgramSession();
    }

    /**
     * Method creatues new guest speaker given name
     * @param array $name
     * @param integer program_session_id
     * @return table with new row for guest speaker with specified name
     */
    public function createGuestSpeakers($names, $session_id) {
        $result = array();
        foreach ($names as $name) {
            $data = array('speaker_name' => $name,
                'program_session_id' => $session_id);
            $result[] = $this->guest_speaker_table->insert($data);
        }
        return $result;
    }

    /**
     * Method deletes row from table for specified guest speaker id
     * @param integer $id
     * return table without deleted id row
     */
    public function deleteGuestSpeakers($session_id) {
        $where = $this->guest_speaker_table->getAdapter()->quoteInto('program_session_id = ?', $session_id);
        return $this->guest_speaker_table->delete($where);
    }

}
