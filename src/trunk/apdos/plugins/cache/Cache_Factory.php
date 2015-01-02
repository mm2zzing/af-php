<?php
namespace apdos\plugins\cache;

use apdos\plugins\cache\handlers\File_Handler;
use apdos\plugins\cache\handlers\Null_Cache_Handler;
use apdos\plugins\config\Config;

class Cache_Factory {
  static public function create_handler($handler_name) {
    if ($handler_name == 'file') {
      return new File_Handler(Config::get_instance()->get_application_path() . '/cache');
    }
    return new Null_Cache_Handler();
  }
}
