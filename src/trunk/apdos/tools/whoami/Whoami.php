<?php
namespace apdos\tools\whoami;

use apdos\tools\ash\Tool;
use apdos\tools\ash\console\Command_Line_Input;
use apdos\tools\ash\console\error\Command_Line_Input_Error;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Kernel;

/**
 * @class Whoami
 *
 * @brief 현재 로그인되어 있는 사용자를 출력 
 * @author Lee Hyeon-gi
 */
class Whoami extends Tool {
  const NAME = "whoami";
  const DESCRIPTION = "Display login user";
  const VERSION = '0.0.1';

  public function __construct() {
  }

  public function main($argc, $argv) {
    $this->cli = Component::create('apdos\tools\ash\console\Command_Line_Input', '/bin/cmd/service');
    $this->cli->init(array('name'=>self::NAME,
                           'description' => self::DESCRIPTION,
                           'version' => self::VERSION,
                           'add_help_option'=>FALSE));
    $this->cli->add_option('help', array(
        'short_name'=>'-h',
        'long_name'=>'--help',
        'action'=>'StoreTrue',
        'description'=>'help'
    ));
    try {
      $this->cli->parse($argc, $argv);
      if ($this->cli->has_option('help')) {
        $this->cli->display_usage();
      }

      echo $this->get_parent()->get_owner() . PHP_EOL;
    }
    catch (Command_Line_Input_Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
    $this->cli->get_parent()->release();
  }
}
