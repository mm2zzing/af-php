<?php
/**
 * @class Run_Test
 *
 * @brief apdt 모듈 테스트 실행 프로그램
 * @author Lee Hyeon-gi
 */
class Run_Tests extends Entry {
  public function __construct($loader) {
    parent::__construct($loader);
    $loader->include_module('plugins/test/test_case');
    $loader->include_module('plugins/test/test_result');
  }

  public function run() {
    $this->run_test_case_test();
    $this->run_event_test();
    $this->run_kernel_test();
    $this->run_actor_test();
    $this->run_actor_accepter_test();
    $this->run_object_converter_test();
    $this->run_mongodb_test();
    $this->run_auth_test();
  }

  private function run_test_case_test() {
    $this->loader->include_module('tests/plugins/test/test_case_test');
    
    $test_result = new Test_Result('test_case_test');

    $test = new Test_Case_Test('test_run');
    $test->run($test_result);
    $test = new Test_Case_Test('test_summary');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }

  private function run_event_test() {
    $this->loader->include_module('tests/kernel/event/event_test');

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
    $this->loader->include_module('tests/kernel/core/kernel_test');

    $test_result = new Test_Result('kernel_test');

    $test = new Kernel_Test('test_create');
    $test->run($test_result);
    echo $test_result->summary() . PHP_EOL;
  }

  private function run_actor_test() {
    $this->loader->include_module('tests/kernel/actor/actor_test');

    $test_result = new Test_Result('actor_test');

    $test = new Actor_Test('test_create');
    $test->run($test_result);
    $test = new Actor_Test('test_add_component');
    $test->run($test_result);
    $test = new Actor_Test('test_remove_component');
    $test->run($test_result);
    echo $test_result->summary() . PHP_EOL;
  }

  private function run_actor_accepter_test() {
    $this->loader->include_module('tests/kernel/actor/actor_accepter_test');

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
    $this->loader->include_module('tests/kernel/core/object_converter_test');

    $test_result = new Test_Result('object_converter_test');

    $test = new Object_Converter_Test('test_object_to_array');
    $test->run($test_result);
    $test = new Object_Converter_Test('test_array_to_object');
    $test->run($test_result);
    echo $test_result->summary() . PHP_EOL;
  }

  private function run_auth_test() {
    $this->loader->include_module('tests/plugins/auth/auth_test');

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

  private function run_mongodb_test() {
    $this->loader->include_module('tests/plugins/db/mongodb/mongodb_test');

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
}
