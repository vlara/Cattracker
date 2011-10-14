<?php

class Model_ArrivalMapper {
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
        $newTimeFormat = strtotime($Arrival->getTime());
        $sqlTime = date("H:i:s",$newTimeFormat);
        
        $data = array(
            'location' => $Arrival->getLocation(),
            'time' => $sqlTime,
            'line' => $Arrival->getLine(),
            'sessionID' => $Arrival->getSessionID()
        );
        
       if(null === ($id = $Arrival->getId())) {
            unset($data['id']);
            try{
                $this->getDbTable()->insert($data);
            } catch(Zend_Db_Statement_Exception $e) {
                if($e->getCode() != 23000) {
                    print_r($e->getMessage());
                }
            }
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach($resultSet as $result) {
            $entry = new Model_Arrival();
            $entry->setLine($result->line)->setLocation($result->location)->setTime($result->time)->setSessionID($result->sessionID)->setID($result->id);
            $entries[] = $entry;
        }
        return $entries;
    }
    public function delete($id){
        $table = $this->getDbTable();
        $where = $this->db->quoteInto('id = ?', $id);
        $table->delete($where);
    }
}
?>

