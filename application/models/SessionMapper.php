<?php

class Model_SessionMapper {
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
            $this->setDbTable('Model_DbTable_Session');
        }
        return $this->_dbTable;
    }
    
    public function find($id, Model_Session $session) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $session->setId($row->id)
                ->setDesc($row->description)
                ->setActive($row->active);
    }
    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach($resultSet as $result) {
            $entry = new Model_Session();
            $entry->setDesc($result->description)
                    ->setId($result->id)
                    ->setActive($result->active);
            $entries[] = $entry;
        }
        
        return $entries;
    }
    
    public function save(Model_Session $session){
        $data = array(
            'id' => $session->getId(),
            'description' => $session->getDesc(),
            'active' => $session->getActive(),
        );
        
        if(null === ($id = $session->getId())) {
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
}
?>
