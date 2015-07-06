<?php
namespace tests\apdos\plugins\sharding;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\plugins\sharding\Shard_Router;
use apdos\kernel\core\Object_Converter;
use apdos\tools\ash\Tool_Config;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Kernel;

/**
 * @class Sharding_Test
 *
 * @brief 샤딩 유닛 테스트
 *        $shard_schema->create_shard_database();
 *        $shard_schema->create_lookup_table();
 *        $shard_schema->create_data_table('mytable1', $this->get_fields());
 *        $shard_schema->drop_data_table('mytable');
 *        $shard_schema->drop_shard_database();
 */
class Sharding_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function set_up() {
    $this->actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/srouter');
    $this->shard_router = $this->actor->add_component('apdos\plugins\sharding\Shard_Router');
    $this->shard_router->load($this->get_shard_tables(), $this->get_shard_sets());
    $this->shard_schema = $this->actor->add_component('apdos\plugins\sharding\Shard_Schema');
    $this->shard_schema->set_property('router', $this->shard_router);

    $this->shard_schema->create_database('lookup01', 'test_lookup01');
    $this->shard_schema->create_database('lookup02', 'test_lookup02');
  }

  public function get_shard_tables() {
    return Tool_Config::get_instance()->get('test_sharding.tables');
  }

  public function get_shard_sets() {
    return Tool_Config::get_instance()->get('test_sharding.shards');
  }

  public function tear_down() {
    $db_connecter = $this->shard_router->get_db_connecter('lookup01');
    $this->shard_schema->drop_database('lookup01', 'test_lookup01');
    $db_connecter = $this->shard_router->get_db_connecter('lookup02');
    $this->shard_schema->drop_database('lookup02', 'test_lookup02');

    $this->actor->release();
  }

  public function test_create_lookup_shards() { 
    $db_schema = $this->shard_router->get_db_schema('lookup01');
    $this->assert(true == $db_schema->has_database('test_lookup01'), 'test_lookup01 is exist');
    $db_schema = $this->shard_router->get_db_schema('lookup02');
    $this->assert(true == $db_schema->has_database('test_lookup02'), 'test_lookup02 is exist');


    $db_connecter = $this->shard_router->get_db_connecter('lookup01');
    $db_connecter->select_database('test_lookup01');

    $db_connecter = $this->shard_router->get_db_connecter('lookup02');
    $db_connecter->select_database('test_lookup02');

    $this->shard_schema->create_table('lookup01', 'lookup', $this->get_lookup_fields());
    $this->shard_schema->create_table('lookup02', 'lookup', $this->get_lookup_fields());

    $db_connecter = $this->shard_router->get_db_connecter('lookup01');
    $this->assert(true == $db_connecter->has_table('lookup'), 'lookup table is exist');
    $db_connecter = $this->shard_router->get_db_connecter('lookup02');
    $this->assert(true == $db_connecter->has_table('lookup'), 'lookup table is exist'); 
  }

  private function get_lookup_fields() {
    return array(
      'object_id'=>array(
        'type'=>'VARCHAR(100)',
        'null'=>FALSE,
        'default'=>''
      ),
      'title_id'=>array(
        'type'=>'VARCHAR(100)',
        'null'=>FALSE,
        'default'=>''
      ),
      'state'=>array(
        'type'=>'VARCHAR(100)',
        'null'=>FALSE,
        'default'=>''
      )
    );
  } 
  public function test_create_data_shards() {
  }

  public function test_insert() {
    //Shard_Router::get_instance()->insert('mytable1', $data);
  }
  
  private $shard_router; 
  private $shard_schema;

  public static function create_suite() {
    $suite = new Test_Suite('Sharding_Test');
    $suite->add(new Sharding_Test('test_create_lookup_shards'));
    $suite->add(new Sharding_Test('test_create_data_shards'));
    //$suite->add(new Sharding_Test('test_insert'));
    return $suite;
  }
}

