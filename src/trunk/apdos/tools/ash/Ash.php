<?php
namespace apdos\tools\ash;
//require_once 'Console/CommandLine.php';
use Console;

$p = new Console_CommandLine(array());

class Ash extends Tool {
  const REQUIRE_PARAMTER_COUNT = 0;

  private $cmds = array();
  private $options;

  public function __construct() {
    $this->options = '';
    $this->options .= "v:";
    $this->options .= "h:";
    $this->options .= "p:";
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

  public function main() {
    $this->print_logo();
    $params = getopt($this->options);

    echo print_r($params, true) . PHP_EOL;

    $host = 'http://localhost';
    $port = '80';
    $is_oneline_cmd = false;
    foreach ($params as $key=>$value) {
      switch ($key) {
        case 'v':
          $is_oneline_cmd = true;
        break;
        case 'h';
          $host = $value;
        break;
        case 'p';
          $port = $value;
        break;
      }
    }

    if ($is_oneline_cmd) {
    }
    else {
    }

    /*
    $tool_name = $this->cmds[$argv[1]];
    $tool_argc = $argc - 1;
    $tool_argv = array();
    for ($i = 1; $i < $argc; ++$i)
      array_push($tool_argv, $argv[$i]);
    $this->run_tool($tool_name, $tool_argc, $tool_argv);
    */
  }

  private function parse_parameter() {
  }

  private function run_tool($tool_name, $tool_argc, $tool_argv) {
    $class = new $tool_name();
    $class->main($argc - 1, $argv);
  }


  private function print_logo() {
    static $logo = '
               (    (              )   (     
        (      )\ ) )\ )        ( /(   )\ )  
        )\    (()/((()/(        )\()) (()/(  
     ((((_)(   /(_))/(_))    __((_)\   /(_)) 
      )\ _ )\ (_)) (_))_    / /  ((_) (_))   
      (_)_\(_)| _ \ |   \  / /  / _ \ / __|  
       / _ \  |  _/ | |) |/_/  | (_) |\__ \  
      /_/ \_\ |_|   |___/       \___/ |___/';
    echo $logo . PHP_EOL . PHP_EOL;
  }
}
