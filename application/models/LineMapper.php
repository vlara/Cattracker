<?php

class Model_LineMapper {
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
            $this->setDbTable('Model_DbTable_Line');
        }
        return $this->_dbTable;
    }
    
    public function find($id, Model_Line $line) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $line->setId($row->id)
                    ->setName($row->name);
    }
    
    public function save(Model_Line $line){
        $data = array(
            'id' => $line->getId(),
            'name' => $line->getName()
        );
        
        if(null === ($id = $line->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach($resultSet as $result) {
            $entry = new Model_Line();
            $entry->setName($result->name)
                    ->setId($result->id);
            $entries[] = $entry;
        }
        
        return $entries;
    }
    
    public function deleteLine($id){
        $table = $this->getDbTable();
        $where = $this->db->quoteInto('id = ?', $id);
        $table->delete($where);
    }
    
    public function getLastInsertedID(){
        return $this->db->lastInsertId();
    }
}
?>

