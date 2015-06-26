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
    $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/srouter');
    $this->shard_router = $actor->add_component('apdos\plugins\sharding\Shard_Router');
    $this->shard_router->load($this->get_shard_tables(), $this->get_lookup_shards(), $this->get_data_shards());

    $this->shard_schema = Component::create('/tests/sharding/shard_schema', 'apdos\plugins\sharding\Shard_Schema');
    $this->shard_schema->set_property('router', $this->shard_router);
  }

  public function get_shard_tables() {
    return Tool_Config::get_instance()->get('test_sharding.shard_tables');
  }

  public function get_lookup_shards() {
    return Tool_Config::get_instance()->get('test_sharding.lookup_shards');
  }

  public function get_data_shards() {
    return Tool_Config::get_instance()->get('test_sharding.data_shards');
  }

  public function tear_down() {
  }

  /**
   * Mock_Sql_Connecter를 이용하여 테스트.
   * Shard_Router에서 shard connecter와 매칭되는 Sql_Connecter종류를 설정할 수 있는 방법을 제공하여
   * 이를 Mock 객체로 설정하여 테스트
   */
  public function test_create_lookup_shards() {
    $this->shard_schema->create_shard();

    $db_util = $shard_router->get_db_util('lookup01');
    $this->assert(true == $db_util->database_exists('test_lookup01'));
    $db_util = $shard_router->get_db_util('lookup02');
    $this->assert(true == $db_util->database_exists('test_lookup02'));
    $db_util = $shard_router->get_db_util('data01');
    $this->assert(true == $db_util->database_exists('test_data01'));
    $db_util = $shard_router->get_db_util('data02');
    $this->assert(true == $db_util->database_exists('test_data02'));
    $db_util = $shard_router->get_db_util('public01');
    $this->assert(true == $db_util->database_exists('test_public01'));

    $this->shard_schema->drop_shard();
  }

  private function get_fields() {
    return array(
      'title'=>array(
        'type'=>'varchar(100)',
        'null'=>false,
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
    $suite->add(new Sharding_Test('test_create_shard'));
    //$suite->add(new Sharding_Test('test_create_lookup_shards')):
    //$suite->add(new Sharding_Test('test_create_data_shards'));
    //$suite->add(new Sharding_Test('test_insert'));
    return $suite;
  }
}

