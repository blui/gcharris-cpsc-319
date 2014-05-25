<?php

/**
 * Model for SessionResource
 * @package    Frp
 * @author     Brian Lui
 * @version    1.0
 */
class Application_Model_SessionResource extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of session_resource, resource and program_session
     */
    private $session_resource_table;

    function __construct() {
        $this->session_resource_table = new Application_Model_DbTable_SessionResource();
    }

    /**
     * Method create a new resource or edit the current one
     * @param integer $program_session_id
     * @param integer $resource_id
     * @param string $field
     * @param string $value
     * @return table row of new session_resource 
     */
    public function createOrEditSessionResource($session_id, $resource_id, $field, $value) {
        $session_resource = $this->session_resource_table->find($session_id, $resource_id)->current();

        if (empty($session_resource)) {
            $resourceArray = array(
                'program_session_id' => $session_id,
                'resource_id' => $resource_id,
                $field => $value
            );

            return $this->session_resource_table->insert($resourceArray);
        } else {
            return $this->editSessionResource($session_id, $resource_id, $field, $value);
        }
    }

    /**
     * Method that deletes a resource on a program session using their resource id
     * @param integer $session_id
     * @param integer $resource_id
     * return session_resource_table with specific resource deleted
     */
    public function deleteSessionResource($session_id, $resource_id) {
        return $this->session_resource_table->delete(array(
            'program_session_id = ?' => $session_id,
            'resource_id = ?' => $resource_id
        ));
    }

    /**
     * Method that deletes a resource on a program session using their resource id
     * @param integer $session_id
     * @param integer $resource_id
     * return session_resource_table with specific resource deleted
     */
    public function editSessionResource($session_id, $resource_id, $field, $value) {
        $data = array($field => $value);
        return $this->session_resource_table->update($data, array(
            'program_session_id = ?' => $session_id,
            'resource_id = ?' => $resource_id
        ));
    }

    /**
     * Method that gets resources for a program session
     * @param integer $session_id
     * @return array ression resources
     */
    public function getSessionResources($session_id, $program_id) {
        return $this->rowsetToArray($this->session_resource_table->getSessionResources($session_id, $program_id));
    }

}

