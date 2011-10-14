<?php

class Model_DaysOperationMapper {
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
            $this->setDbTable('Model_DbTable_DaysOperation');
        }
        return $this->_dbTable;
    }
    
    public function save(Model_DaysOperation $doo){
        $data = array(
            'lineID' => $doo->getLineID(),
            'day' => $doo->getDay()
        );
        $this->getDbTable()->insert($data);
    }
    
    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach($resultSet as $result) {
            $entry = new Model_DaysOperation();
            $entry->setLineID($result->lineID)
                    ->setDay($result->day);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function findByLineID($lineID){
        $table = $this->getDbTable();
        $select = $table->select();
        $select->where('lineID = ?', $lineID);
        $rows = $table->fetchAll($select);
        $entries = array();
        foreach($rows as $row){
            $doo = new Model_DaysOperation();
            $doo->setDay($row['day'])->setLineID($row['lineID']);
            $entries[] = $doo;
        }
        
        return $entries;
    }
    
    public function deleteByLineID($lineID){
        $table = $this->getDbTable();
        $where = $table->getAdapter()->quoteInto('lineID = ?', $lineID);
        $table->delete($where);
    }
}
?>

