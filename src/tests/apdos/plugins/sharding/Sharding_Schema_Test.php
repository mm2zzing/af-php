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
 * @class Sharding_Schema_Test
 *
 * @brief 샤딩 유닛 테스트
 */
class Sharding_Schema_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function set_up() {
    $this->actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/srouter');
    $this->shard_session = $this->actor->add_component(Shard_Session::get_class_name());
    $this->shard_session->set_property('db_session_class_name', MySQL_Session::get_class_name());

    $this->shard_schema = $this->actor->add_component(Shard_Schema::get_class_name());
    $this->shard_config = $this->actor->add_component(Shard_Config::get_class_name());

    $this->shard_config->load($this->get_shard_tables(), $this->get_shard_sets(), $this->get_shards()); 

    $this->actor->update_events();
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

  public function test_create_database() {
    $this->shard_schema->create_database();

    $db_schema = $this->shard_session->get_db_schema(new Shard_ID('lookup01'));
    $this->assert(true == $db_schema->has_database('test_lookup01'), 'test_lookup01 is exist');
    $db_schema = $this->shard_session->get_db_schema(new Shard_ID('lookup02'));
    $this->assert(true == $db_schema->has_database('test_lookup02'), 'test_lookup02 is exist');

    $this->shard_schema->drop_database();
  }

  public function test_has_database() {
    $this->assert(false == $this->shard_schema->has_database());

    $this->shard_schema->create_database();
    $this->assert(true == $this->shard_schema->has_database());
  }


  public function test_drop_database() {
    $this->shard_schema->create_database();
    $this->assert(true == $this->shard_schema->has_database());
    $this->shard_schema->drop_database();
    $this->assert(false == $this->shard_schema->has_database());
  }

  public function test_create_lookup_table() {
    $this->shard_schema->create_database();

    $db_schema = $this->shard_session->get_db_connecter(new Shard_ID('lookup01'));
    $this->assert(false == $db_schema->has_table('lookup'), 'lookup table is exist');
    $db_schema = $this->shard_session->get_db_connecter(new Shard_ID('lookup02'));
    $this->assert(false == $db_schema->has_table('lookup'), 'lookup table is exist');

    $result = $this->create_lookup_table();
    $this->assert(true == $result, 'craete lookup table is success');
    $db_schema = $this->shard_session->get_db_connecter(new Shard_ID('lookup01'));
    $this->assert(true == $db_schema->has_table('lookup'), 'lookup table is exist');
    $db_schema = $this->shard_session->get_db_connecter(new Shard_ID('lookup02'));
    $this->assert(true == $db_schema->has_table('lookup'), 'lookup table is exist');
  }

  public function test_drop_lookup_table() {
    $this->shard_schema->create_database();

    $result = $this->create_lookup_table();
    $this->assert(true == $result, 'craete lookup table is success');
    $db_schema = $this->shard_session->get_db_connecter(new Shard_ID('lookup01'));
    $this->assert(true == $db_schema->has_table('lookup'), 'lookup table is exist');
    $db_schema = $this->shard_session->get_db_connecter(new Shard_ID('lookup02'));
    $this->assert(true == $db_schema->has_table('lookup'), 'lookup table is exist');

    $result = $this->drop_lookup_table(); 
    $this->assert(true == $result, 'drop lookup table is success');
    $db_schema = $this->shard_session->get_db_connecter(new Shard_ID('lookup01'));
    $this->assert(false == $db_schema->has_table('lookup'), 'lookup table is exist');
    $db_schema = $this->shard_session->get_db_connecter(new Shard_ID('lookup02'));
    $this->assert(false == $db_schema->has_table('lookup'), 'lookup table is exist');
  }

  public function test_create_table() { 
    $this->shard_schema->create_database();

    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_a01'));
    $this->assert(false == $db_connecter->has_table('table_a'), 'data table is not exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_a02'));
    $this->assert(false == $db_connecter->has_table('table_a'), 'data table is not exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_b01'));
    $this->assert(false == $db_connecter->has_table('table_a'), 'data table is not exist');

    $result = $this->create_table(new Table_ID('table_a'), $this->get_data_fields());

    $this->assert(true == $result, 'create data tables is success');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_a01'));
    $this->assert(true == $db_connecter->has_table('table_a'), 'data table is exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_a02'));
    $this->assert(true == $db_connecter->has_table('table_a'), 'data table is exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_b01'));
    $this->assert(false == $db_connecter->has_table('table_a'), 'data table is not exist');


    $result = $this->create_table(new Table_ID('table_b'), $this->get_data_fields());
    $this->assert(true == $result, 'create data tables is success');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_a01'));
    $this->assert(false == $db_connecter->has_table('table_b'), 'data table is not exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_a02'));
    $this->assert(false == $db_connecter->has_table('table_b'), 'data table is not exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_b01'));
    $this->assert(true == $db_connecter->has_table('table_b'), 'data table is exist');

    $this->shard_schema->drop_database();
  }

  public function test_drop_table() {
    $this->shard_schema->create_database();
    $this->create_lookup_table();
    $this->create_table(new Table_ID('table_a'), $this->get_data_fields());
    $this->create_table(new Table_ID('table_b'), $this->get_data_fields());
    
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_a01'));
    $this->assert(true == $db_connecter->has_table('table_a'), 'data table is exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_a02'));
    $this->assert(true == $db_connecter->has_table('table_a'), 'data table is exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_b01'));
    $this->assert(true == $db_connecter->has_table('table_b'), 'data table is exist');

    $this->shard_schema->drop_table(new Table_ID('table_a'));

    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_a01'));
    $this->assert(false == $db_connecter->has_table('table_a'), 'data table is not exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_a02'));
    $this->assert(false == $db_connecter->has_table('table_a'), 'data table is not exist');
    $db_connecter = $this->shard_session->get_db_connecter(new Shard_ID('table_b01'));
    $this->assert(true == $db_connecter->has_table('table_b'), 'data table is exist');
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

  private function create_lookup_table() {
    try {
      $this->shard_schema->create_lookup_table();
    }
    catch (Sharding_Error $e) {
      return false;
    }
    return true;
  }

  private function drop_lookup_table() {
    try {
      $this->shard_schema->drop_lookup_table();
    }
    catch (Sharding_Error $e) {
      return false;
    }
    return true;
  }

  private function create_table($table_id, $fields) {
    try {
      $this->shard_schema->create_table($table_id, $fields);
    }
    catch (Sharding_Error $e) {
      return false;
    }
    return true;
  }

  private $shard_session; 
  private $shard_schema;

  public static function create_suite() {
    $suite = new Test_Suite('Sharding_Schema_Test');
    $suite->add(new Sharding_Schema_Test('test_create_database'));
    $suite->add(new Sharding_Schema_Test('test_has_database'));
    $suite->add(new Sharding_Schema_Test('test_drop_database'));
    $suite->add(new Sharding_Schema_Test('test_create_lookup_table'));
    $suite->add(new Sharding_Schema_Test('test_drop_lookup_table'));
    $suite->add(new Sharding_Schema_Test('test_create_table'));
    $suite->add(new Sharding_Schema_Test('test_drop_table'));
    return $suite;
  }
}

