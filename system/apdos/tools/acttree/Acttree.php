<?php
namespace apdos\tools\acttree;

use apdos\tools\ash\Tool;
use apdos\tools\ash\console\Command_Line;
use apdos\tools\ash\console\error\Command_Line_Error;
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
    $cli = new Command_Line( 
      array('name'=>self::NAME,
            'description' => self::DESCRIPTION,
            'version' => self::VERSION,
            'add_help_option'=>FALSE));
    $cli->add_option('help', array(
        'short_name'=>'-h',
        'long_name'=>'--help',
        'action'=>'StoreTrue',
        'description'=>'help'
    ));
    try {
      $cli->parse($argc, $argv);
      if ($cli->has_option('help')) {
        $cli->display_usage();
      }
    }
    catch (Command_Line_Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
  }
}
