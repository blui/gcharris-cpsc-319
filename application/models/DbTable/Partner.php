<?php

/**
 * DbTable model for table Partner
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_DbTable_Partner extends Frp_Db_Table_Abstract {

    protected $_name = 'partner';
    protected $_primary = 'id';

    /**
     * Method returns partner details given an email
     * @param text $email
     * @return table row of details associated with email
     */
    public function getPartnerByEmail($email) {
        $this->select->from($this);
        $this->select->where('email = ?', $email);
        return $this->fetchRow($this->select);
    }

    /**
     * Method returns partner details given a name
     * @param text $name
     * @return table row of details associated with name
     */
    public function getPartnerByName($name) {
        $this->select->from($this);
        $this->select->where('name = ?', $name);
        return $this->fetchRow($this->select);
    }

    /**
     * Method returns partner details given an organization
     * @param text $organization
     * @return table row of details associated with organization
     */
    public function getPartnerByOrganization($organization) {
        $this->select->from($this);
        $this->select->where('organization = ?', $organization);
        return $this->fetchRow($this->select);
    }

}

