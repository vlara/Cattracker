<?php

/**
 *  Table Data Gateway to connect to data source
 *  @author lara.m.victor@gmail.com
 */
class Model_DbTable_Arrival  extends Zend_Db_Table_Abstract
{
    protected $_name = 'arrival';
    protected $_primary = array("location", "time", "line");
}

