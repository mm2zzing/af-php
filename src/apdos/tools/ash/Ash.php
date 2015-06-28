<?php
namespace apdos\tools\ash;

use apdos\kernel\actor\Actor;
use apdos\kernel\core\Kernel;
use apdos\tools\ashconsole\Command_Line;
use apdos\tools\ash\console\error\Command_Line_Error;
use apdos\tools\ash\error\Ash_Error;
use apdos\kernel\log\Logger;
use apdos\kernel\actor\Component;
use apdos\kernel\actor\net\Actor_Connecter;
use apdos\tools\ash\events\Shell_Command;
use apdos\tools\ash\dto\Argument_DTO;

class Ash extends Tool {
  const LOGO = '
            ___        __                ______                                             __  
           /   | _____/ /_____  _____   / ____/________ _____ ___  ___ _      ______  _____/ /__
          / /| |/ ___/ __/ __ \/ ___/  / /_  / ___/ __ `/ __ `__ \/ _ \ | /| / / __ \/ ___/ //_/
         / ___ / /__/ /_/ /_/ / /     / __/ / /  / /_/ / / / / / /  __/ |/ |/ / /_/ / /  / ,<  
        /_/  |_\___/\__/\____/_/     /_/   /_/   \__,_/_/ /_/ /_/\___/|__/|__/\____/_/  /_/|_| 
        ';
  const NAME = 'ash';
  const DESCRIPTION = 'Actor Framework/PHP shell';
  const VERSION = '0.0.1';

  private $actor_connecter;
  private $prompt = '';
  private $host = 'root@localhost';

  private $user;
  private $address;
  private $port;


  public function __construct() {
    $this->actor_connecter = Component::create('apdos\kernel\actor\net\Actor_Connecter', '/bin/actor_connecter');
  } 
  
  public function main($argc, $argv) {
    $this->display_logo();
    try {
      $cli = $this->create_line_input();
      $cli->parse($argc, $argv);
      if ($cli->has_arg('host_address')) {
        $this->host = $cli->get_arg('host_address');
      }
      $this->port = $cli->get_option('port');
      $this->user = $this->get_user();
      $this->address = $this->get_address();
      $this->prompt = $this->get_prompt();

      if ($cli->has_option('run_cmd')) {
        $this->display_version();
        $dtos = $this->parse_line($cli->get_option('run_cmd'));
        foreach ($dtos as $dto)
          $this->run_command($dto);
      }
      else {
        while (1) {
          $line = readline($this->prompt);
          if ($line == 'exit')
            break;
          $dtos = $this->parse_line($line);
          foreach ($dtos as $dto)
            $this->run_command($dto);
        }
      }
    }
    catch (Command_Line_Error $e) {
      Logger::get_instance()->error('ASH', $e->getMessage());
    }
    catch (\Exception $e) {
      Logger::get_instance()->error('ASH', $e->getMessage());
    }
    return;
  }

  /**
   * 입력된 문자열을 커맨드 단위로 파싱한다. |, & 연산자를 통해 커맨드 체인 기능
   * 지원이 된다.
   * 
   * @param input_str string 입력된 문자열
   *
   * @return array(Argument_DTO)
   */
  private function parse_line($input_str) {
    $result = array();
    $cmds = explode('&', $input_str);
    foreach ($cmds as $cmd) {
      $arguments = explode(' ', $cmd);
      array_push($result, new Argument_DTO($arguments));
    }
    return $result;
  }

  private function get_prompt() {
    return "$this->user@$this->address> ";
  }

  private function get_user() {
    $tokens = explode('@', $this->host);
    if (count($tokens) == 1)
      return 'root';
    else
      return $tokens[0];
  }

  private function get_address() {
    $tokens = explode('@', $this->host);
    if (count($tokens) == 1)
      return $tokens[0];
   else
      return $tokens[1];

  }

  private function display_logo() {
    echo self::LOGO . PHP_EOL . PHP_EOL;
  }

  private function display_version() {
    echo self::NAME . ' version ' . self::VERSION . PHP_EOL;
  }

  private function create_line_input() {
    $result = Component::create('apdos\tools\ash\console\Command_Line', '/bin/cmd_line');
    $result->init(array('name'=>self::NAME,
                        'description' => self::DESCRIPTION,
                        'version' => self::VERSION,
                        'add_help_option'=>TRUE,
                        'add_version_option'=>TRUE));
    $result->add_argument('host_address', array('optional'=>true, 'help_name'=>'[user@]host_address'));
    $result->add_option('run_cmd', array(
        'short_name'=>'-r',
        'long_name'=>'--run_cmd',
        'description'=>'Insert one line command string',
        'help_name'=>'{execute command}',
        'action'=>'StoreString',
        'default'=>''
    ));
    $result->add_option('port', array(
        'short_name'=> '-p',
        'long_name'=> '--port', 
        'description'=>'Bind port number', 
        'action'=>'StoreInt',
        'default'=>80
    ));
    return $result;
  }

  private function has_option(&$result, $option) {
      return strlen($result->options[$option]) > 0 ? true : false;
  }

  private function run_command($argument_dto) {
    try {
      $shell_command = new Shell_Command(array($argument_dto->get_count(), $argument_dto->gets(), $this->user));
      $address = 'http://' . $this->address . ':' . $this->port;
      $this->actor_connecter->send($address, $shell_command);

      Kernel::get_instance()->update();
    }
    catch (Command_Line_Error $e) {
      Logger::get_instance()->error('ASH', $e->getMessage());
    }
    catch (Ash_Error $e) {
      Logger::get_instance()->error('ASH', $e->getMessage());
    }
    catch (\Exception $e) {
      Logger::get_instance()->error('ASH', $e->getMessage());
    }
  } 
}
