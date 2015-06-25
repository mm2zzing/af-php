<?php
namespace tests\apdos\plugins\sharding;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;


class Sharding_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function set_up() {
  }

  public function tear_down() {
  }

  public function test_set() {
    //내부에서 각 shard의 db타입 접속 주소등을 관리
    //Shard_Connecter::get_instance()->load($this->get_config()):
    //Shard_Connecter::get_instance()->
  }

  public function get_config() {
    return array();
  }

  public static function create_suite() {
    $suite = new Test_Suite('Sharding_Test');
    $suite->add(new Sharding_Test('test_set'));
    return $suite;
  }
}

