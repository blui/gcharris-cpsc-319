<?php
/**
* Abstract class to be extended by models
*
* @package    Frp
* @author     Grant Harris
* @version    1.0
* @abstract
*/
abstract class Frp_Model_Abstract {

/**
     * Return a empty array rather than a null if none
     * @param type $rowset
     * @return type
     */
    public function rowsetToArray($rowset) {
        if (count($rowset) > 0) {
            $result = $rowset->toArray();
        } else {
            $result = array();
        }
        return $result;
    }
    
    }