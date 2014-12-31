<?php
namespace apdos\tools\acttree;

use apdos\tools\ash\app\Tool;
use apdos\tools\ash\app\console\Command_Line_Input;
use apdos\tools\ash\app\console\error\Command_Line_Input_Error;
use apdos\tools\acttree\actions\Acttree_Help;

/**
 * @class Acttree
 *
 * @brief 생성되어 있는 Actor리스트를 트리구조로 출력 
 * @author Lee Hyeon-gi
 */
class Acttree extends Tool {
  const NAME = "acttre";
  const DESCRIPTION = "Display a tree of actors";
  const VERSION = '0.0.1';

  public function __construct() {
  }

  public function main($argc, $argv) {
    $cli = new Command_Line_Input( 
      array('name'=>self::NAME,
            'description' => self::DESCRIPTION,
            'version' => self::VERSION));
    $cli->addOption('help', array(
      'short_name'=>'-h',
      'long_name'=>'--help',
      'action'=>'Acttree_Help',
      'description'=>'help'
    ));
    try {
      $cli->parse($argc, $argv);
      //echo 'hi';
    }
    catch (Command_Line_Input_Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
  }
}
