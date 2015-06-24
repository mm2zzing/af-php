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
    $cli = $this->create_line_input();
    try {
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
        $tool_argv = explode(' ', $cli->get_option('run_cmd'));
        $tool_argc = count($tool_argv);
        $this->run_command($tool_argc, $tool_argv);
      }
      else {
        while (1) {
          $line = readline($this->prompt);
          if ($line == 'exit')
            break;
          $tool_argv = explode(' ', $line);
          $tool_argc = count($tool_argv);
          $this->run_command($tool_argc, $tool_argv);
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
        'action='=>'StoreString',
        'default'=>''
    ));
    $result->add_option('port', array(
        'short_name'=> '-p',
        'long_name'=> '--port', 'description'=>'Bind port number', 'action'=>'StoreInt',
        'default'=>80
    ));
    return $result;
  }

  private function has_option(&$result, $option) {
      return strlen($result->options[$option]) > 0 ? true : false;
  }

  private function run_command($tool_argc, $tool_argv) {
    try {
      $shell_command = new Shell_Command();
      $shell_command->init($tool_argc, $tool_argv, $this->user);
      $address = 'http://' . $this->address . ':' . $this->port;
      $this->actor_connecter->send($address, $shell_command);
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
