<?php
namespace apdos\kernel\core;

class Assert {
  public function __construct() {
  }

  public function active($callback) {
    assert_options(ASSERT_ACTIVE, 1);
    assert_options(ASSERT_WARNING, 0);
    assert_options(ASSERT_CALLBACK, $callback);
  }

  public function inactive() {
    assert_options(ASSERT_ACTIVE, 0);
  } 

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new Assert();
    }
    return $instance;
  }
}
