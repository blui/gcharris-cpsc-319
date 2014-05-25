<?php

/**
 * DbTable model for table ProgramStaff
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_DbTable_ProgramStaff extends Frp_Db_Table_Abstract {

    protected $_name = 'program_staff';
    protected $_primary = array('staff_id', 'program_id');
    protected $_referenceMap = array(
        'Staff' => array(
            'columns' => 'staff_id',
            'refTableClass' => 'Application_Model_DbTable_Staff',
            'refColumns' => 'id'
        ),
        'Program' => array(
            'columns' => 'program_id',
            'refTableClass' => 'Application_Model_DbTable_Program',
            'refColumns' => 'id'
        )
    );

}

