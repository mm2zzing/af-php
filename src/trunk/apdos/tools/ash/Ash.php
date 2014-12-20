<?php
namespace apdos\tools\ash;

use apdos\tools\ash\console\Command_Line_Input;
use apdos\tools\ash\console\error\Command_Line_Input_Error;

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
  }

  public function main($argc, $argv) {
    $this->display_logo();
    $cli = $this->create_line_input();
    try {
      $cli->parse($argc, $argv);
      if ($cli->has_option('run_cmd')) {
        $tool_argv = split(' ', $cli->get_option('run_cmd'));
        $tool_argc = count($tool_argv);
        $this->run_command($tool_argc, $tool_argv);
      }
      else {
        while (1) {
          $line = readline(self::PROMPT);
          $tool_argv = split(' ', $line);
          $tool_argc = count($tool_argv);
          $this->run_command($tool_argc, $tool_argv);
        }
      }
    }
    catch (Command_Line_Input_Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
    return;
  }

  private function display_logo() {
    echo self::LOGO . PHP_EOL . PHP_EOL;
  }

  private function create_line_input() {
    $result = new Command_Line_Input(
      array('name'=>'APD/OS Shell',
            'description' => 'APD/OS-PHP Shell',
            'version' => "0.0.1",
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
    $class_name = $this->get_tool_class_name($tool_argv[0]);
    $class = new $class_name();
    $class->main($tool_argc, $tool_argv);
  }

  private function get_tool_class_name($cmd_name) {
    return $this->cmds[$cmd_name];
  } 
}
