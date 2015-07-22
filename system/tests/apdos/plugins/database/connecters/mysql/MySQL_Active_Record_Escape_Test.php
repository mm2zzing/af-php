<?php
namespace tests\apdos\plugins\database\connecters\mysql;

use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\MySQL_Session;
use apdos\tools\ash\Tool_Config;
use apdos\plugins\database\base\rdb\errors\RDB_Error;

class MySQL_Active_Record_Escape_Test extends Test_Case {
  const TABLE = 'test_table';

  public function test_update_escape_case1() {
    $this->insert_test_data();

    $this->session->get_connecter()->toggle_escape_query(false);
    $result = $this->update_injection_case1();
    $this->assert($result->get_rows_count() == 0, 'Update exists success');
    $this->assert_update_all_row();
    

    $this->session->get_schema()->drop_table(self::TABLE);
    $this->session->get_schema()->create_table(self::TABLE, $this->get_test_fields());

    $this->insert_test_data();
    $this->session->get_connecter()->toggle_escape_query(true);
    $result = $this->update_injection_case1();
    $this->assert($result->get_rows_count() == 0, 'Update exists success');
    $this->assert_update_protection();
  }

  /**
   * 인젝션 코드 생성. 조건문 title='test_title2'를 이용하여 다른 row 갱신 시도
   *
   * escape 기능에 의해 특문은 모두 이스케이프 처리가 되면서 모든 문자열을 하나의 조건절로 처리
   * mysql 파싱시 OR문이 실행되지는 않으나 조건문 id=1은 처리된다.
   *
   * SELECT * FROM test_table WHERE id='1' OR title='test_title2'
   * 
   * (FILTERING후) SELECT * FROM test_table WHERE id='1\' OR title=\'test_title2\' -- '
   */
  private function update_injection_case1() {
    $data = array('count'=>1000);
    return $this->session->get_connecter()->where('id', "1' OR title='test_title2'-- ")->update(self::TABLE, $data);
  }

  public function test_update_escape_case2() {
    $this->insert_test_data();

    $this->session->get_connecter()->toggle_escape_query(false);
    $result = $this->update_injection_case2();
    $this->assert($result->get_rows_count() == 0, 'Update exists success');
    $this->assert_update_all_row();
    

    $this->session->get_schema()->drop_table(self::TABLE);
    $this->session->get_schema()->create_table(self::TABLE, $this->get_test_fields());

    $this->insert_test_data();
    $this->session->get_connecter()->toggle_escape_query(true);
    $result = $this->update_injection_case2();
    $this->assert($result->get_rows_count() == 0, 'Update exists success');
    $this->assert_update_protection();

  }


  /**
   * 인젝션 코드 생성. 조건문 1=1 을 이용한 모든 row 갱신 시도
   *
   * escape 기능에 의해 특문은 모두 이스케이프 처리가 되면서 모든 문자열을 하나의 조건절로 처리
   * mysql 파싱시 OR문이 실행되지는 않으나 조건문 id=1은 처리된다.
   *
   * SELECT * FROM test_table WHERE id='1' OR 1=1 -- '
   * 
   * (FILTERING후) SELECT * FROM test_table WHERE id='1\' OR 1=1 --'
   */
  private function update_injection_case2() {
    $data = array('count'=>1000);
    return $this->session->get_connecter()->where('id', "1' OR 1=1 -- ")->update(self::TABLE, $data);
  }

  private function assert_update_all_row() {
    $result = $this->session->get_connecter()->where('id', 1)->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, 'Rows count is 1');
    $this->assert($result->get_row(0, 'id') == 1, "Id is 1");
    $this->assert($result->get_row(0, 'count') == 1000, "Count update to 1000");

    $result = $this->session->get_connecter()->where('id', 2)->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, 'Rows count is 1');
    $this->assert($result->get_row(0, 'id') == 2, "Id is 2");
    $this->assert($result->get_row(0, 'count') == 1000, "Count update to 1000 by injection");
  }

  private function assert_update_protection() {
    $result = $this->session->get_connecter()->where('id', 1)->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, 'Rows count is 1');
    $this->assert($result->get_row(0, 'id') == 1, "Id is 1");
    $this->assert($result->get_row(0, 'count') == 1000, "Count is 1000");
    // skip by second condition escape 
    $result = $this->session->get_connecter()->where('id', 2)->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, 'Rows count is 1');
    $this->assert($result->get_row(0, 'id') == 2, "Id is 2");
    $this->assert($result->get_row(0, 'count') == 200, "Count is 200");
  }

  public function test_like_escape() {
    $this->insert_test_data();

    $this->session->get_connecter()->toggle_escape_query(false);
    $result = $this->session->get_connecter()->like('title', "test_title%")->get(self::TABLE);
    $this->assert($result->get_rows_count() == 2, 'Rows count is 2');

    $this->session->get_connecter()->toggle_escape_query(true);
    $result = $this->session->get_connecter()->like('title', "test_title%")->get(self::TABLE);
    $this->assert($result->get_rows_count() == 0, 'Rows count is 0');
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
    $suite = new Test_Suite('MySQL_Active_Record_Escape_Test');
    $suite->add(new MySQL_Active_Record_Escape_Test('test_update_escape_case1'));
    $suite->add(new MySQL_Active_Record_Escape_Test('test_update_escape_case2'));
    $suite->add(new MySQL_Active_Record_Escape_Test('test_like_escape'));
    //$suite->add(new MySQL_Active_Record_Escape_Test('test_multiple_query_injection'));
    return $suite;
  }

  private $actor;
  private $session;
}

