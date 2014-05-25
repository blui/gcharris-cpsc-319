<?php

/**
 * DBTable model for table Resource
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_DbTable_SessionResource extends Frp_Db_Table_Abstract {

    protected $_name = 'session_resource';
    protected $_primary = array('program_session_id', 'resource_id');
    protected $_rowClass = 'Application_Model_Row_SessionResource';
    protected $_referenceMap = array(
        'ProgramSession' => array(
            'columns' => 'program_session_id',
            'refTableClass' => 'Application_Model_DbTable_ProgramSession',
            'refColumns' => 'id'
        ),
        'Resource' => array(
            'columns' => 'resource_id',
            'refTableClass' => 'Application_Model_DbTable_Resource',
            'refColumns' => 'id'
        )
    );

    /**
     * Method that gets resources for a program session
     * @param integer $session_id
     * @return array session resources
     */
    public function getSessionResources($session_id, $program_id) {
        $this->select->reset();
        $where = $this->getAdapter()->quoteInto('SR.resource_id = R.id AND SR.program_session_id = ?', $session_id);
        $select = $this->select->setIntegrityCheck(false)
                ->from(array('R' => 'resource'))
                ->joinLeft(array('SR' => 'session_resource'), $where)
                ->where('R.program_id = ?', $program_id)
                ->order('R.name');
        return $this->fetchAll($select);
    }

}

