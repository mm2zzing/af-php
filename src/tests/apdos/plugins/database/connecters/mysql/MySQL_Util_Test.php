<?php
namespace tests\apdos\plugins\database\connecters\mysql;

use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\MySQL_Connecter;
use apdos\plugins\database\connecters\mysql\MySQL_Util;
use apdos\plugins\database\connecters\mysql\MySQL_Schema;
use apdos\kernel\actor\Actor;
use apdos\tools\ash\Tool_Config;

class MySQL_Util_Test extends Test_Case {
  private $connecter;
  private $util;

  public function test_database_exists() {
    $this->schema->create_database($this->get_db_name());
    $this->assert($this->util->has_database($this->get_db_name()), "Database test_db is exist");
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object(Actor::get_class_name(), '/sys/db/mysql');
    $this->connecter = $actor->add_component(MySQL_Connecter::get_class_name()); 
    $this->schema = $actor->add_component(MySQL_Schema::get_class_name());
    $this->util = $actor->add_component(MySQL_Util::get_class_name());

    $host = Tool_Config::get_instance()->get('test_server.mysql-test-db.host');
    $user = Tool_Config::get_instance()->get('test_server.mysql-test-db.user');
    $password = Tool_Config::get_instance()->get('test_server.mysql-test-db.password');
    $port = Tool_Config::get_instance()->get('test_server.mysql-test-db.port');
    $persistent = Tool_Config::get_instance()->get('test_server.mysql-test-db.persistent');
    $this->connecter->connect($host, $user, $password, $port, $persistent);
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
    $suite = new Test_Suite('MySQL_Util_Test');
    $suite->add(new MySQL_Util_Test('test_database_exists'));
    return $suite;
  }
}

