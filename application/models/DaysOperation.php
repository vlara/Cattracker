<?php

class Model_DaysOperation
{
    protected $lineID;
    protected $day;
    
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
 
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid doo` property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid doo property');
        }
        return $this->$method();
    }
 
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    public function getLineID() {
        return $this->lineID;
    }

    public function setLineID($lineID) {
        $this->lineID = $lineID;
        return $this;
    }

    public function getDay() {
        return $this->day;
    }

    public function setDay($day) {
        $this->day = $day;
        return $this;
    }




}
?>
