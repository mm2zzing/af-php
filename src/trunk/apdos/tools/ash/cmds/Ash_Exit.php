<?php
namespace apdos\tools\ash\cmds;

use apdos\tools\ash\Tool;
use apdos\tools\ash\console\Command_Line_Input;
use apdos\tools\ash\console\error\Command_Line_Input_Error;

/**
 * @class Exit
 *
 * @brief 쉘에서 빠져나오는 프로그램 
 * @author Lee Hyeon-gi
 */
class Ash_Exit extends Tool {
  const NAME = "ash exit";
  const DESCRIPTION = "APD/OS-PHP shell exit";
  const VERSION = '0.0.1';

  public function __construct() {
  }

  public function main($argc, $argv) {
    $cli = new Command_Line_Input( 
      array('name'=>self::NAME,
            'description' => self::DESCRIPTION,
            'version' => self::VERSION));
    try {
      $cli->parse($argc, $argv);
      exit;
    }
    catch (Command_Line_Input_Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
  }
}
