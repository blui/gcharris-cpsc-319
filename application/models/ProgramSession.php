<?php

/**
 * Model for ProgramSession
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_ProgramSession extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of session
     */
    private $session_table;

    function __construct() {
        $this->session_table = new Application_Model_DbTable_ProgramSession();
    }

    /**
     * Create a new session
     * @param integer $program_id
     * @return updated table 
     */
    public function createSession($program_id) {
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();

        $date = date('Y-m-d');

        $data = array('program_id' => $program_id,
            'date' => $date,
            'running' => 1);

        $insert = $this->session_table->insert($data);

        return $insert;
    }

    /**
     * Gets session info
     * @return session
     */
    public function getSessionByID($id) {
        $session = $this->session_table->find($id)->current();
        $gs = $this->rowsetToArray($session->getGuestSpeakers());
        $program = $this->rowsetToArray($session->getProgram());
        $result = $this->rowsetToArray($session);
        $result['guest_speakers'] = $gs;
        $result['program'] = $program;
        return $result;
    }

    /**
     * Update session info
     * @param integer id
     * @param string field
     * @param string value
     * @return array
     */
    public function editSession($id, $field, $value) {
        $data = array($field => $value);
        $update = $this->session_table->update($data, array('id = ?' => $id));

        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();

        return $update;
    }

    /**
     * Delete session
     * @param integer id
     * @return array
     */
    public function deleteSession($id) {
        $delete = $this->session_table->delete(array('id = ?' => $id));

        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();

        return $delete;
    }

    /**
     * Check if session valid
     * @param integer $session_id
     * @return boolean 
     */
    public function getSession($program_id) {
        return $this->rowsetToArray($this->session_table->getSession($program_id));
    }

    /**
     * Close any sessions left open
     * @return array 
     */
    public function closeOpenSessions() {
        $close = $this->session_table->update(array('running' => 0), array('running = ?' => 1));
        return $close;
    }

    /**
     * Get people checked in to session
     * @param integer $session_id
     * @return array of people checked in
     */
    public function getCheckin($session_id) {
        return $this->rowsetToArray($this->session_table->getCheckin($session_id));
    }

    /**
     * Get program session filter paginator
     * @param array $programs
     * @param array $families
     * @param date $start
     * @param date $end
     * @param string $sort
     * @param int $dir
     * @return paginator
     */
    public function getProgramSessionPaginator($userSession, $programs, $families, $start, $end, $sort, $dir) {
        $frpCache = new Frp_Cache();
        $cache = $frpCache->getCache();
        Zend_Paginator::setCache($cache);

        $adapter = new Zend_Paginator_Adapter_DbSelect($this->session_table->getProgramSessionSelect($userSession, $programs, $families, $start, $end, $sort, $dir));

        return new Zend_Paginator($adapter);
    }

}
