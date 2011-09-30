<?php
class Model_Session {
  protected $id;
  protected $desc;
  protected $active;

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
          throw new Exception('Invalid location property');
      }
      $this->$method($value);
  }

  public function __get($name)
  {
      $method = 'get' . $name;
      if (('mapper' == $name) || !method_exists($this, $method)) {
          throw new Exception('Invalid location property');
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

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
    return $this;
  }

  public function getDesc() {
    return $this->desc;
  }

  public function setDesc($desc) {
    $this->desc = $desc;
    return $this;
  }

  public function getActive() {
    return $this->active;
  }

  public function setActive($active) {
    $this->active = $active;
    return $this;
  }
}
?>
