<?php
namespace tests\apdos\plugins\database\connecters\mysql;

use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\MySQL_Session;
use apdos\tools\ash\Tool_Config;
use apdos\plugins\database\base\rdb\errors\RDB_Error;

class MySQL_Active_Record_Where_Test extends Test_Case {
  const TABLE = 'test_table';

  public function test_where() {
    $this->insert_test_data();

    $this->session->get_connecter()->where('title', 'test_title1');
    $result = $this->session->get_connecter()->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $data = $result->get_rows();
    $this->assert($data[0]['title'] == 'test_title1', 'title value is test_title1');
    $this->assert($data[0]['percent'] == 1.0, 'percent is 1.0');

    $this->session->get_connecter()->where('count', 500);
    $result = $this->session->get_connecter()->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $data = $result->get_rows();
    $this->assert($data[0]['title'] == 'test_title2', 'title value is test_title1');
    $this->assert($data[0]['percent'] == 1.1, 'percent is 1.1');

    $this->session->get_connecter()->where('percent', 1.0);
    $result = $this->session->get_connecter()->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $data = $result->get_rows();
    $this->assert($data[0]['title'] == 'test_title1', 'title value is test_title1');
    $this->assert($data[0]['percent'] == 1.0, 'percent is 1.0');

    $this->session->get_connecter()->where('CAST(percent AS DECIMAL(20,5))', 1.22222);
    $result = $this->session->get_connecter()->get(self::TABLE);
    $data = $result->get_rows();
    $this->assert($data[0]['title'] == 'test_title3', 'title value is test_title3');
    $this->assert($data[0]['percent'] == 1.22222, 'percent is 1.22222');
  }

  public function test_like() {
    $this->insert_test_data();

    $this->session->get_connecter()->like('percent', 1.222);
    $result = $this->session->get_connecter()->get(self::TABLE);
    $this->assert($result->get_rows_count() == 0, 'rows count is 0');

    $this->session->get_connecter()->w_like('percent', 1.222);
    $result = $this->session->get_connecter()->get(self::TABLE);
    $this->assert($result->get_rows_count() == 0, 'rows count is 0');

    $this->session->get_connecter()->w_like_w('percent', 1.222);
    $result = $this->session->get_connecter()->get(self::TABLE);
    $data = $result->get_rows();
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $this->assert($data[0]['title'] == 'test_title3', 'title value is test_title3');
    $this->assert($data[0]['percent'] == 1.22222, 'percent is 1.22222');

    $this->session->get_connecter()->like_w('percent', 1.222);
    $result = $this->session->get_connecter()->get(self::TABLE);
    $data = $result->get_rows();
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $this->assert($data[0]['title'] == 'test_title3', 'title value is test_title3');
    $this->assert($data[0]['percent'] == 1.22222, 'percent is 1.22222'); 
  }

  public function test_or_like() {
    $this->insert_test_data();

    $this->session->get_connecter()->like('percent', 1.222);
    $this->session->get_connecter()->or_like('title', 'test_title1');
    $result = $this->session->get_connecter()->get(self::TABLE);
    $data = $result->get_rows();
    $this->assert($result->get_rows_count() == 1, "Get rows count is 1");
    $this->assert($data[0]['title'] == 'test_title1', 'title value is test_title1');
    $this->assert($data[0]['percent'] == 1.0, 'percent is 1.0');
  }

  public function test_not_like() {
    $this->insert_test_data();

    $this->session->get_connecter()->not_like('title', 'test_title1');
    $result = $this->session->get_connecter()->get(self::TABLE);
    $data = $result->get_rows();
    $this->assert($result->get_rows_count() == 2, "Get rows count is 2");
  }

  private function insert_test_data() {
    $data = array(
      array(
        'title'=>'test_title1',
        'count'=>100,
        'percent'=>1.0,
      ),
      array(
        'title'=>'test_title2',
        'count'=>500,
        'percent'=>1.1,
      ),
      array(
        'title'=>'test_title3',
        'count'=>900,
        'percent'=>1.22222,
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
      ),
      'percent'=>array(
        'type'=>'FLOAT',
        'null'=>FALSE,
        'default'=>0.0
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
    $suite = new Test_Suite('MySQL_Active_Record_Where_Test');
    $suite->add(new MySQL_Active_Record_Where_Test('test_where'));
    $suite->add(new MySQL_Active_Record_Where_Test('test_like'));
    $suite->add(new MySQL_Active_Record_Where_Test('test_or_like'));
    $suite->add(new MySQL_Active_Record_Where_Test('test_not_like'));
    return $suite;
  }

  private $actor;
  private $session;
}

