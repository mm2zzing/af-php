<?php
namespace apdos\tools\ash\console;

use Console_CommandLine;
use Console_CommandLine_Exception;
use apdos\tools\ash\console\error\Command_Line_Input_Error;
use apdos\tools\ash\console\Command_Option_Event;
use apdos\kernel\actor\Component;

/**
 * @class Command_Line_Input
 *
 * @brief 유저의 입력을 받아서 파싱해주는 역할을 한다.
 *        Consoel_CommandLine 패키지를 사용하여 구현. Consoel_Table/Console_Color2 패키지
 *        역시 사용할 예정이다.
 *
 * @author Lee, Hyeon-gi
 */
class Command_Line_Input extends Component {
  private $cli;
  private $result;
  private $options = array();

  public function __construct() {
  }

  public function init($params) {
    $this->cli = new Console_CommandLine($params);
  }

  public function register_action($name, $class_name) {
    Console_CommandLine::registerAction($name, $class_name);
  } 
  public function add_option($name, $params) {
    array_push($this->options, $name);
    $this->cli->addOption($name, $params);
  }

  /**
   *
   * @param params array 인자의 타입. optional-> true or false, multiple -> true or false
   */
  public function add_argument($name, $params) {
    $this->cli->addArgument($name, $params);
  }

  public function add_command($name, $params) {
    return $this->cli->addCommand($name, $params);
  }

  public function add_command_option(&$command, $name, $params) {
    $command->addOption($name, $params);
  }

  public function parse($argc, $argv) {
    try {
      $this->result = $this->cli->parse($argc, $argv);
      foreach ($this->options as $option) {
        if ($this->has_option($option)) {
          $event = new Command_Option_Event($option, $this->get_option($option));
          $this->get_parent()->dispatch_event($event);
        }
      }
    }
    catch (Console_CommandLine_Exception $e) {
      throw new Command_Line_Input_Error($e->getMessage(), $e->getCode());
    }
    catch (Exception $e) {
      throw new Command_Line_Input_Error($e->getMessage(), $e->getCode());
    }
  }

  public function has_option($option) {
      return strlen($this->result->options[$option]) > 0 ? true : false;
  }

  public function get_option($option) {
    return $this->result->options[$option];
  }

  public function has_arg($name) {
    return isset($this->result->args[$name]) ? true : false;
  }

  public function get_arg($name) {
    if (isset($this->result->args[$name]))
      return $this->result->args[$name];
    else
      return '';
  }

  public function get_args() {
    return $this->result->args;
  }

  public function display_usage() {
    $this->cli->displayUsage(false);
  }
}
