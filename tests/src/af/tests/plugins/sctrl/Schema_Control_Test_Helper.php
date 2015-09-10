<?php
namespace af\tests\plugins\sctrl;

use af\kernel\core\Kernel;
use af\kernel\actor\Actor;
use af\tools\ash\Tool_Config;
use af\plugins\database\connecters\mysql\MySQL_Connecter;
use af\plugins\database\connecters\mysql\MySQL_Schema;

class Schema_Control_Test_Helper {
  public function create_db_connecter() {
    $db_config = $this->get_test_database_config();

    $actor = Kernel::get_instance()->new_object(Actor::get_class_name(), '/sys/sctrl_db_connecter');
    $db_connecter = $actor->add_component(MySQL_Connecter::get_class_name());
    $db_connecter->connect($db_config->host, $db_config->user, $db_config->password);
    $db_schema = $actor->add_component(MySQL_Schema::get_class_name());
    return $actor;
  }

  public function get_test_database_config() {
    return Tool_Config::get_instance()->get('test_server.mysql-test-db');
  }
}

