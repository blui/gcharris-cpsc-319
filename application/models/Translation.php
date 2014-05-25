<?php

/**
 * Model for Translation
 * @package    Frp
 * @author     Sammie Chan
 * @version    1.0
 */
class Application_Model_Translation extends Frp_Model_Abstract {

    /**
     * Stores an instance of the translation table
     */
    private $translation_table;

    function __construct() {

        $this->translation_table = new Application_Model_DbTable_Translation();
    }

    /**
     * Create a new translation for language code
     * @param varchar $lang_code
     * @param text $translation
     * @return table with new translation per given language code
     */
    public function createTranslation($lang_code, $translation) {
        $array = array(
            'lang_code' => $lang_code,
            'translation' => $translation
        );
        $this->translation_table->insert($array);
    }

    /**
     * Gets translation associated with language code
     * @param varchar $lang_code
     * @return array of text translation
     */
    public function getTranslation($lang_code) {
        $arr = $this->translation_table;

        $array = $this->rowsetToArray($arr->find($lang_code)->current());

        return $array['translation'];
    }

    /**
     * Edit translation for language code
     * @param varchar $lang_Code
     * @param text $translation
     * @return updated table with new translation per language code
     */
    public function setTranslation($lang_code, $translation) {

        $set = array('translation' => $translation);
        $this->translation_table->update($set, array('lang_code = ?'=>$lang_code));
    }

    /**
     * Remove translations associated with language code
     * @param varchar $lang_code
     * @return updated table without removed translations per language code
     */
    public function deleteTranslation($lang_code) {
        $this->translation_table->delete(array('lang_code = ?'=>$lang_code));
    }

}

