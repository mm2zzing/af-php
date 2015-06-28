<?php
namespace apdos\kernel\core;

class Object {
  public function __construct($args, $constructor_methods) {
    $this->constructor = new Constructor($this, $args);

    for ($i = 0; $i < count($constructor_methods); $i++) {
      $arg_count = $i + 1;
      $construct_method = $constructor_methods[$i];
      if ($construct_method != '')
        $this->constructor->regist($arg_count, $construct_method);
    }
    $this->constructor->run();
  }
}
