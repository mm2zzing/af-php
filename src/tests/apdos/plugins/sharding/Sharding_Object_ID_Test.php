<?php
namespace tests\apdos\plugins\sharding;

use apdos\kernel\objectid\ID;
use apdos\kernel\objectid\ID_Timestamp;
use apdos\kernel\objectid\Shard_ID;
use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\Time;
use apdos\plugins\sharding\adts\Shard_Object_ID;

class Sharding_Object_ID_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_create_id() {
    $timestamp = Time::get_instance()->get_timestamp();
    $lookup_shard_id = Shard_ID::create("lookup01");
    $id = Shard_Object_ID::create($lookup_shard_id, $timestamp);
    $this->assert($id->get_timestamp() == $timestamp, "Get unpack timestamp");
    $hash = $lookup_shard_id->to_hash();
    $this->assert($id->get_lookup_shard_id() == $hash, "Get unpack lookup shard id");
  }

  public function test_not_duplicated_ids() {
    $timestamp = Time::get_instance()->get_timestamp();
    $ids = $this->generate_ids($this->get_max_generate_count(), $timestamp);
    $this->assert(count($ids) == $this->get_max_generate_count(), 'Not duplicated ids');
  }

  public function set_up() {
    ID_Timestamp::get_instance()->reset();
  }

  public function tear_down() {
  }

  private function generate_ids($count, $current_time = -1) {
    $ids = array();
    $lookup_shard_id = Shard_ID::create("lookup01");
    for ($i = 0; $i < $count; $i++) {
      $id = Shard_Object_ID::create($lookup_shard_id, $current_time, $this->get_max_generate_count());
      array_push($ids, $id->to_string());
    }
    return array_unique($ids);
  }

  private function get_max_generate_count() {
    return 10;
  }

  public static function create_suite() {
    $suite = new Test_Suite('Sharding_Object_ID_Test');
    $suite->add(new Sharding_Object_ID_Test('test_create_id'));
    $suite->add(new Sharding_Object_ID_Test('test_not_duplicated_ids'));
    return $suite;
  }

}

