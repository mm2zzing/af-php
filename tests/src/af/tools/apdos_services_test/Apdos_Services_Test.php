<?php
namespace af\tools\apdos_services_test;

use af\kernel\core\Kernel;
use af\tools\ash\Tool;
use af\plugins\test\Test_Result;
use af\kernel\actor\Component;
use af\plugins\test\Test_Case;
use af\plugins\test\Test_Runner;
use af\tools\ash\console\Command_Line;
use af\tools\ash\console\error\Command_Line_Error;

/**
 * @class Apdos_Services_Test
 *
 * @brief apdos 모듈 테스트 실행 프로그램
 * @author Lee Hyeon-gi
 */
class Apdos_Services_Test extends Tool {
  const NAME = "apdos-services-test";
  const DESCRIPTION = "APD/OS-PHP unittest runner";
  const VERSION = '0.0.1';

  public function __construct() {
  }

  public function main($argc, $argv) {
    $cli = Component::create('af\tools\ash\console\Command_Line', '/bin/cmd/run_tests');
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
    $runner = new Test_Runner();
    $runner->run();
    echo $runner->summary() . PHP_EOL;
  }
}
