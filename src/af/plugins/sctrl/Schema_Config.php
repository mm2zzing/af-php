<?php
namespace af\plugins\sctrl;

use af\kernel\actor\Component;

class Schema_Config extends Component {
  public function __construct() {
  }

  public function load($database) {
    $this->database = $database;
  }

  public function get_database_config() {
    return $this->database;
  }

  private $database;
}
