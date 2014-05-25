<?php

/**
 * Mail system for sending view based email
 *
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Frp_Mail extends Zend_Mail {

    /**
     * Sets the body of the mail from the supplied view file
     * @param type $type
     * @param type $scriptname
     * @param type $params
     */
    public function setBodyByView($email_view, $params = array()) {

        $view = new Zend_View;
        $view->setScriptPath($this->_getViewPath());

        foreach ($params as $key => $value) {
            $view->$key = $value;
        }
      
        $html = $view->render($email_view . '.phtml');
        $this->setBodyHtml($html);
    }

    /**
     * Get the view path
     * @return type
     */
    protected function _getViewPath() {
        return APPLICATION_PATH . '/views/scripts/email/';
    }

    /*
     * here because unserialize complains otherwise
     */

    public function send() {
        parent::send();
    }

}

