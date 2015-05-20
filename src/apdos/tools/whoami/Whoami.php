<?php
namespace apdos\tools\whoami;

use apdos\tools\ash\Tool;
use apdos\tools\ash\console\Command_Line;
use apdos\tools\ash\console\error\Command_Line_Error;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Kernel;
use apdos\plugins\input\Input;

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
    $this->cli = Component::create('apdos\tools\ash\console\Command_Line', '/bin/cmd/service');
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

      $user = $this->get_parent()->get_owner();
      $remote_ip = Input::get_instance()->get_ip();
      $user_agent = Input::get_instance()->get_user_agent();

      echo "user:$user remote ip:$remote_ip user agent:$user_agent" . PHP_EOL;
    }
    catch (Command_Line_Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
    $this->cli->get_parent()->release();
  }
}
