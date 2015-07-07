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

/**
 * @class Sharding_Schema_Test
 *
 * @brief 샤딩 유닛 테스트
 *        $shard_schema->create_shard_database();
 *        $shard_schema->create_lookup_table();
 *        $shard_schema->create_data_table('mytable1', $this->get_fields());
 *        $shard_schema->drop_data_table('mytable');
 *        $shard_schema->drop_shard_database();
 */
class Sharding_Schema_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function set_up() {
    $this->actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/srouter');
    $this->shard_session = $this->actor->add_component(Shard_Session::get_class_name());
    $this->shard_schema = $this->actor->add_component(Shard_Schema::get_class_name());
    $this->shard_config = $this->actor->add_component(Shard_Config::get_class_name());
    $this->shard_router = $this->actor->add_component(Shard_Router::get_class_name());

    $this->shard_config->load($this->get_shard_tables(), $this->get_shard_sets()); 
  }

  public function get_shard_tables() {
    return Tool_Config::get_instance()->get('test_sharding.tables');
  }

  public function get_shard_sets() {
    return Tool_Config::get_instance()->get('test_sharding.shards');
  }

  public function tear_down() {
    $this->actor->release();
  }

  public function test_create_database() {
    $this->shard_schema->create_database(new Shard_ID('lookup01'));
    $this->shard_schema->create_database(new Shard_ID('lookup02'));

    $db_schema = $this->shard_session->get_db_schema(new Shard_ID('lookup01'));
    $this->assert(true == $db_schema->has_database('test_lookup01'), 'test_lookup01 is exist');
    $db_schema = $this->shard_session->get_db_schema(new Shard_ID('lookup02'));
    $this->assert(true == $db_schema->has_database('test_lookup02'), 'test_lookup02 is exist');

    $this->shard_schema->drop_databases(new Shard_ID('lookup02'));
    $this->shard_schema->drop_databases(new Shard_ID('lookup01'));
  }

  public function test_create_databases() {
    $this->shard_schema->create_databases();

    $db_schema = $this->shard_session->get_db_schema(new Shard_ID('lookup01'));
    $this->assert(true == $db_schema->has_database('test_lookup01'), 'test_lookup01 is exist');
    $db_schema = $this->shard_session->get_db_schema(new Shard_ID('lookup02'));
    $this->assert(true == $db_schema->has_database('test_lookup02'), 'test_lookup02 is exist');

    $this->shard_schema->drop_databases();
  }

  public function test_create_table() { 
    $this->shard_schema->create_databases();
    $this->shard_router->select_databases();

    $this->shard_schema->create_table(new Shard_ID('lookup01'), 'lookup', $this->get_lookup_fields());
    $this->shard_schema->create_table(new Shard_ID('lookup02'), 'lookup', $this->get_lookup_fields());

    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('lookup01'));
    $this->assert(true == $db_connecter->has_table('lookup'), 'lookup table is exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('lookup02'));
    $this->assert(true == $db_connecter->has_table('lookup'), 'lookup table is exist');

    $this->shard_schema->drop_databases();
  }

  public function test_create_tables() {
    $this->shard_schema->create_databases();
    $this->shard_router->select_databases();

    $shard_ids = new Shard_IDs();
    $shard_ids->add(new Shard_ID('lookup01'));
    $shard_ids->add(new Shard_ID('lookup02'));
    $this->shard_schema->create_tables($shard_ids, 'lookup', $this->get_lookup_fields());

    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('lookup01'));
    $this->assert(true == $db_connecter->has_table('lookup'), 'lookup table is exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('lookup02'));
    $this->assert(true == $db_connecter->has_table('lookup'), 'lookup table is exist');

    $this->shard_schema->drop_databases();
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

  private $shard_session; 
  private $shard_router;
  private $shard_schema;

  public static function create_suite() {
    $suite = new Test_Suite('Sharding_Schema_Test');
    $suite->add(new Sharding_Schema_Test('test_create_database'));
    $suite->add(new Sharding_Schema_Test('test_create_databases'));
    $suite->add(new Sharding_Schema_Test('test_create_table'));
    $suite->add(new Sharding_Schema_Test('test_create_tables'));
    return $suite;
  }
}

