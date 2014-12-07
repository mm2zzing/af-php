<?php
namespace apdos\tools\cli;

use apdos\kernel\core\entry;

class Command_Line_Runner extends Entry {
  public function __construct($loader) {
    parent::__construct($loader); 
  }

  public function run($entry_class_name) {
    //$this->loader->include_module($entry_class_name);
    $class = new $entry_class_name($this->loader);
    $class->run();
  }
}
