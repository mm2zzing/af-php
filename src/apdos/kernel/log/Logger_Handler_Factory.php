<?php
namespace apdos\kernel\log;

use apdos\kernel\log\handlers\File_Handler;
use apdos\kernel\log\handlers\Null_Logger_Handler;
use apdos\plugins\config\Config;

class Logger_Handler_Factory {
  public function create($handler) {
    if ($handler->type == 'file') {
      return new File_Handler($this->get_path($handler->path));
    }
    if ($handler->type == 'console') {
      return new Console_Handler();
    }
    return new Null_Logger_Handler();
  }

  private function get_path($path) {
    if ($this->is_full_path($path))
      return $path;
    else
      return Config::get_instance()->get_application_path() . '/' . $path;
  }

  private function is_full_path($path) {
    return $path[0] == '/' ? true : false;
  }
}
