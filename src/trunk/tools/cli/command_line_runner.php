<?php
class Command_Line_Runner extends Entry {
  public function __construct($loader) {
    parent::__construct($loader); 
  }

  public function run($entry_module_path, $entry_class_name) {
    $this->loader->include_module($entry_module_path);
    $class = new $entry_class_name($this->loader);
    $class->run();
  }
}
