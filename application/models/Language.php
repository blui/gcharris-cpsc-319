<?php

/**
 * Model for table Language
 * @package    Frp
 * @author     Sammie Chan
 * @version    1.0
 */
class Application_Model_Language extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of language
     */
    private $language_table;

    function __construct() {
        $this->language_table = new Application_Model_DbTable_Language();
    }

    /**
     * Method returns an array of all the keys which are language codes
     * @return array of keys of language codes
     */
    public function getAllLangCodes() {
        $languageTable = $this->language_table->fetchAll(NULL, 'lang_name_english');
        return $this->rowsetToArray($languageTable);
    }

    /**
     * Gets used languages
     * @return array languages
     */
    public function getActiveLanguages() {
        return $this->rowsetToArray($this->language_table->getActiveLanguages());
    }

}

