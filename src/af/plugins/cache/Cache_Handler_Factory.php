<?php
namespace af\plugins\cache;

use af\plugins\cache\handlers\File_Handler;
use af\plugins\cache\handlers\Null_Cache_Handler;
use af\plugins\config\Config;

class Cache_Handler_Factory {
  public function create($handler) {
    if ($handler->type == 'file') {
      return new File_Handler($this->get_path($handler->path));
    }
    return new Null_Cache_Handler();
  }

  private function get_path($path) {
    if ($this->is_full_path($path))
      return $path;
    else
      return Config::get_instance()->get_application_path() . '/db/' . $path;
  }

  private function is_full_path($path) {
    return $path[0] == '/' ? true : false;
  }
}
