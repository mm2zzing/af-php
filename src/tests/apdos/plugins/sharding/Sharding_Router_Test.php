<?php
namespace tests\apdos\plugins\sharding;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\Object_Converter;
use apdos\tools\ash\Tool_Config;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Kernel;
use apdos\plugins\sharding\Shard_Router;
use apdos\plugins\sharding\Shard_Session;
use apdos\plugins\sharding\Shard_Schema;
use apdos\plugins\sharding\Shard_Config;
use apdos\plugins\sharding\adts\Shard_ID;
use apdos\plugins\sharding\adts\Shard_IDs;
use apdos\plugins\sharding\adts\Table_ID;
use apdos\plugins\database\connecters\mysql\MySQL_Session;

/**
 * @class Sharding_Router_Test
 *
 * @brief 샤딩 유닛 테스트
 */
class Sharding_Router_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function set_up() {
    $this->actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/srouter');
    $this->shard_session = $this->actor->add_component(Shard_Session::get_class_name());
    $this->shard_session->set_property('db_session_class_name', MySQL_Session::get_class_name());

    $this->shard_schema = $this->actor->add_component(Shard_Schema::get_class_name());
    $this->shard_config = $this->actor->add_component(Shard_Config::get_class_name());
    $this->shard_router = $this->actor->add_component(Shard_Router::get_class_name());

    $this->shard_config->load($this->get_shard_tables(), $this->get_shard_sets(), $this->get_shards()); 

    $this->actor->update_events();

    $this->shard_schema->create_database();
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

  public function tear_down() {
    $this->shard_schema->drop_database();
    $this->actor->release();
  }

  public function test_has_table() {
    $this->assert(false == $this->shard_router->has_lookup_table());
    $this->assert(false == $this->shard_router->has_lookup_table());

    $this->shard_schema->create_lookup_table();
    $this->assert(true == $this->shard_router->has_lookup_table());
    $this->assert(true == $this->shard_router->has_lookup_table());
    $this->assert(false == $this->shard_router->has_table(new Table_ID('table_a')));
    $this->assert(false == $this->shard_router->has_table(new Table_ID('table_b')));

    $this->shard_schema->create_table(new Table_ID('table_a'), $this->get_data_fields());
    $this->shard_schema->create_table(new Table_ID('table_b'), $this->get_data_fields());
    $this->assert(true == $this->shard_router->has_table(new Table_ID('table_a')));
    $this->assert(true == $this->shard_router->has_table(new Table_ID('table_b')));
  }

  public function test_insert() {
    $this->preapre_data_schema();
  }

  private function preapre_data_schema() {
    $this->shard_schema->create_lookup_table();
    // auto sharding table
    $this->shard_schema->create_table(new Table_ID('table_a'), $this->get_data_fields());
    // static sharding table
    $this->shard_schema->create_table(new Table_ID('table_b'), $this->get_data_fields());
  }

  public function test_update() {
    $this->preapre_data_schema();
  }

  public function test_get() {
    $this->preapre_data_schema();
  }

  public function test_delete() {
    $this->preapre_data_schema();
  }

  private function get_data_fields() {
    return array(
      'field1'=>array(
        'type'=>'VARCHAR(100)',
        'null'=>FALSE,
        'default'=>''
      ),
      'field2'=>array(
        'type'=>'VARCHAR(100)',
        'null'=>FALSE,
        'default'=>''
      )
    );
  } 

  private $shard_session; 
  private $shard_router;
  private $shard_schema;

  public static function create_suite() {
    $suite = new Test_Suite('Sharding_Router_Test');
    $suite->add(new Sharding_Router_Test('test_has_table'));
    $suite->add(new Sharding_Router_Test('test_insert'));
    $suite->add(new Sharding_Router_Test('test_update'));
    $suite->add(new Sharding_Router_Test('test_get'));
    $suite->add(new Sharding_Router_Test('test_delete'));
    return $suite;
  }
}

