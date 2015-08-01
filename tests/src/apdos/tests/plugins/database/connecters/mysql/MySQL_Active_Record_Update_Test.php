<?php
namespace apdos\tests\plugins\database\connecters\mysql;

use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\MySQL_Session;
use apdos\tools\ash\Tool_Config;
use apdos\plugins\database\base\rdb\errors\RDB_Error;

class MySQL_Active_Record_Update_Test extends Test_Case {
  const TABLE = 'test_table';

  public function test_update() {
    $this->insert_test_data();

    $result = $this->session->get_connecter()->where('id', 1)->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, 'Rows count is 1');
    $this->assert($result->get_row(0, 'id') == 1, "Id is 1");
    $this->assert($result->get_row(0, 'count') == 100, "Count is 100");

    $data = array('count'=>1000);
    $result = $this->session->get_connecter()->where('id', 1)->update(self::TABLE, $data);
    $this->assert($result->get_rows_count() == 0, 'Update exists success');

    $result = $this->session->get_connecter()->where('id', 1)->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, 'Rows count is 1');
    $this->assert($result->get_row(0, 'id') == 1, "Id is 1");
    $this->assert($result->get_row(0, 'count') == 1000, "Count update to 1000");
  }

  public function test_update_where() {
    $this->insert_test_data();

    $result = $this->session->get_connecter()->where('id', 1)->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, 'Rows count is 1');
    $this->assert($result->get_row(0, 'id') == 1, "Id is 1");
    $this->assert($result->get_row(0, 'count') == 100, "Count is 100");

    $data = array('count'=>1000);
    $result = $this->session->get_connecter()->update_where(self::TABLE, $data, array('id'=>1));
    $this->assert($result->get_rows_count() == 0, 'Update exists success');

    $result = $this->session->get_connecter()->where('id', 1)->get(self::TABLE);
    $this->assert($result->get_rows_count() == 1, 'Rows count is 1');
    $this->assert($result->get_row(0, 'id') == 1, "Id is 1");
    $this->assert($result->get_row(0, 'count') == 1000, "Count update to 1000");
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
    $suite = new Test_Suite('MySQL_Active_Record_Update_Test');
    $suite->add(new MySQL_Active_Record_Update_Test('test_update'));
    $suite->add(new MySQL_Active_Record_Update_Test('test_update_where'));
    return $suite;
  }

  private $actor;
  private $session;
}

