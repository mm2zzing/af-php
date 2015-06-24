<?php
namespace tests\tools\apdos_mysql_test;

use apdos\kernel\core\Kernel;
use apdos\tools\ash\Tool;
use apdos\plugins\test\Test_Result;
use apdos\kernel\actor\Component;
use apdos\plugins\test\Test_Case;
use apdos\tools\ash\console\Command_Line;
use apdos\tools\ash\console\error\Command_Line_Error;
use tests\apdos\plugins\database\connecters\mysql\Mysql_Connecter_Test;
use tests\apdos\plugins\database\connecters\mysql\Mysql_Schema_Test;

/**
 * @class Apdos_Mysql_Test
 *
 * @brief apdos 모듈 테스트 실행 프로그램
 * @author Lee Hyeon-gi
 */
class Apdos_Mysql_Test extends Tool {
  const NAME = "apdos-mysql-test";
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
    $this->run_mysql_connecter_test();
    $this->run_mysql_schema_test();
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
}
