<?php

/**
 * Model for table HearAboutUs
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_HearAboutUs extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of hear_about_us and family 
     */
    private $hear_about_us_table;
    private $family_table;

    function __construct() {
        $this->hear_about_us_table = new Application_Model_DbTable_HearAboutUs();
        $this->family_table = new Application_Model_DbTable_Family();
    }

    /**
     * Method creatues new text entry for section "hear about us" in registration...
     * @param text $text
     * return table with new row for "hear about us"
     */
    public function createNewHearAboutUs($text) {
        $result = $this->hear_about_us_table->insert($text);
        return $result;
    }

    /**
     * Method returns text of all "hear about us'"
     * @return array of all "hear about us'"
     */
    public function getAllHearAboutUs() {
        $allHearTable = $this->hear_about_us_table->fetchAll();
        return $this->rowsetToArray($allHearTable);
    }

    /**
     * Method that returns a text "hear about us" associated with id
     * @param integer $id
     * return row with text of "hear about us" associated with id
     */
    public function getHearAboutUsByID($id) {
        return $this->rowsetToArray($this->hear_about_us_table->find($id));
    }

}
