<?php
namespace af\plugins\cache\handlers;

use af\plugins\cache\errors\Cache_Error;

class Null_Cache_Handler implements Cache_Handler {
  public function has($key) {
    return false;
  }

  public function set($key, $value, $expire_time) {
  }

  public function get($key) {
    if (!$this->has($key))
      throw new Cache_Error("Value is null. Key: $key", Cache_Error::CACHE_VALUE_IS_NULL);
  }

  public function clear($key) {
  }

  public function clear_all() {
  }
}

