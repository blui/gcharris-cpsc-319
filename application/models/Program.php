<?php

/**
 * Model for Program
 * @package    Frp
 * @author     Ruixue Li and Sammie Chan
 * @version    1.0
 */
class Application_Model_Program extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of program, staff and program_staff
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
     * Creates a new Program 
     * @param String $name
     * @param Array $coordinators
     * @return Int
     */
    public function createProgram($name, $coordinator_ids) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $data = array(
                'name' => $name
            );

            $program_id = $this->program_table->insert($data);
            $program_id = $program_id['id'];

            foreach ($coordinator_ids as $coordinator_id) {
                $data = array(
                    'staff_id' => $coordinator_id,
                    'program_id' => $program_id
                );

                $this->program_staff_table->insert($data);
            }

            $db->commit();

            return $program_id;
        } catch (Exception $e) {
            $db->rollBack();
            echo $e->getMessage();
        }
    }

    /**
     * Updates a Program 
     * @param String $name
     * @param Array $coordinators
     * @return Int
     */
    public function editProgram($program_id, $name, $coordinator_ids) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $data = array(
                'name' => $name
            );

            $where = $this->program_table->getAdapter()->quoteInto('id = ?', $program_id);
            $ret = $this->program_table->update($data, $where);

            $where = $this->program_staff_table->getAdapter()->quoteInto('program_id = ?', $program_id);
            $this->program_staff_table->delete($where);

            foreach ($coordinator_ids as $coordinator_id) {
                $data = array(
                    'staff_id' => $coordinator_id,
                    'program_id' => $program_id
                );

                $this->program_staff_table->insert($data);
            }

            $db->commit();

            return $ret;
        } catch (Exception $e) {
            $db->rollBack();
            echo $e->getMessage();
        }
    }

    /**
     * Gets all programs 
     * @return Array of the Programs
     */
    public function getAllPrograms($userSession) {
        $programtable = $this->program_table->getAllPrograms($userSession);
        return $this->rowsetToArray($programtable);
    }

    /**
     * Deletes the program
     * @param Int $program_id
     * @return Int Confirmation
     */
    public function deleteProgram($program_id) {
        $where = $this->program_table->getAdapter()->quoteInto('id = ?', $program_id);
        return $this->program_table->delete($where);
    }

    /**
     * Gets program by id
     * @param type $program_id
     * @return array program
     */
    public function getProgramByID($program_id) {
        return $this->rowsetToArray($this->program_table->find($program_id)->current());
    }

    /**
     * Gets the program and staff associated with given id
     * @param integer $id
     * @return array
     */
    public function getProgramStaffByID($id) {
        return $this->rowsetToArray($this->program_table->getProgramStaffByID($id));
    }

    /**
     * Function to populate program page
     * @return program_id/name, active session_id,
     * program coordinator first/last_name
     */
    public function getAllProgramData($userSession) {
        return $this->rowsetToArray($this->program_table->getAllProgramData($userSession));
    }

    /**
     * Gets referrals by program
     * @param integer $program_id
     * @return array referrals
     */
    public function getReferralsByProgram($program_id) {
        $program = $this->program_table->find($program_id)->current();
        return $this->rowsetToArray($program->getReferrals());
    }

    /**
     * Gets referrals by program
     * @param integer $program_id
     * @return array resources
     */
    public function getResourcesByProgram($program_id) {
        $program = $this->program_table->find($program_id)->current();
        return $this->rowsetToArray($program->getResources());
    }

    /**
     * Gets active programs
     * @return array programs
     */
    public function getActivePrograms($userSession) {
        return $this->rowsetToArray($this->program_table->getActivePrograms($userSession));
    }

}
