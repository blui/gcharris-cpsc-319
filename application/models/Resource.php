<?php

/**
 * Model for table Resource
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_Resource extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of resource and session_resource
     */
    private $resource_table;
    private $session_resource_table;

    function __construct() {
        $this->resource_table = new Application_Model_DbTable_Resource();
        $this->session_resource_table = new Application_Model_DbTable_SessionResource();
    }

    /**
     * Method to create a new resource given name
     * @param text $name
     * return new resource in resource_table with associated name
     */
    public function createResource($name, $program_id) {
        $data = array('name' => $name,
            'program_id' => $program_id);
        return $this->resource_table->insert($data);
    }

    /**
     * Method to return a list of all resources in table
     * return a list of resources from resource_table
     */
    public function getAllResources() {
        return $this->rowsetToArray($this->resource_table->fetchAll());
    }

    /**
     * Method to edit data for associated resource with id, and data user wants to input
     * @param integer $resource_id
     * @param text $data
     * return resource_table with updated data linked to resource id
     */
    public function setResourceData($resource_id, $name) {
        $data = array('name' => $name);
        return $this->resource_table->update($data, array('id = ?' => $resource_id));
    }

    /**
     * Method to delete resource associated to id from resource_table
     * @param integer $resource_id
     * return table without row for the deleted resource id
     */
    public function deleteResource($resource_id) {
        return $this->resource_table->delete(array('id = ?' => $resource_id));
    }

}

