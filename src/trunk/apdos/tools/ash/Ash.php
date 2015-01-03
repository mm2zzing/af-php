<?php
namespace apdos\tools\ash;

use apdos\kernel\actor\Actor;
use apdos\kernel\core\Kernel;
use apdos\tools\ashconsole\Command_Line_Input;
use apdos\tools\ash\console\error\Command_Line_Input_Error;
use apdos\tools\ash\error\Ash_Error;
use apdos\kernel\log\Logger;
use apdos\kernel\actor\Component;

class Ash extends Tool {
  const LOGO = '
               (    (              )   (     
        (      )\ ) )\ )        ( /(   )\ )  
        )\    (()/((()/(        )\()) (()/(  
     ((((_)(   /(_))/(_))    __((_)\   /(_)) 
      )\ _ )\ (_)) (_))_    / /  ((_) (_))   
      (_)_\(_)| _ \ |   \  / /  / _ \ / __|  
       / _ \  |  _/ | |) |/_/  | (_) |\__ \  
      /_/ \_\ |_|   |___/       \___/ |___/';
  const NAME = 'ash';
  const DESCRIPTION = 'APD/OS-PHP shell';
  const VERSION = '0.0.1';
  const PROMPT = 'ash> ';

  private $cmds = array();

  public function __construct() {
  }

  /**
   * 쉘에서 처리할 명령어를 등록한다.
   *
   * @param cmd_name string 쉘에서 입력할 명령어
   * @param tool_class_name 명령어가 처리할 툴 컴포넌트 클래스 이름
   */
  public function register_cmd($cmd_name, $tool_class_name) {
    $this->cmds[$cmd_name] = $tool_class_name;
    $path = '/bin/' . $cmd_name;
    $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', $path);
    $actor->add_component($tool_class_name);
  }

  public function main($argc, $argv) {
    $this->display_logo();
    $cli = $this->create_line_input();
    try {
      $cli->parse($argc, $argv);
      if ($cli->has_option('run_cmd')) {
        $this->display_version();
        $tool_argv = explode(' ', $cli->get_option('run_cmd'));
        $tool_argc = count($tool_argv);
        $this->run_command($tool_argc, $tool_argv);
      }
      else {
        while ($line = readline(self::PROMPT)) {
          if ($line == 'exit')
            break;
          $tool_argv = explode(' ', $line);
          $tool_argc = count($tool_argv);
          $this->run_command($tool_argc, $tool_argv);
        }
      }
    }
    catch (Command_Line_Input_Error $e) {
      Logger::get_instance()->error('ASH', $e->getMessage());
    }
    catch (\Exception $e) {
      Logger::get_instance()->error('ASH', $e->getMessage());
    }
    return;
  }

  private function display_logo() {
    echo self::LOGO . PHP_EOL . PHP_EOL;
  }

  private function display_version() {
    echo self::NAME . ' version ' . self::VERSION . PHP_EOL;
  }

  private function create_line_input() {
    $result = Component::create('apdos\tools\ash\console\Command_Line_Input', '/bin/cmd_line');
    $result->init(array('name'=>self::NAME,
                        'description' => self::DESCRIPTION,
                        'version' => self::VERSION,
                        'add_help_option'=>TRUE,
                        'add_version_option'=>TRUE));
    $result->add_option('run_cmd', array(
        'short_name'=>'-r',
        'long_name'=>'--run_cmd',
        'description'=>'Insert one line command string',
        'help_name'=>'{execute command}',
        'action='=>'StoreString',
        'default'=>''
    ));
    return $result;
  }

  private function has_option(&$result, $option) {
      return strlen($result->options[$option]) > 0 ? true : false;
  }

  private function run_command($tool_argc, $tool_argv) {
    try {
      $cmd_name = $tool_argv[0];
      $path = '/bin/' . $cmd_name;
      $class_name = $this->get_tool_class_name($cmd_name);
      $actor = Kernel::get_instance()->find_object($path);
      $component = $actor->get_component($class_name);
      $component->main($tool_argc, $tool_argv);
    }
    catch (Command_Line_Input_Error $e) {
      Logger::get_instance()->error('ASH', $e->getMessage());
    }
    catch (Ash_Error $e) {
      Logger::get_instance()->error('ASH', $e->getMessage());
    }
    catch (\Exception $e) {
      Logger::get_instance()->error('ASH', $e->getMessage());
    }
  }

  private function get_tool_class_name($cmd_name) {
    if (!$this->has_cmd($cmd_name))
      throw new Ash_Error("Command \"$cmd_name\" is unknown.", Ash_Error::UNKNOW_COOMAND);
    return $this->cmds[$cmd_name];
  } 

  private function has_cmd($cmd_name) {
    return isset($this->cmds[$cmd_name]);
  }
}
