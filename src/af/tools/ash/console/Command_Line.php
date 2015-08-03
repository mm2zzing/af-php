<?php
namespace af\tools\ash\console;

use Console_CommandLine;
use Console_CommandLine_Exception;
use af\tools\ash\console\error\Command_Line_Error;
use af\tools\ash\console\Command_Option_Event;
use af\kernel\actor\Component;

/**
 * @class Command_Line
 *
 * @brief 콘솔 프로그램의 인자와 옵션 처리를 위한 컴포넌트
 *        Consoel_CommandLine 패키지를 사용하여 구현. Consoel_Table/Console_Color2 패키지
 *        역시 사용할 예정이다.
 *
 * @author Lee, Hyeon-gi
 */
class Command_Line extends Component {
  private $cli;
  private $result;
  private $options = array();
  private $valid_options = array('short_name', 
                                 'long_name', 
                                 'description', 
                                 'help_name', 
                                 'action',
                                 'default');

  public function __construct() {
  }

  public function init($params) {
    $this->cli = new Console_CommandLine($params);
  }

  public function register_action($name, $class_name) {
    Console_CommandLine::registerAction($name, $class_name);
  } 

  /**
   * 파라미터 옵션을 추가한다. action타입에 StoreTrue, StoreFalse를
   * 지정하는 경우 옵션에 대한 추가 파라미터가 필요없다.
   *
   * @param name string 옵션명
   * @param params array(string=>value) 옵션 설정
   */
  public function add_option($name, $params) {
    foreach ($params as $key=>$value) {
      if (!in_array($key, $this->valid_options)) {
        throw new Command_Line_Error('parameter is invalid name: ' . $key ,
                                     Command_Line_Error::OPTION_NAME_IS_WRONG);
      }
    }
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
      throw new Command_Line_Error($e->getMessage(), $e->getCode());
    }
    catch (Exception $e) {
      throw new Command_Line_Error($e->getMessage(), $e->getCode());
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
