<?php
namespace apdos\tools\ash;

use apdos\kernel\actor\Component;

class Tool extends Component {
  public function __construct() {
  } 

  public function run($argc, $argv) {
  }

  protected function select_config($tool_path) {
    Tool_Config::get_instance()->select_tool($this->get_parent(), $tool_path);
  } 
}
