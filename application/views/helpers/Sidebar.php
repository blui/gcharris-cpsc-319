<?php

/**
 * Helper for rending the sidebar
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Zend_View_Helper_Sidebar extends Zend_View_Helper_Abstract {
    public function sidebar() {
        if (count($this->view->sidebar_data) > 0) {
            echo '<ul id="sub-nav" class="nav nav-tabs">';

            foreach ($this->view->sidebar_data as $header => $subNav) {
                foreach ($subNav as $subItem => $path) {
                    echo '<li><a href="#/' . $path . '">' . $subItem . '</a></li>';
                }
            }

            echo '</ul>';
        }
    }
}