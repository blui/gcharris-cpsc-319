<?php

/**
 * Helper for automatically including JS files in modules
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Zend_View_Helper_JsHelper extends Zend_View_Helper_Abstract {

    public function jsHelper() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $file_uri = 'js/frp/' . $request->getModuleName() . '.js';
        if (file_exists($file_uri)) {
            ?>
            <script src="<?php echo $this->view->baseUrl($file_uri); ?>"></script> 
            <?php
        }
    }

}
