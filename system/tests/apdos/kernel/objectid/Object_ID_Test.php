<?php
namespace tests\apdos\kernel\objectid;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\kernel\actor\Actor;
use apdos\kernel\core\Kernel;
use apdos\kernel\objectid\ID;
use apdos\kernel\objectid\ID_Timestamp;
use apdos\kernel\objectid\Object_ID;
use apdos\kernel\objectid\Shard_ID;
use apdos\kernel\core\Time;
use apdos\kernel\objectid\errors\Increment_Count_Overflow;
use apdos\kernel\objectid\errors\Backward_Timestamp;

class Object_ID_Test extends Test_Case {
  private $server;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_create_id() {
    $timestamp = Time::get_instance()->get_timestamp();
    $id = Object_ID::create($timestamp);
    $this->assert($id->get_timestamp() == $timestamp, "Get unpack timestamp");
  }

  public function test_not_duplicated_ids() {
    $timestamp = Time::get_instance()->get_timestamp();
    $ids = $this->generate_ids($this->get_max_generate_count(), $timestamp);
    $this->assert(count($ids) == $this->get_max_generate_count(), 'Not duplicated ids');
  }

  public function test_reset_increment() {
    $timestamp = Time::get_instance()->get_timestamp();
    $id = Object_ID::create($timestamp);
    $this->assert(1 == ID_Timestamp::get_instance()->get_increment(), 'Inc is 1');
    $id = Object_ID::create($timestamp);
    $this->assert(2 == ID_Timestamp::get_instance()->get_increment(), 'Inc is 2');

    $reset_increment_timestamp = $timestamp + 1;
    $id = Object_ID::create($reset_increment_timestamp);
    $this->assert(1 == ID_Timestamp::get_instance()->get_increment(), 'Inc is 1');
  }

  public function test_throw_backward_timestamp() {
    $timestamp = Time::get_instance()->get_timestamp();
    $this->assert(true == $this->process_generate_ids(1, $timestamp));
    $backward_timestamp = $timestamp - 1;
    $this->assert(false == $this->process_generate_ids(1, $backward_timestamp));
  }

  public function test_throw_increment_count_overflow() {
    $timestamp = Time::get_instance()->get_timestamp();
    $this->assert(false == $this->process_generate_ids($this->get_max_generate_count() + 1, $timestamp));
  }

  private function process_generate_ids($count, $current_time) {
    try {
      $this->generate_ids($count, $current_time, $this->get_max_generate_count());
    }
    catch (Backward_Timestamp $e) {
      return false;
    }
    catch (Increment_Count_Overflow $e) {
      return false;
    }
    return true;
  }

  private function generate_ids($count, $current_time = -1) {
    $ids = array();
    for ($i = 0; $i < $count; $i++) {
      $id = Object_ID::create($current_time, $this->get_max_generate_count());
      array_push($ids, $id->to_string());
    }
    return array_unique($ids);
  }

  public function set_up() {
    ID_Timestamp::get_instance()->reset();
  }

  public function tear_down() {
  }

  private function get_max_generate_count() {
    return 10;
  }

  public static function create_suite() {
    $suite = new Test_Suite('Object_ID_Test');
    $suite->add(new Object_ID_Test('test_create_id'));
    $suite->add(new Object_ID_Test('test_not_duplicated_ids'));
    $suite->add(new Object_ID_Test('test_reset_increment'));
    $suite->add(new Object_ID_Test('test_throw_backward_timestamp'));
    $suite->add(new Object_ID_Test('test_throw_increment_count_overflow'));
    return $suite;
  }
}
