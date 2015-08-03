<?php
namespace af\tests\plugins\cache;

use af\plugins\test\Test_Case;
use af\plugins\cache\Cache;
use af\plugins\test\Test_Suite;
use af\plugins\cache\handlers\File_Handler;


class Cache_Test extends Test_Case {
  private $TEST_KEY_1 = "test_key_1";
  private $TEST_KEY_2 = "test_key_2";
  private $TEST_NUMERIC_VALUE = 1000;
  private $TEST_ARRAY_VALUE = array(1, "test", 1.01);

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_numeric() {
    Cache::get_instance()->set($this->TEST_KEY_1, $this->TEST_NUMERIC_VALUE);
    $this->assert(true == Cache::get_instance()->has($this->TEST_KEY_1), "Value is has");
    $value = Cache::get_instance()->get($this->TEST_KEY_1);
    $this->assert($this->TEST_NUMERIC_VALUE == $value, "Value is same");
  }

  public function test_array() {
    Cache::get_instance()->set($this->TEST_KEY_1, $this->TEST_ARRAY_VALUE);
    $this->assert(true == Cache::get_instance()->has($this->TEST_KEY_1), "Value is has");
    $value = Cache::get_instance()->get($this->TEST_KEY_1);
    $this->assert($this->TEST_ARRAY_VALUE[0] == $value[0], "Value is same");
    $this->assert($this->TEST_ARRAY_VALUE[1] == $value[1], "Value is same");
    $this->assert($this->TEST_ARRAY_VALUE[2] == $value[2], "Value is same");
  }

  public function test_expire() {
    Cache::get_instance()->set($this->TEST_KEY_1, $this->TEST_ARRAY_VALUE, 0);
    $this->assert(false == Cache::get_instance()->has($this->TEST_KEY_1), "Value is not has");
  }

  public function test_clear() {
    Cache::get_instance()->set($this->TEST_KEY_1, $this->TEST_ARRAY_VALUE);
    $this->assert(true == Cache::get_instance()->has($this->TEST_KEY_1), "Value is has");
    Cache::get_instance()->clear($this->TEST_KEY_1);
    $this->assert(false == Cache::get_instance()->has($this->TEST_KEY_1), "Value is not has");
  }

  public function test_clear_all() {
    Cache::get_instance()->set($this->TEST_KEY_1, $this->TEST_ARRAY_VALUE);
    Cache::get_instance()->set($this->TEST_KEY_2, $this->TEST_ARRAY_VALUE);
    Cache::get_instance()->clear_all();
    $this->assert(false == Cache::get_instance()->has($this->TEST_KEY_1), "Value is not has");
    $this->assert(false == Cache::get_instance()->has($this->TEST_KEY_2), "Value is not has");
  }

  public function set_up() {
    Cache::get_instance()->set_handler(new File_Handler('/tmp'));
  }

  public function tear_down() {
    Cache::get_instance()->clear_all();
    Cache::get_instance()->remove_handler();
  }

  public static function create_suite() {
    $suite = new Test_Suite('Cache_Test');
    $suite->add(new Cache_Test('test_numeric'));
    $suite->add(new Cache_Test('test_array'));
    $suite->add(new Cache_Test('test_expire'));
    $suite->add(new Cache_Test('test_clear'));
    $suite->add(new Cache_Test('test_clear_all'));
    return $suite;
  }
}

