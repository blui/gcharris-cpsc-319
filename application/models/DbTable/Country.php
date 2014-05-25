<?php

/**
 * DbTable model for table Country
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_DbTable_Country extends Frp_Db_Table_Abstract {

    protected $_name = 'country';
    protected $_primary = 'code';

    /**
     * Method returns country code given a country name
     * @param char $name
     * @return row of detailing country code associated with given name
     */
    public function getCountryCodeByName($name) {
        $this->select->from($this);
        $this->select->where('name = ?', $name);
        return $this->fetchRow($this->select);
    }

    /**
     * Method returns country name given a country code
     * @param char $code
     * @return row of detailing country name associated with given code
     */
    public function getCountryNameByCode($code) {
        $this->select->from($this);
        $this->select->where('code = ?', $code);
        return $this->fetchRow($this->select);
    }
    
    /**
     * Gets active countries
     * @return array countries
     */
    public function getActiveCountries() {
        $this->select->reset();
        $select = $this->select->setIntegrityCheck(false)
                ->from(array('C' => 'country'))
                ->where('EXISTS(SELECT id FROM family WHERE guardian_origin_country = C.code)')
                ->order('C.name');

        return $this->fetchAll($select);
    }

}

