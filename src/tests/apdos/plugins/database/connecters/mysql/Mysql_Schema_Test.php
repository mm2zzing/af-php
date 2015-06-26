<?php
namespace tests\apdos\plugins\database\connecters\mysql;

use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\Mysql_Connecter;

class Mysql_Schema_Test extends Test_Case {
  private $connecter;
  private $schema;

  public function test_create_database() {
    $this->schema->create_database('test_db');
    $this->assert($this->connecter->has_database('test_db'), "Database test_db is exist");
  }

  public function test_drop_database() {
    $this->schema->create_database('test_db');
    $this->schema->drop_database('test_db');
    $this->assert(false == $this->connecter->has_database('test_db'), "Database test_db is not exist");
  }

  public function test_create_table() {
    $this->schema->create_database('test_db');
    $this->connecter->select_database('test_db');
    $this->assert(false == $this->connecter->has_table('test_table'), "Database test_db is not exist");

    $this->schema->create_table('test_table', $this->get_fields());
    $this->assert($this->connecter->has_table('test_table'), "Database test_db is exist");
  }

  public function test_drop_table() {
    $this->schema->create_database('test_db');
    $this->connecter->select_database('test_db');
    $this->schema->create_table('test_table', $this->get_fields());

    $this->schema->drop_table('test_table');
    $this->assert(!$this->connecter->has_table('test_table'), "Database test_db is exist");
  }

  private function get_fields() {
    return array(
      'title'=>array(
        'type'=>'varchar(100)',
        'null'=>false,
        'default'=>''
      )
    );
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/db/mysql');
    $this->connecter = $actor->add_component('apdos\plugins\database\connecters\mysql\Mysql_Connecter'); 
    $this->connecter->connect('p:localhost', 'root', ''); 

    $this->schema = $actor->add_component('apdos\plugins\database\connecters\mysql\Mysql_Schema');
    $this->schema->set_property('connecter', $this->connecter);
  }

  public function tear_down() {
    $this->schema->drop_database('test_db');
    $this->connecter->close();
    Kernel::get_instance()->delete_object('/sys/db/mysql');
  }

  public static function create_suite() {
    $suite = new Test_Suite('Mysql_Connecter_Test');
    $suite->add(new Mysql_Connecter_Test('test_create_database'));
    $suite->add(new Mysql_Connecter_Test('test_drop_database'));
    $suite->add(new Mysql_Connecter_Test('test_insert'));
    $suite->add(new Mysql_Connecter_Test('test_select'));
    $suite->add(new Mysql_Connecter_Test('test_delete'));
    return $suite;
  }
}

