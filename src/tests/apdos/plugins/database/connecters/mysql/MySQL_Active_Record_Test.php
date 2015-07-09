<?php
namespace tests\apdos\plugins\database\connecters\mysql;

use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\MySQL_Session;
use apdos\tools\ash\Tool_Config;
use apdos\plugins\database\base\rdb\errors\RDB_Error;

class MySQL_Active_Record_Test extends Test_Case {
  const TEST_TABLE_NAME = 'test_table';

  public function test_insert() {
    $data = array(
      'title'=>'test_title'
    );
    $result = $this->session->get_connecter()->insert(self::TEST_TABLE_NAME, $data);
    $this->assert($result->is_success(), 'Insert result is success');
    $query = 'SELECT title FROM ' . self::TEST_TABLE_NAME . ' WHERE title=\'test_title\'';
    $result = $this->session->get_connecter()->query($query);
    $this->assert($result->get_rows_count() == 1, 'Insert count is 1');
  }

  public function test_insert_fail() {
    $data = array(
      'title'=>'test_title',
      'name'=>'test_name'
    );
    $query_faield = false;
    try {
      $result = $this->session->get_connecter()->insert(self::TEST_TABLE_NAME, $data);
    }
    catch (RDB_Error $e) {
      $query_faield = true;
    }
    $this->assert($query_faield == true, "Unknown field is occur error");
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
    $result = $this->session->get_connecter()->insert_batch(self::TEST_TABLE_NAME, $data);
    $this->assert($result->is_success(), "Insert result is success");
    $query = 'SELECT title FROM ' . self::TEST_TABLE_NAME;
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

    $query_faield = false;
    try {
      $result = $this->session->get_connecter()->insert_batch(self::TEST_TABLE_NAME, $data);
    }
    catch (RDB_Error $e) {
      $query_faield = true;
    }
    $this->assert($query_faield == true, "Unknown field is occur error");

    $result = $this->session->get_connecter()->insert_batch(self::TEST_TABLE_NAME, array());
    $this->assert($result->is_success() == false, "Insert result is failed");
    $query = 'SELECT title FROM ' . self::TEST_TABLE_NAME;
    $result = $this->session->get_connecter()->query($query);
    $this->assert($result->get_rows_count() == 0, 'Insert count is 0');
  }

  public function test_get() {
    $this->insert_test_data();
  
    $result = $this->session->get_connecter()->get(self::TEST_TABLE_NAME);
    $this->assert($result->get_rows_count() == 2, "Get rows count is 2");

    $result = $this->session->get_connecter()->get(self::TEST_TABLE_NAME, 1, 0);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");

    $result = $this->session->get_connecter()->get(self::TEST_TABLE_NAME, 222, 1);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");

    $result = $this->session->get_connecter()->get(self::TEST_TABLE_NAME, 1, 9999);
    $this->assert($result->get_rows_count() == 0, "Get rows count is 0");
  }

  public function test_limit() {
    $this->insert_test_data();

    $result = $this->session->get_connecter()->limit(1, 0)->get(self::TEST_TABLE_NAME);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
  }

  public function test_get_where() {
    $this->insert_test_data();
    $result = $this->session->get_connecter()->get_where(self::TEST_TABLE_NAME, array('title'=>'test_title1'));
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $data = $result->get_rows();
    $this->assert($data[0]['title'] == 'test_title1', 'title value is test_title1');

    $result = $this->session->get_connecter()->get_where(self::TEST_TABLE_NAME, array('title'=>'test_title1'), 999, 0);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $data = $result->get_rows();
    $this->assert($data[0]['title'] == 'test_title1', 'title value is test_title1');

    $result = $this->session->get_connecter()->get_where(self::TEST_TABLE_NAME, array('title'=>'test_title2'));
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $data = $result->get_rows();
    $this->assert($data[0]['title'] == 'test_title2', 'title value is test_title1');
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
    $this->session->get_connecter()->insert_batch(self::TEST_TABLE_NAME, $data);
  }

  public function test_delete() {
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

    $this->session->get_schema()->create_table(self::TEST_TABLE_NAME, $this->get_fields());
  }

  private function get_fields() {
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
    $suite->add(new MySQL_Active_Record_Test('test_delete'));
    return $suite;
  }

  private $actor;
  private $session;
}

