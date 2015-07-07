<?php
namespace tests\apdos\plugins\database\connecters\mysql;

use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\MySQL_Session;
use apdos\tools\ash\Tool_Config;

class MySQL_Active_Record_Test extends Test_Case {
  public function test_insert() {
    $this->session->get_connecter()->select_database($this->get_db_name());
  }

  public function test_get() {
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
    $suite->add(new MySQL_Active_Record_Test('test_get'));
    $suite->add(new MySQL_Active_Record_Test('test_delete'));
    return $suite;
  }

  private $actor;
  private $session;
}

