<?php
namespace apdos\kernel\core;

class Object {
  public function __construct($args = array(), $constructor_methods = array()) {
    $this->constructor = new Constructor($this, $args);

    for ($i = 0; $i < count($constructor_methods); $i++) {
      $arg_count = $i + 1;
      $construct_method = $constructor_methods[$i];
      if ($construct_method != '')
        $this->constructor->regist($arg_count, $construct_method);
    }
    $this->constructor->run();
  }

  public function get_type() {
    return get_class_name($this);
  }

  /**
   * 네임스페이스를 포함 클래스명을 조회합니다. PHP >= 5.3
   *
   * @return string 클래스명
   */
  public static function get_class_name() {
    return get_called_class();
  }
}
