<?php
namespace apdos\tests\plugins\database\connecters\mysql;

use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\Mysql_Connecter;

class Mysql_Test extends Test_Case {
  public function test_insert() {
  }

  public function test_get() {
  }

  public function test_delete() {
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/db/mysql');
    $this->connecter = $actor->add_component('apdos\plugins\database\connecters\mysql\Mysql_Connecter');
    $this->connecter->connect('localhost', 'root', 'hserver1@sql');
    $this->connecter->drop_database('ft');
    $this->connecter->create_database('ft');
    $this->connecter->select_database('ft');
  }

  public function tear_down() {
    $this->connecter->close();
    Kernel::get_instance()->delete_object('/sys/db/mysql');
  }
}

