<?php
/**
 * Table abstract for use by DbTable
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 * @abstract
 */
abstract class Frp_Db_Table_Abstract extends Zend_Db_Table_Abstract {
    /**
     * Zend select object
     * @var object
     */
    protected $select;

    public function init() {
        $this->select = $this->select();
    }

    /**
     * If Zend insert gives us back an array that means there is more
     * than one primary key. This contains a list of all the key values
     * However, if there is only one primary key, Zend insert only gives
     * us back the id of the inserted item, not its primary_key name,
     * so we must return it from what is specified in the _primary array
     * @param type $dataArray
     * @return array
     */
    public function insert($dataArray) {

        $result = parent::insert($dataArray);

        if (is_array($result)) {
            return $result;
        } else {
            reset($this->_primary);
            $key = current($this->_primary);
            return array("$key" => $result);
        }
    }

    /**
     * Delete row(s) based on supplied conditions, return 0 if none instead of null
     * @param type $condition
     * @return bool
     */
    public function delete($condition) {
        //Successfully deleted returns a 1, not (ie if row doesn't exist returns null, we want 0)
        return (parent::delete($condition) == 1) ? 1 : 0;
    }

}
