<?php
namespace tests\apdos\plugins\sharding;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\kernel\actor\Actor;
use apdos\kernel\core\Kernel;
use apdos\plugins\sharding\Shard_Config;
use apdos\tools\ash\Tool_Config;
use apdos\plugins\sharding\errors\Sharding_Error;

class Sharding_Config_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function set_up() {
    $this->actor = Kernel::get_instance()->new_object(Actor::get_class_name(), '/sys/srouter');
    $this->shard_config = $this->actor->add_component(Shard_Config::get_class_name());
  }

  public function tear_down() {
    $this->actor->release();
    Tool_Config::get_instance()->clear('test_sharding');
  }

  public function test_load_lookup_shards() {
    $this->assert_true($this->load_configs());
  }

  public function test_duplicated_shard_id_failed() {
    $shards = $this->get_shards();
    Tool_Config::get_instance()->push('test_sharding.shards', $shards[0]); 
    $this->assert_false($this->load_configs());
  }

  public function test_duplicated_shard_hash_failed() {
  }

  public function test_reload_lookup_shards() {
  }

  private function load_configs() {
    try {
      $this->shard_config->load($this->get_shard_tables(), $this->get_shard_sets(), $this->get_shards());
    }
    catch (Sharding_Error $e) {
      return false;
    }
    return true;
  }

  public function get_shard_tables() {
    return Tool_Config::get_instance()->get('test_sharding.tables');
  }

  public function get_shards() {
    return Tool_Config::get_instance()->get('test_sharding.shards');
  }

  public function get_shard_sets() {
    return Tool_Config::get_instance()->get('test_sharding.shard_sets');
  }

  private $actor;
  private $shard_config;

  public static function create_suite() {
    $suite = new Test_Suite('Sharding_Config_Test');
    $suite->add(new Sharding_Config_Test('test_load_lookup_shards'));
    $suite->add(new Sharding_Config_Test('test_duplicated_shard_id_failed'));
    $suite->add(new Sharding_Config_Test('test_duplicated_shard_hash_failed'));
    $suite->add(new Sharding_Config_Test('test_reload_lookup_shards'));
    return $suite;
  }
}

