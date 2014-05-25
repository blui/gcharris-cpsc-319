<?php

/**
 * DbTable model for table Language
 * @package    Frp
 * @author     Sammie Chan
 * @version    1.0
 */
class Application_Model_DbTable_Language extends Frp_Db_Table_Abstract {

    protected $_name = 'language';
    protected $_primary = 'lang_code';

    /**
     * Gets used languages
     * @return array languages
     */
    public function getActiveLanguages() {
        $this->select->reset();
        $select = $this->select->setIntegrityCheck(false)
                ->from(array('L' => 'language'), array('lang_code' => 'L.lang_code', 'lang_name_english' => 'L.lang_name_english'))
                ->where('EXISTS(SELECT id FROM family WHERE guardian_first_lang = L.lang_code)')
                ->order('L.lang_name_english');

        return $this->fetchAll($select);
    }

}

