<?php
namespace apdos\tools\ash\app\console;

use Console_CommandLine;
use Console_CommandLine_Exception;
use apdos\tools\ash\console\error\Command_Line_Input_Error;

/**
 * @class Command_Line_Input
 *
 * @brief 유저의 입력을 받아서 파싱해주는 역할을 한다.
 *        Consoel_CommandLine 패키지를 사용하여 구현. Consoel_Table/Console_Color2 패키지
 *        역시 사용할 예정이다.
 *
 * @author Lee, Hyeon-gi
 */
class Command_Line_Input {
  private $cli;
  private $result;

  public function __construct($params) {
    $this->cli = new Console_CommandLine($params);
  }

  public function add_option($name, $params) {
    $this->cli->addOption($name, $params);
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
}
