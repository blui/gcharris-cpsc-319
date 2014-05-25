<?php

/**
 * Model for Child
 * @package    Frp
 * @author     Mark Obad
 * @version    1.0
 */
class Application_Model_Child extends Frp_Model_Abstract {

    /**
     * A reference to the singleton instance of child and family 
     */
    private $child_table;
    private $family_table;

    function __construct() {
        $this->child_table = new Application_Model_DbTable_Child();
        $this->family_table = new Application_Model_DbTable_Family();
    }

    /**
     * Creates a child with given data
     * @param array $child_data
     */
    public function createChild($child_data) {
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();
        return $this->child_table->insert($child_data);
    }

    /**
     * Creates multiple children with given data
     * @param array $children_data
     * @return array
     */
    public function createChildren($children_data) {
        $created_children = array();
        foreach ($children_data as $child_data) {
            $created_children[] = $this->createChild($child_data);
        }
        return $created_children;
    }

    /**
     * Gets a child's info
     * @param integer $id
     * @return row child
     */
    public function getChildByID($id) {
        $result = $this->rowsetToArray($this->child_table->fetchAll(array('id = ?' => $id))->current());
        if (!empty($result['birthday'])) {
            $result['birthday'] = DateTime::createFromFormat('Y-m-d', $result['birthday'])->format("m/d/Y");
        } else {
            $result['birthday'] = "";
        }

        return $result;
    }

    /**
     * Retrieves all children that contain a given name in the
     * concatenation of their first and last name
     * @param text $name
     * @return 2 dimensional associative array of all children
     * that have the provided name
     */
    public function getChildrenByName($name) {
        return $this->rowsetToArray($this->child_table->getChildrenByName($name));
    }

    /**
     * Get children with family with matching phone number
     * @param integer $phonenumber
     * @return array $children
     */
    public function getChildrenByPhone($phone_number) {
        $family = $this->family_table->getFamilyByPhone($phone_number);
        return $this->rowsetToArray($family->getChildren());
    }

    /**
     * Updates a child with the provided data
     * @param integer $child_id
     * @param array $data
     */
    public function setChildData($child_id, $data) {
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();
        return $this->child_table->update($data, array('id = ?' => $child_id));
    }

    /**
     * Deletes a child with the provided ID
     * @param integer $child_id
     * @return array
     */
    public function deleteChild($child_id) {
        $frpCache = new Frp_Cache();
        $frpCache->getCache()->clean();
        return $this->child_table->delete(array('id = ?' => $child_id));
    }

    /**
     * Deletes children with the provided IDs
     * @param int $children_ids
     * @return array $deleted_children
     */
    public function deleteChildren($children_ids) {
        $deleted_children = array();
        foreach ($children_ids as $child_id) {
            $deleted_children[] = $this->deleteChild($child_id);
        }
        return $deleted_children;
    }

}

