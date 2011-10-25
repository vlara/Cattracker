<?php

class Model_LocationMapper {
    protected $_dbTable;
    protected $db;
    
    function __construct() {
        $this->db = Zend_Db_Table_Abstract::getDefaultAdapter();
    }
    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Model_DbTable_Location');
        }
        return $this->_dbTable;
    }
    
    public function find($id, Model_Location $location) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $location->setId($row->id)
                ->setLat($row->lat)
                ->setLng($row->lng)
                ->setName($row->name)
                ->setDescription($row->description);
    }
    
    public function findByName($name, Model_Location $location) {
        $table = $this->getDbTable();
        $select = $table->select();
        $select->where('name = ?', $name);
        $row = $table->fetchRow($select);
        if(isset($row)){
            $location->setDescription($row['description']);
            $location->setId($row['id']);
            $location->setLat($row['lat']);
            $location->setLng($row['lng']);
            $location->setName($row['name']);
            return $location;
        } else {
            return null;
        }
    }
    
    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach($resultSet as $result) {
            $entry = new Model_Location();
            $entry->setDescription($result->description)
                    ->setId($result->id)
                    ->setLat($result->lat)
                    ->setName($result->name)
                    ->setLng($result->lng);
            $entries[] = $entry;
        }
        
        return $entries;
    }
    public function fetchAllByID($id){
        $resultSet = $this->getDbTable()->find(array(18));
        return $resultSet;
//        $sql = "SELECT * FROM location WHERE id IN ?";
//$stmt = $this->db->prepare($sql);
//$stmt->execute($ids);
//return $stmt->fetchAll();

//        $table = $this->getDbTable();
//        $select = $table->select();
//        $select->where("id in (?)", $ids);
//        $resultSet = $table->fetchAll($select);
//        foreach($resultSet as $result) {
//            $entry = new Model_Location();
//            $entry->setId($result->id)->setLat($result->lat)->setLng($result->lng)->setName($result->name);
//            $entries[] = $entry;
//        }
//        return $entries;
    }
    public function save(Model_Location $location){
        $data = array(
            'id' => $location->getId(),
            'lat' => $location->getLat(),
            'lng' => $location->getLng(),
            'name' => $location->getName(),
            'description' => $location->getDescription()
        );
        
        if(null === ($id = $location->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    public function delete($id){
        $table = $this->getDbTable();
        $where = $this->db->quoteInto('id = ?', $id);
        $table->delete($where);
    }
    
    public function getLastInsertedID(){
        return $this->db->lastInsertId();
    }
}
?>
