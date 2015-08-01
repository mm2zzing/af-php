<?php
namespace apdos\tests\plugins\database\connecters\mysql;

use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\MySQL_Connecter;
use apdos\tools\ash\Tool_Config;

class MySQL_Schema_Test extends Test_Case {
  private $connecter;
  private $schema;

  public function test_create_database() {
    $this->schema->create_database($this->get_db_name());
    $this->assert($this->schema->has_database($this->get_db_name()), "Database test_db is exist");
  }

  public function test_drop_database() {
    $this->schema->create_database($this->get_db_name());
    $this->schema->drop_database($this->get_db_name());
    $this->assert(false == $this->schema->has_database($this->get_db_name()), "Database test_db is not exist");
  }

  public function test_create_table() {
    $this->schema->create_database($this->get_db_name());
    $this->connecter->select_database($this->get_db_name());
    $this->assert(false == $this->connecter->has_table('test_table'), "Database test_db is not exist");

    $this->schema->create_table('test_table', $this->get_fields());
    $this->assert($this->connecter->has_table('test_table'), "Database test_db is exist");
  }

  public function test_drop_table() {
    $this->schema->create_database($this->get_db_name());
    $this->connecter->select_database($this->get_db_name());
    $this->schema->create_table('test_table', $this->get_fields());

    $this->schema->drop_table('test_table');
    $this->assert(!$this->connecter->has_table('test_table'), "Database test_db is exist");
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
      )
    );
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/db/mysql');
    $this->connecter = $actor->add_component('apdos\plugins\database\connecters\mysql\MySQL_Connecter'); 
    $host = Tool_Config::get_instance()->get('test_server.mysql-test-db.host');
    $user = Tool_Config::get_instance()->get('test_server.mysql-test-db.user');
    $password = Tool_Config::get_instance()->get('test_server.mysql-test-db.password');
    $port = Tool_Config::get_instance()->get('test_server.mysql-test-db.port');
    $persistent = Tool_Config::get_instance()->get('test_server.mysql-test-db.persistent');
    $this->connecter->connect($host, $user, $password, $port, $persistent);

    $this->schema = $actor->add_component('apdos\plugins\database\connecters\mysql\MySQL_Schema');
  }

  public function tear_down() {
    $this->schema->drop_database($this->get_db_name());
    $this->connecter->close();
    Kernel::get_instance()->delete_object('/sys/db/mysql');
  }

  private function get_db_name() {
    return Tool_Config::get_instance()->get('test_server.mysql-test-db.db_name');
  }

  public static function create_suite() {
    $suite = new Test_Suite('MySQL_Schema_Test');
    $suite->add(new MySQL_Schema_Test('test_create_database'));
    $suite->add(new MySQL_Schema_Test('test_drop_database'));
    $suite->add(new MySQL_Schema_Test('test_create_table'));
    $suite->add(new MySQL_Schema_Test('test_drop_table'));
    return $suite;
  }
}

