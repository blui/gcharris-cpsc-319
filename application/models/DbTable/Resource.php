<?php

/**
 * DBTable model for table Resource
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_DbTable_Resource extends Frp_Db_Table_Abstract {

    protected $_name = 'resource';
    protected $_primary = 'id';
    protected $_rowClass = 'Application_Model_Row_Resource';
    protected $_referenceMap = array(
        'Program' => array(
            'columns' => 'program_id',
            'refTableClass' => 'Application_Model_DbTable_Program',
            'refColumns' => 'id'
        )
    );

}

