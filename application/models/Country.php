<?php

/**
 * Model for table Country
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Country extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of country and family 
     */
    private $country_table;
    private $family_table;

    function __construct() {
        $this->country_table = new Application_Model_DbTable_Country();
        $this->family_table = new Application_Model_DbTable_Family();
    }

    /**
     * Returns an array of all the keys which are country codes
     * @return array of country codes
     */
    public function getAllCountries() {
        $countryTable = $this->country_table->fetchAll(NULL, 'name');
        return $this->rowsetToArray($countryTable);
    }

    /**
     * Method that returns a country associated with given code
     * @param char $code
     * return country associated with code
     */
    public function getCountryByCode($code) {
        return $this->rowsetToArray($this->country_table->getCountryNameByCode($code));
    }

    /**
     * Gets active countries
     * @return array countries
     */
    public function getActiveCountries() {
        return $this->rowsetToArray($this->country_table->getActiveCountries());
    }

}

