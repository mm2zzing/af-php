<?php
namespace tests\apdos\plugins\database\connecters\mysql;

use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connecters\mysql\MySQL_Connecter;
use apdos\plugins\database\connecters\mysql\MySQL_Util;
use apdos\plugins\database\connecters\mysql\MySQL_Schema;
use apdos\plugins\database\connecters\mysql\MySQL_Session;
use apdos\kernel\actor\Actor;
use apdos\tools\ash\Tool_Config;

class MySQL_Session_Test extends Test_Case {

  public function test_create() {
    $this->actor = Kernel::get_instance()->new_object(Actor::get_class_name(), '/sys/rdb_session');
    $this->session = $this->actor->add_component(MySQL_Session::get_class_name());

    $this->actor->update();

    $this->assert($this->session->get_connecter()->get_class_name() == MySQL_Connecter::get_class_name(),
                  'connecter name is MySQL_Connecter');
    $this->assert($this->session->get_schema()->get_class_name() == MySQL_Schema::get_class_name(),
                  'connecter name is MySQL_Schema');
    $this->assert($this->session->get_util()->get_class_name() == MySQL_Util::get_class_name(),
                  'connecter name is MySQL_Util');

    Kernel::get_instance()->delete_object($this->actor->get_path());
  }

  public function set_up() {
  }

  public function tear_down() {
  }

  public static function create_suite() {
    $suite = new Test_Suite('MySQL_Session_Test');
    $suite->add(new MySQL_Session_Test('test_create'));
    return $suite;
  }

  private $actor;
}

