<?php
namespace af\tests\plugins\sharding;

use af\plugins\test\Test_Case;
use af\plugins\test\Test_Suite;
use af\kernel\actor\Actor;
use af\kernel\core\Kernel;
use af\plugins\sharding\Shard_Config;
use af\tools\ash\Tool_Config;
use af\plugins\sharding\errors\Shard_Error;

class Sharding_Config_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function set_up() {
    $this->last_error_code = -1;
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

  public function test_duplicated_table() {
    $this->assert_true($this->load_configs());

    Tool_Config::get_instance()->push('test_sharding.tables', $this->get_duplicated_id_table_data());
    $this->assert_false($this->load_configs());
    $this->assert_equal(Shard_Error::TABLE_ID_DUPLICATED, $this->get_last_error_code());
  }

  private function get_duplicated_id_table_data() {
    $tables = Tool_Config::get_instance()->get('test_sharding.tables');
    return $tables[0];
  }

  public function test_duplicated_shard_hash() {
    $this->assert_true($this->load_configs());

    Tool_Config::get_instance()->push('test_sharding.shards', $this->get_duplicated_hash_shard_data()); 
    $this->assert_false($this->load_configs());
    $this->assert_equal(Shard_Error::SHARD_HASH_DUPLICATED, $this->get_last_error_code());
  }

  private function get_duplicated_hash_shard_data() {
    return array(
      "id"=>"lookup-duplicated-test",
      "hash"=>"4dc",
      "master"=> array(
        "connecter"=> "mysql",
        "host"=> "localhost",
        "port"=> 3306,
        "user"=> "root",
        "password"=> "",
        "db_name"=> "test_lookup01",
        "charset"=> "utf8",
        "persistent"=> true
      ),
      "slave"=> array(
        "connecter"=> "mysql",
        "host"=> "localhost",
        "port"=> 3306,
        "user"=> "root",
        "password"=> "",
        "db_name"=> "test_lookup01",
        "charset"=> "utf8",
        "persistent"=> true
      )
    ); 
  }

  public function test_duplicated_shard_set() {
    $this->assert_true($this->load_configs());

    Tool_Config::get_instance()->push('test_sharding.shard_sets', $this->get_duplicated_id_shard_set_data());
    $this->assert_false($this->load_configs());
    $this->assert_equal(Shard_Error::SHARD_SET_ID_DUPLICATED, $this->get_last_error_code());
  }

  private function get_duplicated_id_shard_set_data() {
    $sets = Tool_Config::get_instance()->get('test_sharding.shard_sets');
    return $sets[0];
  }

  private function load_configs() {
    try {
      $this->shard_config->load($this->get_shard_tables(), $this->get_shard_sets(), $this->get_shards());
    }
    catch (Shard_Error $e) {
      $this->last_error_code = $e->get_code();
      return false;
    }
    return true;
  }

  private function get_last_error_code() {
    return $this->last_error_code;
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
  private $last_error_code = -1;

  public static function create_suite() {
    $suite = new Test_Suite('Sharding_Config_Test');
    $suite->add(new Sharding_Config_Test('test_load_lookup_shards'));
    $suite->add(new Sharding_Config_Test('test_duplicated_table'));
    $suite->add(new Sharding_Config_Test('test_duplicated_shard_hash'));
    $suite->add(new Sharding_Config_Test('test_duplicated_shard_set'));
    return $suite;
  }
}

