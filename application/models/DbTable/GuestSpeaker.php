<?php

/**
 * DbTable model for table GuestSpeaker
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_DbTable_GuestSpeaker extends Frp_Db_Table_Abstract {

    protected $_name = 'guest_speaker';
    protected $_primary = 'id';
    protected $_referenceMap = array(
        'ProgramSession' => array(
            'columns' => 'program_session_id',
            'refTableClass' => 'Application_Model_DbTable_ProgramSession',
            'refColumns' => 'id'
        )
    );

    /**
     * Method returns guest speaker given name
     * @param text $speaker_name
     * @return row of detailing guest speakers associated with given name
     */
    public function getGuestSpeakerByName($speaker_name) {
        $this->select->from($this);
        $this->select->where('speaker_name = ?', $speaker_name);
        return $this->fetchRow($this->select);
    }

}

