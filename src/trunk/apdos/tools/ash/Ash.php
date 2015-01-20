<?php
namespace apdos\tools\ash;

use apdos\kernel\actor\Actor;
use apdos\kernel\core\Kernel;
use apdos\tools\ashconsole\Command_Line_Input;
use apdos\tools\ash\console\error\Command_Line_Input_Error;
use apdos\tools\ash\error\Ash_Error;
use apdos\kernel\log\Logger;
use apdos\kernel\actor\Component;
use apdos\kernel\actor\Actor_Connecter;
use apdos\tools\ash\events\Shell_Command;

class Ash extends Tool {
  const LOGO = '
               (    (              )   (     
        (      )\ ) )\ )        ( /(   )\ )  
        )\    (()/((()/(        )\()) (()/(  
     ((((_)(   /(_))/(_))    __((_)\   /(_)) 
      )\ _ )\ (_)) (_))_    / /  ((_) (_))   
      (_)_\(_)| _ \ |   \  / /  / _ \ / __|    APD/OS-PHP shell 
       / _ \  |  _/ | |) |/_/  | (_) |\__ \  
      /_/ \_\ |_|   |___/       \___/ |___/';
  const NAME = 'ash';
  const DESCRIPTION = 'APD/OS-PHP shell';
  const VERSION = '0.0.1';
  const PROMPT = 'ash> ';

  private $actor_connecter;

  public function __construct() {
    $this->actor_connecter = Component::create('apdos\kernel\actor\Actor_Connecter', '/bin/actor_connecter');
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
      $shell_command = new Shell_Command();
      $shell_command->init($tool_argc, $tool_argv);
      $this->actor_connecter->send('http://211.50.119.82:10005', $shell_command);
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
}
