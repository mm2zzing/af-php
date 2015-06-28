<?php
namespace apdos\tools\ash\console;

use apdos\kernel\event\Event;

class Command_Option_Event extends Event {
  public static $COMMAND_OPTION_EVENT = "command_option_event";

  private $option_name;
  private $option_value;

  public function __construct($args) {
    parent::__construct($args, array('', 'construct2'));
  }

  public function construct2($option_name, $option_value) {
    $this->set_name(self::$COMMAND_OPTION_EVENT);
    $this->option_name = $option_name;
    $this->option_value = $option_value;
  }

  public function get_option_name() {
    return $this->option_name;
  }

  public function get_option_value() {
    return $this->option_value;
  }
}
