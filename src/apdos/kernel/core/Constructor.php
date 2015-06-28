<?php
namespace apdos\kernel\core;

class Constructor {
  public function __construct($instance, $args) {
    $this->instance = $instance;
    $this->args = $args;
    $this->constructors = array();
  }

  public function regist($arg_count, $construct_method) {
    $this->constructors[$arg_count] = $construct_method;
  }

  public function run() {
    if (isset($this->constructors[count($this->args)])) {
      $method = $this->constructors[count($this->args)];
      call_user_func_array(array($this->instance, $method), $this->args);
    }
  }

  private $instance;
  private $args;
}


