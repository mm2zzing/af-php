<?php
namespace apdos\tools\service;

use apdos\tools\ash\Tool;
use apdos\tools\ash\console\Command_Line_Input;
use apdos\tools\ash\console\Command_Option_Event;
use apdos\tools\ash\console\error\Command_Line_Input_Error;
use apdos\tools\servicee\actions\Service_Help;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Kernel;

/**
 * @class Service
 *
 * @brief 생성되어 있는 Actor리스트를 트리구조로 출력 
 * @author Lee Hyeon-gi
 */
class Service extends Tool {
  const NAME = "service";
  const DESCRIPTION = "Router service on/off";
  const VERSION = '0.0.1';

  private $cli;
  private $service_lisetner;

  public function __construct() {   
  }

  public function main($argc, $argv) { 
    $this->cli = Component::create('apdos\tools\ash\console\Command_Line_Input', '/bin/cmd/service');
    $this->cli->init(array('name'=>self::NAME,
                           'description' => self::DESCRIPTION,
                           'version' => self::VERSION,
                           'add_help_option'=>FALSE));
    $this->service_lisetner = Component::create('apdos\tools\service\listener\Service_Listener', 
                                                '/bin/cmd/service_listener');
    $this->service_lisetner->init($this->cli);
    $this->cli->get_parent()->add_event_listener(Command_Option_Event::$COMMAND_OPTION_EVENT, 
                                                 $this->create_command_option_listener());
    $this->cli->add_option('help', array(
      'short_name'=>'-h',
      'long_name'=>'--help',
      'action'=>'StoreTrue',
      'description'=>'help'
    ));
    $this->cli->add_option('start', array(
        'long_name'=>'--start',
        'action'=>'StoreTrue',
        'description'=>'Router service start'
    ));
    $this->cli->add_option('stop', array(
        'long_name'=>'--stop',
        'action'=>'StoreTrue',
        'description'=>'Router service stop'
    ));

    try {
      $this->cli->parse($argc, $argv);
    }
    catch (Command_Line_Input_Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
    Kernel::get_instance()->delete_object($this->cli->get_parent()->get_path());
    Kernel::get_instance()->delete_object($this->service_lisetner->get_parent()->get_path());
  }

  private function create_command_option_listener() {
    $other = $this;
    return function($event) use(&$other) {
      if ($event->get_option_name() == 'help') {
        $other->service_lisetner->help();
      }
      if ($event->get_option_name() == 'start') {
        $other->service_lisetner->start();
      }
      if ($event->get_option_name() == 'stop') {
        $other->service_lisetner->stop();
      }
    };
  }
}
