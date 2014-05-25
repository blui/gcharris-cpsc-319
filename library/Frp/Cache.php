<?php

class Frp_Cache {

    private $cache;
    
    /**
     * Memcached based zend cacheing system 
     */
    function __construct() {
        $frontController = Zend_Controller_Front::getInstance();
        $config = $frontController->getParam('bootstrap')->getOption('memcached');

                
        $frontend = array('caching' => true, 'lifetime' => $config['lifetime'], 'automatic_serialization' => true);
        
        //Set memcached port. Config stored in application.ini
        $backend = array(
            'servers' => array(
                array('host' => $config['host'], 'port' => $config['port'])
            ),
            'compression' => false
        );

        $this->cache = Zend_Cache::factory('Core', 'Memcached', $frontend, $backend);
    }

    /**
     * Returns a Zend Cache object
     * @return type 
     */
    public function getCache() {
        return $this->cache;
    }

}

?>
