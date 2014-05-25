<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    //Register the namespace for our library files
    protected function _initNamespaces() {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Frp_');
        date_default_timezone_set('America/Vancouver');
    }

}

