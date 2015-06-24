<?php
namespace tests\tools\apdos_test;

use apdos\kernel\core\Kernel;
use apdos\tools\ash\Tool;
use apdos\plugins\test\Test_Result;
use apdos\kernel\actor\Component;
use apdos\plugins\test\Test_Case;
use tests\apdos\plugins\test\Test_Case_Test;
use tests\apdos\kernel\event\Event_Test;
use tests\apdos\kernel\event\Redf_Serializer_Test;
use tests\apdos\kernel\core\Kernel_Test;
use tests\apdos\kernel\actor\Actor_Test;
use tests\apdos\kernel\user\User_Server_Test;
use tests\apdos\kernel\core\Object_Converter_Test;
use tests\apdos\kernel\actor\actor_accepter_test;
use tests\apdos\plugins\prereg\Prereg_Manager_Test;
use tests\apdos\plugins\prereg\Prereg_Test;
use tests\apdos\plugins\auth\Auth_Test;
use apdos\tools\ash\console\Command_Line;
use apdos\tools\ash\console\error\Command_Line_Error;
use tests\apdos\plugins\cache\Cache_Test;
use tests\apdos\plugins\input\Input_Test;

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
    $cli = Component::create('apdos\tools\ash\console\Command_Line', '/bin/cmd/run_tests');
    $cli->init(array('name'=>self::NAME,
                     'description' => self::DESCRIPTION,
                     'version' => self::VERSION));
    try {
      $cli->parse($argc, $argv);
      $this->run_test_cases();
    }
    catch (Command_Line_Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
    Kernel::get_instance()->delete_object('/bin/cmd/run_tests');
  }

  private function run_test_cases() {
    $this->run_test_case_test();
    $this->run_event_test();
    $this->run_redf_serializer_test();
    $this->run_kernel_test();
    $this->run_actor_test();
    $this->run_user_server_test();
    $this->run_actor_accepter_test();
    $this->run_object_converter_test();
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
    $test = new Test_Case_Test('test_mock_object');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_event_test() {
    $test_result = new Test_Result('event_test');

    $test = new Event_Test('test_add_event_listener');
    $test->run($test_result);
    $test = new Event_Test('test_remove_event_listener');
    $test->run($test_result);
    $test = new Event_Test('test_dispatch_event');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_redf_serializer_test() {
    $test_result = new Test_Result('redf_serializer_test');

    $test = new Redf_Serializer_Test('test_serialize');
    $test->run($test_result);
    $test = new Redf_Serializer_Test('test_deserialize');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_kernel_test() {
    $test_result = new Test_Result('kernel_test');

    $test = new Kernel_Test('test_create');
    $test->run($test_result);
    
    $test = new Kernel_Test('test_lookup');
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
    $test = new Actor_Test('test_add_components');
    $test->run($test_result);
    $test = new Actor_Test('test_remove_component');
    $test->run($test_result);
    echo $test_result->summary() . PHP_EOL;
  }

  private function run_user_server_test() {
    $test_result = new Test_Result('user_server_test');

    $test = new User_Server_Test('test_create');
    $test->run($test_result);
    $test = new User_Server_Test('test_change_user');
    $test->run($test_result);
    $test = new User_Server_Test('test_change_app_user');
    $test->run($test_result);
    $test = new User_Server_Test('test_user_login');
    $test->run($test_result);
    $test = new User_Server_Test('test_app_user_login');
    $test->run($test_result);
    $test = new User_Server_Test('test_logout');
    $test->run($test_result);
    $test = new User_Server_Test('test_register');
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
