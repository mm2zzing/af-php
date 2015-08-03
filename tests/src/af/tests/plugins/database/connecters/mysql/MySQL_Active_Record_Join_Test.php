<?php
namespace af\tests\plugins\database\connecters\mysql;

use af\plugins\test\Test_Suite;
use af\kernel\core\kernel;
use af\plugins\test\Test_Case;
use af\plugins\database\connecters\mysql\MySQL_Session;
use af\tools\ash\Tool_Config;
use af\plugins\database\base\rdb\errors\RDB_Error;

class MySQL_Active_Record_Join_Test extends Test_Case {
  const TABLE = 'test_table';
  const JOIN_TABLE = 'test_join_table';

  public function test_join() {
    $this->insert_test_join_data();

    $join_compare = "test_join_table.test_table_id = test_table.id";
    $select_fields = array('test_table.id', 
                           'test_table.count', 
                           'test_table.title', 
                           'test_join_table.test_table_id', 
                           'test_join_table.name');
    $this->session->get_connecter()->select($select_fields);
    $result = $this->session->get_connecter()->join(self::JOIN_TABLE, $join_compare)->get(self::TABLE);
    $this->assert_inner_join($result);
  }

  public function test_inner_join() {
    $this->insert_test_join_data();

    $join_compare = "test_join_table.test_table_id = test_table.id";
    $select_fields = array('test_table.id', 
                           'test_table.count', 
                           'test_table.title', 
                           'test_join_table.test_table_id', 
                           'test_join_table.name');
    $this->session->get_connecter()->select($select_fields);
    $result = $this->session->get_connecter()->join(self::JOIN_TABLE, $join_compare, 'inner')->get(self::TABLE);
    $this->assert_inner_join($result);
  }

  private function assert_inner_join($result) {
    // 두 테이블간에 조건이 겹치는 행의 갯수는 3개이다.
    $this->assert($result->get_rows_count() == 3, "Join row count is 3");

    $this->assert($result->get_row(0, 'id') == 1, 'Join row id is 1');
    $this->assert($result->get_row(0, 'count') == 100, 'Join count is 100');
    $this->assert($result->get_row(0, 'title') == 'test_title1', 'Join title is test_title1');
    $this->assert($result->get_row(0, 'test_table_id') == 1, 'Join test_table_id is 1');
    $this->assert($result->get_row(0, 'name') == 'blahblahblah', 'Join name is blahblahblah');

    $this->assert($result->get_row(1, 'id') == 1, 'Join row id is 1');
    $this->assert($result->get_row(1, 'count') == 100, 'Join count is 100');
    $this->assert($result->get_row(1, 'title') == 'test_title1', 'Join title is test_title1');
    $this->assert($result->get_row(1, 'test_table_id') == 1, 'Join test_table_id is 1');
    $this->assert($result->get_row(1, 'name') == 'blahblahblah', 'Join name is blahblahblah');

    $this->assert($result->get_row(2, 'id') == 1, 'Join row id is 1');
    $this->assert($result->get_row(2, 'count') == 100, 'Join count is 100');
    $this->assert($result->get_row(2, 'title') == 'test_title1', 'Join title is test_title1');
    $this->assert($result->get_row(2, 'test_table_id') == 1, 'Join test_table_id is 1');
    $this->assert($result->get_row(2, 'name') == 'foo', 'Join name is foo');
  }

  public function test_cross_join() {
    $this->insert_test_join_data();

    $select_fields = array('test_table.id', 
                           'test_table.count', 
                           'test_table.title', 
                           'test_join_table.test_table_id', 
                           'test_join_table.name',
                           'test_join_table.id AS test_join_table_id');
    $this->session->get_connecter()->select($select_fields);
    $result = $this->session->get_connecter()->join(self::JOIN_TABLE, '', 'inner')->get(self::TABLE);


    $this->assert($result->get_rows_count() == 12, 'rows count is 12');
    $this->assert($result->get_row(0, 'id') == 1, 'Join row id is 1');
    $this->assert($result->get_row(0, 'test_join_table_id') == 1, 'Join row id is 1');

    $this->assert($result->get_row(1, 'id') == 2, 'Join row id is 2');
    $this->assert($result->get_row(1, 'test_join_table_id') == 1, 'Join row id is 1');

    $this->assert($result->get_row(2, 'id') == 3, 'Join row id is 3');
    $this->assert($result->get_row(2, 'test_join_table_id') == 1, 'Join row id is 1');

    $this->assert($result->get_row(3, 'id') == 1, 'Join row id is 1');
    $this->assert($result->get_row(3, 'test_join_table_id') == 2, 'Join row id is 2');

    $this->assert($result->get_row(4, 'id') == 2, 'Join row id is 2');
    $this->assert($result->get_row(4, 'test_join_table_id') == 2, 'Join row id is 2');

    $this->assert($result->get_row(5, 'id') == 3, 'Join row id is 3');
    $this->assert($result->get_row(5, 'test_join_table_id') == 2, 'Join row id is 2');

    $this->assert($result->get_row(6, 'id') == 1, 'Join row id is 1');
    $this->assert($result->get_row(6, 'test_join_table_id') == 3, 'Join row id is 3');

    $this->assert($result->get_row(7, 'id') == 2, 'Join row id is 2');
    $this->assert($result->get_row(7, 'test_join_table_id') == 3, 'Join row id is 3');

    $this->assert($result->get_row(8, 'id') == 3, 'Join row id is 3');
    $this->assert($result->get_row(8, 'test_join_table_id') == 3, 'Join row id is 3');

    $this->assert($result->get_row(9, 'id') == 1, 'Join row id is 1');
    $this->assert($result->get_row(9, 'test_join_table_id') == 4, 'Join row id is 4');

    $this->assert($result->get_row(10, 'id') == 2, 'Join row id is 2');
    $this->assert($result->get_row(10, 'test_join_table_id') == 4, 'Join row id is 4');

    $this->assert($result->get_row(11, 'id') == 3, 'Join row id is 3');
    $this->assert($result->get_row(11, 'test_join_table_id') == 4, 'Join row id is 4');
  }

  public function test_left_join() {
    $this->insert_test_join_data();

    $join_compare = "test_join_table.test_table_id = test_table.id";
    $select_fields = array('test_table.id', 
                           'test_table.count', 
                           'test_table.title', 
                           'test_join_table.test_table_id', 
                           'test_join_table.name');
    $this->session->get_connecter()->select($select_fields);
    $result = $this->session->get_connecter()->join(self::JOIN_TABLE, $join_compare, 'left')->get(self::TABLE);
    $this->assert_left_outer_join($result);
  }

  public function test_left_outer_join() {
    $this->insert_test_join_data();
    $join_compare = "test_join_table.test_table_id = test_table.id";
    $select_fields = array('test_table.id', 
                           'test_table.count', 
                           'test_table.title', 
                           'test_join_table.test_table_id', 
                           'test_join_table.name');
    $this->session->get_connecter()->select($select_fields);
    $result = $this->session->get_connecter()->join(self::JOIN_TABLE, $join_compare, 'left_outer')->get(self::TABLE);
    $this->assert_left_outer_join($result);
  }

  private function assert_left_outer_join($result) {
    // 왼쪽 테이블 기준으로 검색시 결과 행의 갯수는 5개이다. 왼쪽 1행의 조건과 겹치는 오른쪽 행의 갯수 3개이므로
    // 3개의 행이 검색. 왼쪽 2행, 3행과 오른쪽 행의 조건의 겹치는 행이 없으므로 각각 1개
    $this->assert($result->get_rows_count() == 5, "Join row count is 5");

    $this->assert($result->get_row(0, 'id') == 1, 'id is 1');
    $this->assert($result->get_row(0, 'test_table_id') == 1, 'test_table_id is 1');

    $this->assert($result->get_row(1, 'id') == 1, 'id is 1');
    $this->assert($result->get_row(1, 'test_table_id') == 1, 'test_table_id is 1');

    $this->assert($result->get_row(2, 'id') == 1, 'id is 1');
    $this->assert($result->get_row(2, 'test_table_id') == 1, 'test_table_id is 1');

    $this->assert($result->get_row(3, 'id') == 2, 'id is 2');
    $this->assert($result->get_row(3, 'test_table_id') == NULL, 'test_table_id is NULL');

    $this->assert($result->get_row(4, 'id') == 3, 'id is 3');
    $this->assert($result->get_row(3, 'test_table_id') == NULL, 'test_table_id is NULL');
  }

  public function test_right_join() {
    $this->insert_test_join_data();

    $join_compare = "test_join_table.test_table_id = test_table.id";
    $select_fields = array('test_join_table.id', 
                           'test_join_table.name', 
                           'test_join_table.test_table_id', 
                           'test_table.title');
    $this->session->get_connecter()->select($select_fields);
    $result = $this->session->get_connecter()->join(self::JOIN_TABLE, $join_compare, 'right')->get(self::TABLE);
    $this->assert_right_outer_join($result);
  }

  public function test_right_outer_join() {
    $this->insert_test_join_data();

    $join_compare = "test_join_table.test_table_id = test_table.id";
    $select_fields = array('test_join_table.id', 
                           'test_join_table.name', 
                           'test_join_table.test_table_id', 
                           'test_table.title');
    $this->session->get_connecter()->select($select_fields);
    $result = $this->session->get_connecter()->join(self::JOIN_TABLE, $join_compare, 'right_outer')->get(self::TABLE);
    $this->assert_right_outer_join($result);
  }

  private function assert_right_outer_join($result) {
    $this->assert($result->get_rows_count() == 4, 'Rows count is 4');

    $this->assert($result->get_row(0, 'id') == 1, 'id is 1');
    $this->assert($result->get_row(0, 'test_table_id') == 1, 'test_table_id is 1');
    $this->assert($result->get_row(0, 'title') == 'test_title1', 'title is test_tiel1');

    $this->assert($result->get_row(1, 'id') == 2, 'id is 2');
    $this->assert($result->get_row(1, 'test_table_id') == 1, 'test_table_id is 1');
    $this->assert($result->get_row(1, 'title') == 'test_title1', 'title is test_tiel1');

    $this->assert($result->get_row(2, 'id') == 3, 'id is 3');
    $this->assert($result->get_row(2, 'test_table_id') == 1, 'test_table_id is 1');
    $this->assert($result->get_row(2, 'title') == 'test_title1', 'title is test_tiel1');

    $this->assert($result->get_row(3, 'id') == 4, 'id is 4');
    $this->assert($result->get_row(3, 'test_table_id') == 0, 'test_table_id is 0');
    $this->assert($result->get_row(3, 'title') == NULL, 'title is NULL');
  }

  private function insert_test_join_data() {
    $data = array(
      array(
        'title'=>'test_title1',
        'count'=>100
      ),
      array(
        'title'=>'test_title2',
        'count'=>500
      ),
      array(
        'title'=>'test_title3',
        'count'=>900
      )
    );
    $this->session->get_connecter()->insert_batch(self::TABLE, $data);

    $result = $this->session->get_connecter()->select(array('id'))->get(self::TABLE);
    $data = array(
      'test_table_id'=>$result->get_row(0, 'id'),
      'name'=>'blahblahblah'
    );
    $this->session->get_connecter()->insert(self::JOIN_TABLE, $data);
    $this->session->get_connecter()->insert(self::JOIN_TABLE, $data);
    $data = array(
      'test_table_id'=>$result->get_row(0, 'id'),
      'name'=>'foo'
    );
    $this->session->get_connecter()->insert(self::JOIN_TABLE, $data);
    $data = array(
      'name'=>'blahblahblah'
    );
    $this->session->get_connecter()->insert(self::JOIN_TABLE, $data);
  }

  public function set_up() {
    $this->actor = Kernel::get_instance()->new_object('af\kernel\actor\Actor', '/sys/db/mysql');
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
    $this->session->get_schema()->create_table(self::JOIN_TABLE, $this->get_test_join_fields());
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

  private function get_test_join_fields() {
    return array(
      'id'=>array(
        'type'=>'INT(11)',
        'unsigned'=>TRUE,
        'auto_increment'=>TRUE,
        'null'=>FALSE,
        'primary_key'=>TRUE
      ),
      'name'=>array(
        'type'=>'VARCHAR(200)',
        'null'=>FALSE,
        'default'=>''
      ),
      'test_table_id'=>array(
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
    $suite = new Test_Suite('MySQL_Active_Record_Join_Test');
    $suite->add(new MySQL_Active_Record_Join_Test('test_join'));
    $suite->add(new MySQL_Active_Record_Join_Test('test_inner_join'));
    $suite->add(new MySQL_Active_Record_Join_Test('test_cross_join'));
    $suite->add(new MySQL_Active_Record_Join_Test('test_left_join'));
    $suite->add(new MySQL_Active_Record_Join_Test('test_left_outer_join'));
    $suite->add(new MySQL_Active_Record_Join_Test('test_right_join'));
    $suite->add(new MySQL_Active_Record_Join_Test('test_right_outer_join'));
    return $suite;
  }

  private $actor;
  private $session;
}

