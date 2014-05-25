<?php

/**
 * Model for StaffHours
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_StaffHours extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of staff_resource, staff_hours and program_session
     */
    private $staff_hours_table;
    private $staff_table;
    private $program_session_table;
    private $program_table;

    function __construct() {
        $this->staff_table = new Application_Model_DbTable_Staff();
        $this->staff_hours_table = new Application_Model_DbTable_StaffHours();
        $this->program_session_table = new Application_Model_DbTable_ProgramSession();
        $this->program_table = new Application_Model_DbTable_Program();
    }

    /**
     * Gets all staff hours
     * @return array
     */
    public function getAllStaffHours($userSession, $staff, $program, $start, $end, $sort, $dir) {
        return $this->rowsetToArray($this->staff_hours_table->
                                getAllStaffHours($userSession, $staff, $program, $start, $end, $sort, $dir));
    }

    /**
     * Gets all staff hours paginator
     * @param array $staff
     * @param array $programs
     * @param date $start
     * @param date $end
     * @param string $sort
     * @param int $dir
     * @return paginator
     */
    public function getAllStaffHoursPaginator($userSession, $staff, $programs, $start, $end, $sort, $dir) {
        $frpCache = new Frp_Cache();
        $cache = $frpCache->getCache();
        Zend_Paginator::setCache($cache);
        $adapter = new Zend_Paginator_Adapter_DbSelect($this->staff_hours_table->
                        getAllStaffHoursSelect($userSession, $staff, $programs, $start, $end, $sort, $dir));
        return new Zend_Paginator($adapter);
    }

    /**
     * Get hours of staff member
     * @param integer $id
     * @return array
     */
    public function getStaffHoursByID($id) {
        return $this->rowsetToArray($this->staff_hours_table->getStaffHours($id));
    }

    /**
     * Get hours of staff member
     * @param integer $staff_id
     * @return integer hours
     */
    public function getHoursByStaffID($staff_id) {
        return $this->rowsetToArray($this->staff_hours_table->getHours($staff_id));
    }

    /**
     * Method allows one to edit hours for a staff member
     * @param integer $staff_id
     * @param integer $hours
     * @return table row of new hours associated withs taff 
     */
    public function editStaffHours($id, $data) {
        $update = $this->staff_hours_table->update($data, array('id = ?' => $id));

        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();

        return $update;
    }

    /**
     * Method that deletes all hours associated to staff id
     * @param integer $staff_id
     * return updated table with hours deleted
     */
    public function deleteStaffHours($id) {
        $delete = $this->staff_hours_table->delete(array('id = ?' => $id));

        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();

        return $delete;
    }

    /**
     * Creates a contribution
     * @param String $name
     * @param Array $coordinators
     * @return Int
     */
    public function createStaffHours($staff_id, $program_id, $date, $hours) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $data = array(
                'staff_id' => $staff_id,
                'program_id' => $program_id,
                'date' => $date,
                'hours' => $hours
            );

            $ret = $this->staff_hours_table->insert($data);

            $db->commit();

            $frpCache = new Frp_Cache();
            $frpCache->getCache()->clean();

            return $ret;
        } catch (Exception $e) {
            $db->rollBack();
            echo $e->getMessage();
        }
    }

}

