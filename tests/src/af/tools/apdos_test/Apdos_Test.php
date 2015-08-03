<?php
namespace af\tools\apdos_test;

use af\kernel\core\Kernel;
use af\tools\ash\Tool;
use af\plugins\test\Test_Result;
use af\kernel\actor\Component;
use af\plugins\test\Test_Case;
use af\plugins\test\Test_Runner;
use af\tests\plugins\test\Test_Case_Test;
use af\tests\plugins\test\Test_Runner_Test;
use af\tests\kernel\actor\property\Property_Test;
use af\tests\kernel\event\Event_Test;
use af\tests\kernel\event\RDP_Serializer_Test;
use af\tests\kernel\core\Kernel_Test;
use af\tests\kernel\actor\Actor_Test;
use af\tests\kernel\user\User_Server_Test;
use af\tests\kernel\core\Object_Test;
use af\tests\kernel\core\Object_Converter_Test;
use af\tests\kernel\actor\Actor_Accepter_Test;
use af\tests\kernel\objectid\Object_ID_Test;
use af\tests\plugins\prereg\Prereg_Manager_Test;
use af\tests\plugins\database\connecters\mongodb\Mongodb_Test;
use af\tests\plugins\prereg\Prereg_Test;
use af\tests\plugins\auth\Auth_Test;
use af\tools\ash\console\Command_Line;
use af\tools\ash\console\error\Command_Line_Error;
use af\tests\plugins\cache\Cache_Test;
use af\tests\plugins\input\Input_Test;
use af\tests\plugins\database\connecters\mysql\MySQL_Connecter_Test;
use af\tests\plugins\database\connecters\mysql\MySQL_Schema_Test;
use af\tests\plugins\database\connecters\mysql\MySQL_Util_Test;
use af\tests\plugins\database\connecters\mysql\MySQL_Session_Test;
use af\tests\plugins\database\connecters\mysql\MySQL_Active_Record_Test;
use af\tests\plugins\database\connecters\mysql\MySQL_Active_Record_Where_Test;
use af\tests\plugins\database\connecters\mysql\MySQL_Active_Record_Update_Test;
use af\tests\plugins\database\connecters\mysql\MySQL_Active_Record_Join_Test;
use af\tests\plugins\database\connecters\mysql\MySQL_Active_Record_Escape_Test;
use af\tests\plugins\sharding\Sharding_Config_Test;
use af\tests\plugins\sharding\Sharding_Schema_Test;
use af\tests\plugins\sharding\Sharding_Router_Test;
use af\tests\plugins\sharding\Sharding_Object_ID_Test;

/**
 * @class Apdos_Test
 *
 * @brief apdos 모듈 테스트 실행 프로그램
 * @author Lee Hyeon-gi
 */
class Apdos_Test extends Tool {
  const NAME = "apdos-test";
  const DESCRIPTION = "APD/OS-PHP unittest runner";
  const VERSION = '0.0.1';

  public function __construct() {
  }

  public function main($argc, $argv) {
    $this->select_config(realpath(__DIR__));
    $this->cli = $this->create_line_input();
    try {
      $this->cli->parse($argc, $argv);
      $this->run_test_cases();
    }
    catch (Command_Line_Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
    Kernel::get_instance()->delete_object('/bin/cmd/run_tests');
  }

  private function create_line_input() {
    $cli = Component::create(Command_Line::get_class_name(), '/bin/cmd/run_tests');
    $cli->init(array('name'=>self::NAME,
                     'description' => self::DESCRIPTION,
                     'version' => self::VERSION));
    $cli->add_option('dmysql', array(
        'long_name'=>'--d-mysql',
        'description'=>'include mysql dependency tests. test require mysqli extension',
        'action'=>'StoreTrue',
    ));
    $cli->add_option('dmongodb', array(
        'long_name'=>'--d-mongodb',
        'description'=>'include mongodb dependency tests.',
        'action'=>'StoreTrue',
    ));
    $cli->add_option('shortsummary', array(
        'long_name'=>'--short-summary',
        'description'=>'',
        'action'=>'StoreTrue',
    ));

    return $cli;
  }

  private function run_test_cases() {
    $runner = new Test_Runner();
    $runner->add(Test_Case_Test::create_suite());
    $runner->add(Test_Runner_Test::create_suite());
    $runner->add(Object_Test::create_suite());
    $runner->add(Event_Test::create_suite());
    $runner->add(RDP_Serializer_Test::create_suite());
    $runner->add(Kernel_Test::create_suite());
    $runner->add(Actor_Test::create_suite());
    $runner->add(Property_Test::create_suite());
    $runner->add(User_Server_Test::create_suite());
    $runner->add(Actor_Accepter_Test::create_suite());
    $runner->add(Object_Converter_Test::create_suite());
    $runner->add(Object_ID_Test::create_suite());
    $runner->add(Auth_Test::create_suite());
    $runner->add(Prereg_Test::create_suite());
    $runner->add(Prereg_Manager_Test::create_suite());
    $runner->add(Cache_Test::create_suite());
    $runner->add(Input_Test::create_suite());

    if ($this->cli->has_option('dmysql')) {
      $runner->add(MySQL_Connecter_Test::create_suite());
      $runner->add(MySQL_Schema_Test::create_suite());
      $runner->add(MySQL_Util_Test::create_suite());
      $runner->add(MySQL_Session_Test::create_suite());
      $runner->add(MySQL_Active_Record_Test::create_suite());
      $runner->add(MySQL_Active_Record_Where_Test::create_suite());
      $runner->add(MySQL_Active_Record_Update_Test::create_suite());
      $runner->add(MySQL_Active_Record_Join_Test::create_suite());
      $runner->add(MySQL_Active_Record_Escape_Test::create_suite());
      $runner->add(Sharding_Object_ID_Test::create_suite());
      $runner->add(Sharding_Config_Test::create_suite());
      $runner->add(Sharding_Schema_Test::create_suite());
      $runner->add(Sharding_Router_Test::create_suite());
    }

    if ($this->cli->has_option('dmongodb')) {
      $runner->add(Mongodb_Test::create_suite());
    }
    $runner->run();
    if ($this->cli->has_option('shortsummary')) 
      echo $runner->short_summary() . PHP_EOL;
    else
      echo $runner->summary() . PHP_EOL;
  }
}
