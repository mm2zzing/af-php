<?php
namespace apdos\tools\ash\events;

use apdos\kernel\actor\events\Remote_Event;

class Shell_Command extends Remote_Event {
  public static $RUN = "run";

  public function __construct($args) {
    parent::__construct($args, array('', '', 'construct3'));
  }

  public function construct3($argc, $argv, $login_user) {
    $this->set_name(self::$RUN);
    $this->set_data(array('argc'=>$argc, 'argv'=>$argv, 'login_user'=>$login_user));
  }

  public function get_argc() {
    return $this->get_data()['argc'];
  }

  public function get_argv() {
    return $this->get_data()['argv'];
  }

  public function get_login_user() {
    return $this->get_data()['login_user'];
  }
}
