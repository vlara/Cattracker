<?php

class Model_ArrivalMapper {
    protected $_dbTable;
    
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
            $this->setDbTable('Model_DbTable_Arrival');
        }
        return $this->_dbTable;
    }
    
    public function find($id, Model_Arrival $Arrival) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $Arrival->setLocation($row->location)
                ->setTime($row->time)
                ->setLine($row->line)
                ->setSessionID($row->sessionId);
    }
    
    public function save(Model_Arrival $Arrival){
        $data = array(
            'location' => $Arrival->getLocation(),
            'time' => $Arrival->getTime(),
            'line' => $Arrival->getLine(),
            'sessionID' => $Arrival->getSessionID()
        );
        
//        if(null === ($id = $location->getId())) {
//            unset($data['id']);
            $this->getDbTable()->insert($data);
//        } else {
//            $this->getDbTable()->update($data, array('id = ?' => $id));
//        }
    }
    
    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach($resultSet as $result) {
            $entry = new Model_Arrival();
            $entry->setLine($result->line)->setLocation($result->location)->setTime($result->time);
            $entries[] = $entry;
        }
        return $entries;
    }
}
?>

