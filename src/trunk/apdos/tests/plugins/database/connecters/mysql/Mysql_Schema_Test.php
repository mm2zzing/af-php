<?php
namespace apdos\tests\plugins\database\connecters\mysql;

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
    $this->schema = $actor->add_component('apdos\plugins\database\connecters\mysql\Mysql_Schema');
    $this->connecter->connect('p:localhost', 'root', 'hserver1@sql'); 
  }

  public function tear_down() {
    $this->schema->drop_database('test_db');
    $this->connecter->close();
    Kernel::get_instance()->delete_object('/sys/db/mysql');
  }
}

