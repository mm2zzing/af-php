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
use apdos\kernel\objectid\Shard_ID;
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
    $this->assert_false($this->shard_router->has_lookup_table());
    $this->assert_false($this->shard_router->has_lookup_table());

    $this->shard_schema->create_lookup_table();
    $this->assert_true($this->shard_router->has_lookup_table());
    $this->assert_true($this->shard_router->has_lookup_table());
    $this->assert_false($this->shard_router->has_table(new Table_ID('table_a')));
    $this->assert_false($this->shard_router->has_table(new Table_ID('table_b')));

    $this->shard_schema->create_table(new Table_ID('table_a'), $this->get_data_fields());
    $this->shard_schema->create_table(new Table_ID('table_b'), $this->get_data_fields());
    $this->assert_true($this->shard_router->has_table(new Table_ID('table_a')));
    $this->assert_true($this->shard_router->has_table(new Table_ID('table_b')));
  }

  public function test_insert() {
    $this->preapre_data_schema();
    $this->assert_equal(0, $this->shard_session->get_db_connecter(new Shard_ID('table_a01'))->count('table_a'), 'table_a count is 0');
    $this->assert_equal(0, $this->shard_session->get_db_connecter(new Shard_ID('table_a02'))->count('table_a'), 'table_a count is 0');
    $this->assert_equal(0, $this->shard_session->get_db_connecter(new Shard_ID('table_b01'))->count('table_b'), 'table_a count is 0');

    $data = array('field1'=>'foo', 'field2'=>'bar');
    $this->shard_router->insert(new Table_ID('table_a'), $data);
    $this->assert_equal(1, $this->get_table_row_count(new Table_ID('table_a')), 'row count is 1');
    $this->assert_equal(0, $this->get_table_row_count(new Table_ID('table_b')), 'row count is 0');
    $this->assert_object_ids(array(new Table_ID('table_a'), new Table_ID('table_b')));

    $this->shard_router->insert(new Table_ID('table_b'), $data);
    $this->assert_equal(1, $this->get_table_row_count(new Table_ID('table_a')), 'row count is 1');
    $this->assert_equal(1, $this->get_table_row_count(new Table_ID('table_b')), 'row count is 1');
    $this->assert_object_ids(array(new Table_ID('table_a'), new Table_ID('table_b')));

    $this->shard_router->insert(new Table_ID('table_b'), $data);
    $this->assert_equal(1, $this->get_table_row_count(new Table_ID('table_a')), 'row count is 1');
    $this->assert_equal(2, $this->get_table_row_count(new Table_ID('table_b')), 'row count is 2');
    $this->assert_object_ids(array(new Table_ID('table_a'), new Table_ID('table_b')));
  }

  private function preapre_data_schema() {
    $this->shard_schema->create_lookup_table();
    $this->shard_schema->create_table(new Table_ID('table_a'), $this->get_data_fields());
    $this->shard_schema->create_table(new Table_ID('table_b'), $this->get_data_fields());
  }

  private function get_table_row_count($table_id) {
    $table = $this->shard_config->get_table($table_id);
    $shard_set = $this->shard_config->get_shard_set($table->get_shard_set_id());
    $count = 0;
    foreach ($shard_set->get_data_shard_ids() as $shard_id) {
      $db_connecter = $this->shard_session->get_db_connecter($shard_id);
      $count += $db_connecter->count($table_id->to_string());
    }
    return $count;
  }

  private function assert_object_ids($table_ids) {
    $data = array();
    foreach ($table_ids as $id) 
      $data = array_merge($data, $this->get_table_rows($id));
    $ids = array();
    foreach ($data as $element)
      array_push($ids, $element['object_id']);
    $ids = array_unique($ids);
    $this->assert(count($ids) == count($data), "Ids is not duplicated");
  }

  private function get_table_rows($table_id) {
    $table = $this->shard_config->get_table($table_id);
    $shard_set = $this->shard_config->get_shard_set($table->get_shard_set_id());
    $result = array();
    foreach ($shard_set->get_data_shard_ids() as $shard_id) {
      $db_connecter = $this->shard_session->get_db_connecter($shard_id);
      $mysql_result = $db_connecter->get($table_id->to_string());
      $result = array_merge($result, $mysql_result->get_rows());
    }
    return $result;
  } 

  public function test_get() {
    $this->preapre_data_schema();
    $data = array('field1'=>'foo1', 'field2'=>'bar1');
    $this->shard_router->insert(new Table_ID('table_a'), $data);
    $this->shard_router->insert(new Table_ID('table_a'), $data);
    $data = array('field1'=>'foo2', 'field2'=>'bar2');
    $this->shard_router->insert(new Table_ID('table_b'), $data);

    $result = $this->shard_router->get(new Table_ID('table_a'));
    $this->assert($result->get_rows_count() == 2, 'table_a has 2 data');
  }

  public function test_get_where() {
    $this->preapre_data_schema();
    $data = array('field1'=>'foo1', 'field2'=>'bar1');
    $this->shard_router->insert(new Table_ID('table_a'), $data);
    $this->shard_router->insert(new Table_ID('table_a'), $data);
    $data = array('field1'=>'foo2', 'field2'=>'bar2');
    $this->shard_router->insert(new Table_ID('table_b'), $data);

    $get_result = $this->shard_router->get(new Table_ID('table_a'));
    $shard_object_ids = array();
    foreach ($get_result->get_rows() as $row)
      array_push($shard_object_ids, $row['object_id']);

    $get_where_result = $this->shard_router->get_where(new Table_ID('table_a'), array('object_id'=>$row['object_id']));
    $this->assert(count($get_where_result) == 1, 'result count is 1');
    $this->assert($get_where_result->get_row(0, 'object_id') == $row['object_id']);
  }

  public function test_update() {
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
    $suite->add(new Sharding_Router_Test('test_get'));
    //$suite->add(new Sharding_Router_Test('test_get_where'));
    $suite->add(new Sharding_Router_Test('test_update'));
    $suite->add(new Sharding_Router_Test('test_delete'));
    return $suite;
  }
}

