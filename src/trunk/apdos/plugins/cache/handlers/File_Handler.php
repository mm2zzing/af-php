<?php
namespace apdos\plugins\cache\handlers;

use apdos\plugins\config\Config;
use apdos\kernel\core\Time;
use apdos\plugins\cache\errors\Cache_Error;

class File_Handler implements Cache_Handler {
  private $cache_directory;

  public function __construct($cache_directory) {
    $this->cache_directory = $cache_directory;
  }

  public function set($key, $value, $cache_time) {
    $data = array("value"=>$value, "expire_time"=>Time::get_instance()->get_timestamp() + $cache_time);
    file_put_contents($this->get_cache_path($key), serialize($data)); 
  }

  public function get($key) {
    if (!file_exists($this->get_cache_path($key)))
      throw new Cache_Error("Value is null. Key: $key", Cache_Error::CACHE_VALUE_IS_NULL);
    $contents = file_get_contents($this->get_cache_path($key));
    if (!$contents)
      throw new Cache_Error("Value is null. Key: $key", Cache_Error::CACHE_VALUE_IS_NULL);
    $data = unserialize($contents);  
    if (Time::get_instance()->get_timestamp() >= $data['expire_time']) {
      unlink($this->get_cache_path($key));
      throw new Cache_Error("Value is null. Key: $key", Cache_Error::CACHE_VALUE_IS_NULL);
    }
    else
      return $data['value'];
  }

  public function has($key) {
    try {
      $this->get($key);
      return true;
    }
    Catch (Cache_Error $e) {
      return false;
    }
  }

  private function get_cache_path($key) {
    return $this->cache_directory . '/' . $key . '.acache';
  }

  public function clear($key) {
    unlink($this->get_cache_path($key));
  }

  public function clear_all() {
    $files = scandir($this->cache_directory);
    foreach ($files as $key=>$value) {
      if (false !== strpos($value, '.acache')) {
        unlink($this->cache_directory . "/$value");
      }
    }
  }
}

