<?php
namespace tests\tools\apdos_shard_test;

use apdos\kernel\core\Kernel;
use apdos\tools\ash\Tool;
use apdos\plugins\test\Test_Result;
use apdos\kernel\actor\Component;
use apdos\plugins\test\Test_Case;
use tests\apdos\plugins\test\Test_Case_Test;
use tests\apdos\kernel\event\Event_Test;
use tests\apdos\kernel\event\Rdp_Serializer_Test;
use tests\apdos\kernel\core\Kernel_Test;
use tests\apdos\kernel\actor\Actor_Test;
use tests\apdos\kernel\user\User_Server_Test;
use tests\apdos\kernel\core\Object_Converter_Test;
use tests\apdos\kernel\actor\actor_accepter_test;
use tests\apdos\plugins\database\connecters\mongodb\Mongodb_Test;
use tests\apdos\plugins\prereg\Prereg_Manager_Test;
use tests\apdos\plugins\prereg\Prereg_Test;
use tests\apdos\plugins\auth\Auth_Test;
use apdos\tools\ash\console\Command_Line;
use apdos\tools\ash\console\error\Command_Line_Error;
use tests\apdos\plugins\cache\Cache_Test;
use tests\apdos\plugins\input\Input_Test;
use tests\apdos\plugins\shard\Shard_Test;

/**
 * @class Apdos_Shard_Test
 *
 * @brief apdos 모듈 테스트 실행 프로그램
 * @author Lee Hyeon-gi
 */
class Apdos_Shard_Test extends Tool {
  const NAME = "apdos-shard-test";
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
    $this->run_shard_test();
  }

  private function run_shard_test() {
    $test_result = new Test_Result('shard_test');

    $test = new Shard_Test('test_set');
    $test->run($test_result);

    echo $test_result->summary() . PHP_EOL;
  }
}
