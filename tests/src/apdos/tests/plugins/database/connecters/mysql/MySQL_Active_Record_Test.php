<?php
namespace apdos\tests\plugins\database\connecters\mysql;

use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\MySQL_Session;
use apdos\tools\ash\Tool_Config;
use apdos\plugins\database\base\rdb\errors\RDB_Error;

class MySQL_Active_Record_Test extends Test_Case {
  const TABLE = 'test_table';

  public function test_insert() {
    $data = array(
      'title'=>'test_title'
    );
    $result = $this->session->get_connecter()->insert(self::TABLE, $data);
    $this->assert($result->get_rows_count() == 0, 'Insert result is success but not has data');
    $query = 'SELECT title FROM ' . self::TABLE . ' WHERE title=\'test_title\'';
    $result = $this->session->get_connecter()->query($query);
    $this->assert($result->get_rows_count() == 1, 'Insert count is 1');
  }

  public function test_insert_fail() {
    $data = array(
      'title'=>'test_title',
      'name'=>'test_name'
    );

    $exception = false;
    try {
      $this->session->get_connecter()->insert(self::TABLE, $data);
    }
    catch (RDB_Error $e) {
      $exception = true;
    }
    $this->assert($exception == true, "Unknown field is occur error");
  }

  public function test_insert_batch() {
    $data = array(
      array(
        'title'=>'test_title1'
      ),
      array(
        'title'=>'test_title1'
      )
    );
    $result = $this->session->get_connecter()->insert_batch(self::TABLE, $data);
    $this->assert($result->get_rows_count() == 0, "Insert result is success. but not has data");
    $query = 'SELECT title FROM ' . self::TABLE;
    $result = $this->session->get_connecter()->query($query);
    $this->assert($result->get_rows_count() == 2, 'Insert count is 2');
  }

  public function test_insert_batch_fail() {
    $data = array(
      array(
        'title'=>'test_title1',
        'name'=>'test_name'
      ),
      array(
        'title'=>'test_title1',
        'name'=>'test_name'
      )
    );

    $exception = false;
    try {
      $result = $this->session->get_connecter()->insert_batch(self::TABLE, $data);
    }
    catch (RDB_Error $e) {
      $exception = true;
    }
    $this->assert($exception == true, "Unknown field is occur error");

    $exception = false;
    try {
      $result = $this->session->get_connecter()->insert_batch(self::TABLE, array());
    }
    catch (RDB_Error $e) {
      $exception = true;
    }
    $this->assert($exception == true, "Insert result is failed");
    $query = 'SELECT title FROM ' . self::TABLE;
    $result = $this->session->get_connecter()->query($query);
    $this->assert($result->get_rows_count() == 0, 'Insert count is 0');
  }

  public function test_get() {
    $this->insert_test_data();
  
    $result = $this->session->get_connecter()->get(self::TABLE);
    $this->assert($result->get_rows_count() == 2, "Get rows count is 2");

    $result = $this->session->get_connecter()->get(self::TABLE, 1, 0);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");

    $result = $this->session->get_connecter()->get(self::TABLE, 222, 1);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");

    $result = $this->session->get_connecter()->get(self::TABLE, 1, 9999);
    $this->assert($result->get_rows_count() == 0, "Get rows count is 0");
  }

  public function test_join() {
    $this->insert_test_join_data();

    $table_name = self::TABLE;
    $join_table_name = self::TEST_JOIN_TABLE;
    $join_compare = "$join_table_name.test_table_id = $table_name.id";
    $result = $this->session->get_connecter()->join($join_table_name, $join_compare)->get($table_name);

    $this->assert($result->get_rows_count() == 1, "Join row count is 1");
    $row = $result->get_row(0);
    $this->assert($row['id' ] == 1, 'Join row id is 1');
  }

  public function test_limit() {
    $this->insert_test_data();

    $result = $this->session->get_connecter()->limit(1, 0)->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
  }

  public function test_get_where() {
    $this->insert_test_data();
    $result = $this->session->get_connecter()->get_where(self::TABLE, array('title'=>'test_title1'));
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $data = $result->get_rows();
    $this->assert($data[0]['title'] == 'test_title1', 'title value is test_title1');

    $result = $this->session->get_connecter()->get_where(self::TABLE, array('title'=>'test_title1'), 999, 0);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $data = $result->get_rows();
    $this->assert($data[0]['title'] == 'test_title1', 'title value is test_title1');

    $result = $this->session->get_connecter()->get_where(self::TABLE, array('title'=>'test_title2'));
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $data = $result->get_rows();
    $this->assert($data[0]['title'] == 'test_title2', 'title value is test_title1');
  } 

  public function test_count() {
    $this->insert_test_data();
    $count = $this->session->get_connecter()->count(self::TABLE);
    $this->assert($count == 2, 'Data count is 2');
  }

  public function test_delete() {
    $this->insert_test_data();
    $count = $this->session->get_connecter()->count(self::TABLE);
    $this->assert($count == 2, 'Data count is 2');

    $result = $this->session->get_connecter()->get_where(self::TABLE, array('title'=>'test_title1'));
    $id = $result->get_row(0, 'id');
    $this->session->get_connecter()->delete(self::TABLE, array('id'=>$id));
    $count = $this->session->get_connecter()->count(self::TABLE);
    $this->assert($count == 1, 'Data count is 1');
  }

  public function test_delete_all() {
    $this->insert_test_data();
    $this->session->get_connecter()->delete_all(self::TABLE);
    $count = $this->session->get_connecter()->count(self::TABLE);
    $this->assert($count == 0, 'Data count is 1');
  }

  public function test_select() {
    $this->insert_test_data();
    $result = $this->session->get_connecter()->get_where(self::TABLE, array('title'=>'test_title1'));
    $data = $result->get_row(0);
    $this->assert(array_key_exists('id', $data), "Id field exists");
    $this->assert($data['id'] == 1, "Id is 1");
    $this->assert(array_key_exists('title', $data), "Title field exists");
    $this->assert(array_key_exists('count', $data), "Count field exists");

    $result = $this->session->get_connecter()->select(array('id', 'title'))->get_where(
      self::TABLE, array('title'=>'test_title1'));
    $data = $result->get_row(0);
    $this->assert(array_key_exists('id', $data), "Id field exists");
    $this->assert($data['id'] == 1, "Id is 1");
    $this->assert(array_key_exists('title', $data), "Title field exists");
    $this->assert(!array_key_exists('count', $data), "Count field not exists");

    $result = $this->session->get_connecter()->select(array('id', 'title'))->get(self::TABLE);
    $data = $result->get_row(0);
    $this->assert(array_key_exists('id', $data), "Id field exists");
    $this->assert($data['id'] == 1, "Id is 1");
    $this->assert(array_key_exists('title', $data), "Title field exists");
    $this->assert(!array_key_exists('count', $data), "Count field not exists");
  }

  public function test_select_max() {
    $this->insert_test_select_data();
    $result = $this->session->get_connecter()->select_max('count')->get_where(self::TABLE, 
                                                                              array('title'=>'test_title1'));
    $data = $result->get_row(0);
    $this->assert(array_key_exists('count', $data), 'Count key exists');
    $this->assert($data['count'] == 500, 'Count max is 500');

    $result = $this->session->get_connecter()->select_max('count')->get(self::TABLE);
    $data = $result->get_row(0);
    $this->assert(array_key_exists('count', $data), 'Count key exists');
    $this->assert($data['count'] == 900, 'Count max is 900');

    $result = $this->session->get_connecter()->select_max('count', 'max_count')->get(self::TABLE);
    $data = $result->get_row(0);
    $this->assert(array_key_exists('max_count', $data), 'Count key exists');
    $this->assert($data['max_count'] == 900, 'Count max is 900');
  }

  public function test_select_min() {
    $this->insert_test_select_data();
    $result = $this->session->get_connecter()->select_min('count')->get(self::TABLE);
    $data = $result->get_row(0);
    $this->assert(array_key_exists('count', $data), 'Count key exists');
    $this->assert($data['count'] == 100, 'Count min is 100');

    $result = $this->session->get_connecter()->select_min('count', 'min_count')->get(self::TABLE);
    $data = $result->get_row(0);
    $this->assert(array_key_exists('min_count', $data), 'Count key exists');
    $this->assert($data['min_count'] == 100, 'Count min is 100');
  }

  public function test_select_avg() {
    $this->insert_test_select_data();
    $result = $this->session->get_connecter()->select_avg('count')->get(self::TABLE);
    $data = $result->get_row(0);
    $this->assert(array_key_exists('count', $data), 'Count key exists');
    $this->assert($data['count'] == 500, 'Count avg is 500');

    $result = $this->session->get_connecter()->select_avg('count', 'avg_count')->get(self::TABLE);
    $data = $result->get_row(0);
    $this->assert(array_key_exists('avg_count', $data), 'Count key exists');
    $this->assert($data['avg_count'] == 500, 'Count avg is 500');
  }

  public function test_select_sum() {
    $this->insert_test_select_data();
    $result = $this->session->get_connecter()->select_sum('count')->get(self::TABLE);
    $data = $result->get_row(0);
    $this->assert(array_key_exists('count', $data), 'Count key exists');
    $this->assert($data['count'] == 1500, 'Count sum is 1500');

    $result = $this->session->get_connecter()->select_sum('count', 'sum_count')->get(self::TABLE);
    $data = $result->get_row(0);
    $this->assert(array_key_exists('sum_count', $data), 'Count key exists');
    $this->assert($data['sum_count'] == 1500, 'Count sum is 1500');
  }

  public function test_order_by() {
    $this->insert_test_select_data();

    $result = $this->session->get_connecter()->order_by_asc('count')->get(self::TABLE);
    $this->assert($result->get_rows_count() == 3, 'Rows count is 3');
    $this->assert($result->get_row(0, 'count') == 100, 'count is 100');
    $this->assert($result->get_row(1, 'count') == 500, 'count is 500');
    $this->assert($result->get_row(2, 'count') == 900, 'count is 900');

    $result = $this->session->get_connecter()->order_by_desc('count')->get(self::TABLE);
    $this->assert($result->get_rows_count() == 3, 'Rows count is 3');
    $this->assert($result->get_row(0, 'count') == 900, 'count is 900');
    $this->assert($result->get_row(1, 'count') == 500, 'count is 500');
    $this->assert($result->get_row(2, 'count') == 100, 'count is 100');

    $this->session->get_connecter()->order_by_desc('count');
    $this->session->get_connecter()->order_by_desc('title');
    $result = $this->session->get_connecter()->get(self::TABLE);
    $this->assert($result->get_rows_count() == 3, 'Rows count is 3');
    $this->assert($result->get_row(0, 'count') == 900, 'count is 900');
    $this->assert($result->get_row(1, 'count') == 500, 'count is 500');
    $this->assert($result->get_row(2, 'count') == 100, 'count is 100');
  }

  private function insert_test_data() {
    $data = array(
      array(
        'title'=>'test_title1',
        'count'=>100
      ),
      array(
        'title'=>'test_title2',
        'count'=>200
      )
    );
    $this->session->get_connecter()->insert_batch(self::TABLE, $data);
  }

  private function insert_test_select_data() {
    $data = array(
      array(
        'title'=>'test_title1',
        'count'=>100
      ),
      array(
        'title'=>'test_title1',
        'count'=>500
      ),
      array(
        'title'=>'test_title2',
        'count'=>900
      )
    );
    $this->session->get_connecter()->insert_batch(self::TABLE, $data);
  }

  public function set_up() {
    $this->actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/db/mysql');
    $this->session = $this->actor->add_component(MySQL_Session::get_class_name());

    $this->actor->update_events();

    $host = Tool_Config::get_instance()->get('test_server.mysql-test-db.host');
    $user = Tool_Config::get_instance()->get('test_server.mysql-test-db.user');
    $password = Tool_Config::get_instance()->get('test_server.mysql-test-db.password');
    $port = Tool_Config::get_instance()->get('test_server.mysql-test-db.port');
    $persistent = Tool_Config::get_instance()->get('test_server.mysql-test-db.persistent');
    $this->session->get_connecter()->connect($host, $user, $password, $port, $persistent);
    $this->session->get_schema()->create_database($this->get_db_name());
    $this->session->get_connecter()->select_database($this->get_db_name());

    $this->session->get_schema()->create_table(self::TABLE, $this->get_test_fields());
  }

  private function get_test_fields() {
    return array(
      'id'=>array(
        'type'=>'INT(11)',
        'unsigned'=>TRUE,
        'auto_increment'=>TRUE,
        'null'=>FALSE,
        'primary_key'=>TRUE
      ),
      'title'=>array(
        'type'=>'VARCHAR(100)',
        'null'=>FALSE,
        'default'=>''
      ),
      'count'=>array(
        'type'=>'INT(11)',
        'null'=>FALSE,
        'default'=>0
      )
    );
  }
  
  public function tear_down() {
    $this->session->get_schema()->drop_database($this->get_db_name());
    $this->session->get_connecter()->close();
    Kernel::get_instance()->delete_object($this->actor->get_path());
  }

  private function get_db_name() {
    return Tool_Config::get_instance()->get('test_server.mysql-test-db.db_name');
  }

  public static function create_suite() {
    $suite = new Test_Suite('MySQL_Active_Record_Test');
    $suite->add(new MySQL_Active_Record_Test('test_insert'));
    $suite->add(new MySQL_Active_Record_Test('test_insert_fail'));
    $suite->add(new MySQL_Active_Record_Test('test_insert_batch'));
    $suite->add(new MySQL_Active_Record_Test('test_insert_batch_fail'));
    $suite->add(new MySQL_Active_Record_Test('test_get'));
    $suite->add(new MySQL_Active_Record_Test('test_limit'));
    $suite->add(new MySQL_Active_Record_Test('test_get_where'));
    $suite->add(new MySQL_Active_Record_Test('test_select'));
    $suite->add(new MySQL_Active_Record_Test('test_select_max'));
    $suite->add(new MySQL_Active_Record_Test('test_select_min'));
    $suite->add(new MySQL_Active_Record_Test('test_select_avg'));
    $suite->add(new MySQL_Active_Record_Test('test_select_sum'));
    $suite->add(new MySQL_Active_Record_Test('test_count'));
    $suite->add(new MySQL_Active_Record_Test('test_delete'));
    $suite->add(new MySQL_Active_Record_Test('test_delete_all'));
    $suite->add(new MySQL_Active_Record_Test('test_order_by'));
    return $suite;
  }

  private $actor;
  private $session;
}

