<?php

/**
 * Model for table ProgramStaff
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_ProgramStaff extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of program, staff, program_staff 
     */
    private $program_table;
    private $staff_table;
    private $program_staff_table;

    function __construct() {
        $this->program_table = new Application_Model_DbTable_Program();
        $this->staff_table = new Application_Model_DbTable_Staff();
        $this->program_staff_table = new Application_Model_DbTable_ProgramStaff();
    }

    /**
     * Gets all program staffs
     * @return Array of the Program staffs
     */
    public function getAllProgramStaff() {
        $programStaffTable = $this->program_staff_table->fetchAll();
        return $this->rowsetToArray($programStaffTable);
    }

    /**
     * Method allows linking of a staff id to a program id to generate new 
     * program staff relationship
     * @param integer $staff_id
     * @param integer $program_id
     * @return updated table with relationship between program id and staff id
     */
    public function createProgramStaff($staff_id, $program_id) {
        $data = array(
            'staff_id' => $staff_id,
            'program_id' => $program_id
        );

        $this->program_staff_table->insert($data);
        return $result;
    }

    /**
     * Given a specific staff id, delete the association as program staff with
     * program id
     * @param integer $staff_id
     * @return updated table
     */
    public function deleteProgramStaff($staff_id) {
        $where = $this->progrma_staff_table->getAdapter()->quoteInto('id = ?', $staff_id);
        return $this->_program_staff_table->delete($where);
    }

}
