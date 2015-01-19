<?php
namespace apdos\tests;

use apdos\tools\ash\Tool;
use apdos\plugins\test\Test_Result;
use apdos\kernel\actor\Component;
use apdos\plugins\test\Test_Case;
use apdos\tests\plugins\test\test_case_test;
use apdos\tests\kernel\event\Event_Test;
use apdos\tests\kernel\core\Kernel_Test;
use apdos\tests\kernel\actor\Actor_Test;
use apdos\tests\kernel\core\object_converter_test;
use apdos\tests\kernel\actor\actor_accepter_test;
use apdos\tests\plugins\database\connecters\mongodb\Mongodb_Test;
use apdos\tests\plugins\prereg\Prereg_Manager_Test;
use apdos\tests\plugins\prereg\Prereg_Test;
use apdos\tests\plugins\auth\Auth_Test;
use apdos\tools\ash\console\Command_Line_Input;
use apdos\tools\ash\console\error\Command_Line_Input_Error;
use apdos\tests\plugins\database\connecters\mysql\Mysql_Connecter_Test;
use apdos\tests\plugins\database\connecters\mysql\Mysql_Schema_Test;
use apdos\tests\plugins\cache\Cache_Test;
use apdos\tests\plugins\input\Input_Test;
use apdos\kernel\core\Kernel;

/**
 * @class Run_Test
 *
 * @brief apdos 모듈 테스트 실행 프로그램
 * @author Lee Hyeon-gi
 */
class Run_Tests extends Tool {
  const NAME = "run_tests";
  const DESCRIPTION = "APD/OS-PHP unittest runner";
  const VERSION = '0.0.1';

  public function __construct() {
  }

  public function main($argc, $argv) {
    $cli = Component::create('apdos\tools\ash\console\Command_Line_Input', '/bin/cmd/run_tests');
    $cli->init(array('name'=>self::NAME,
                     'description' => self::DESCRIPTION,
                     'version' => self::VERSION));
    try {
      $cli->parse($argc, $argv);
      $this->run_test_cases();
    }
    catch (Command_Line_Input_Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
    Kernel::get_instance()->delete_object('/bin/cmd/run_tests');
  }

  private function run_test_cases() {
    $this->run_test_case_test();
    $this->run_event_test();
    $this->run_kernel_test();
    $this->run_actor_test();
    $this->run_actor_accepter_test();
    $this->run_object_converter_test();
    $this->run_mongodb_test();
    $this->run_mysql_connecter_test();
    $this->run_mysql_schema_test();
    $this->run_mysql_table_test();
    $this->run_auth_test();
    $this->run_prereg_test();
    $this->run_prereg_manager_test();
    $this->run_cache_test();
    $this->run_input_test();
  }

  private function run_test_case_test() {
    $test_result = new Test_Result('test_case_test');

    $test = new Test_Case_Test('test_run');
    $test->run($test_result);
    $test = new Test_Case_Test('test_summary');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_event_test() {
    $test_result = new Test_Result('event_test');

    $test = new Event_Test('test_serialize');
    $test->run($test_result);
    $test = new Event_Test('test_deserialize');
    $test->run($test_result);
    $test = new Event_Test('test_add_event_listener');
    $test->run($test_result);
    $test = new Event_Test('test_remove_event_listener');
    $test->run($test_result);
    $test = new Event_Test('test_dispatch_event');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_kernel_test() {
    $test_result = new Test_Result('kernel_test');

    $test = new Kernel_Test('test_create');
    $test->run($test_result);
    echo $test_result->summary() . PHP_EOL;
  }

  private function run_actor_test() {
    $test_result = new Test_Result('actor_test');

    $test = new Actor_Test('test_create');
    $test->run($test_result);
    $test = new Actor_Test('test_add_child');
    $test->run($test_result);

    $test = new Actor_Test('test_add_component');
    $test->run($test_result);
    $test = new Actor_Test('test_remove_component');
    $test->run($test_result);
    echo $test_result->summary() . PHP_EOL;
  }

  private function run_actor_accepter_test() {
    $test_result = new Test_Result('actor_accepeter_test');

    $test = new Actor_Accepter_Test('test_json_string_event');
    $test->run($test_result);
    $test = new Actor_Accepter_Test('test_wrong_property_event');
    $test->run($test_result);
    $test = new Actor_Accepter_Test('test_wrong_json_string_event');
    $test->run($test_result);
    $test = new Actor_Accepter_Test('test_actor_path');
    $test->run($test_result);
    echo $test_result->summary() . PHP_EOL;
  }

  private function run_object_converter_test() {

    $test_result = new Test_Result('object_converter_test');

    $test = new Object_Converter_Test('test_object_to_array');
    $test->run($test_result);
    $test = new Object_Converter_Test('test_array_to_object');
    $test->run($test_result);
    echo $test_result->summary() . PHP_EOL;
  }

  private function run_auth_test() {

    $test_result = new Test_Result('auth_test');

    $test = new Auth_Test('test_register_guest');
    $test->run($test_result);
    $test = new Auth_Test('test_register');
    $test->run($test_result);
    $test = new Auth_Test('test_register_device');
    $test->run($test_result);
    $test = new Auth_Test('test_get_user');
    $test->run($test_result);
    $test = new Auth_Test('test_login');
    $test->run($test_result);
    $test = new Auth_Test('test_unregister');
    $test->run($test_result);
    $test = new Auth_Test('test_unregister_login');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_prereg_test() {
    $test_result = new Test_Result('prereg_test');

    $test = new Prereg_Test('test_register');
    $test->run($test_result);
    $test = new Prereg_Test('test_register_with_values');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_prereg_manager_test() {
    $test_result = new Test_Result('prereg_manager_test');

    $test = new Prereg_Manager_Test('test_get_prereg_users');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }


  /**
   * @TODO 외부 플러그인으로 제외시킨다.
   */
  private function run_mongodb_test() {
    $test_result = new Test_Result('mongodb_test');

    $test = new Mongodb_Test('test_insert');
    $test->run($test_result);
    $test = new Mongodb_Test('test_find_one');
    $test->run($test_result);
    $test = new Mongodb_Test('test_find');
    $test->run($test_result);
    $test = new Mongodb_Test('test_limit');
    $test->run($test_result);
    $test = new Mongodb_Test('test_skip');
    $test->run($test_result);
    $test = new Mongodb_Test('test_update');
    $test->run($test_result);
    $test = new Mongodb_Test('test_set');
    $test->run($test_result);
    $test = new Mongodb_Test('test_set_all');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_mysql_connecter_test() {
    $test_result = new Test_Result('mysql_connecter_test');

    $test = new Mysql_Connecter_Test('test_create_database');
    $test->run($test_result);
    $test = new Mysql_Connecter_Test('test_drop_database');
    $test->run($test_result);
    $test = new Mysql_Connecter_Test('test_insert');
    $test->run($test_result);
    $test = new Mysql_Connecter_Test('test_select');
    $test->run($test_result);
    $test = new Mysql_Connecter_Test('test_delete');
    $test->run($test_result);


    echo $test_result->summary() . PHP_EOL;
  }

  private function run_mysql_schema_test() {
    $test_result = new Test_Result('mysql_schema_test');

    $test = new Mysql_Schema_Test('test_create_database');
    $test->run($test_result);
    $test = new Mysql_Schema_Test('test_drop_database');
    $test->run($test_result);
    $test = new Mysql_Schema_Test('test_create_table');
    $test->run($test_result);
    $test = new Mysql_Schema_Test('test_drop_table');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_cache_test() {
    $test_result = new Test_Result('cache_test');

    $test = new Cache_Test('test_numeric');
    $test->run($test_result);
    $test = new Cache_Test('test_array');
    $test->run($test_result);
    $test = new Cache_Test('test_expire');
    $test->run($test_result);
    $test = new Cache_Test('test_clear');
    $test->run($test_result);
    $test = new Cache_Test('test_clear_all');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_input_test() {
    $test_result = new Test_Result('input_test');

    $test = new Input_Test('test_get');
    $test->run($test_result);
    $test = new Input_Test('test_has');
    $test->run($test_result);
    $test = new Input_Test('test_get_ip');
    $test->run($test_result);
    $test = new Input_Test('test_get_user_agent');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_mysql_table_test() {
  }
}
