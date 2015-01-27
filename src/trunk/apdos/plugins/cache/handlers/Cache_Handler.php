<?php
namespace apdos\plugins\cache\handlers;

interface Cache_Handler {
  public function has($key);
  public function set($key, $value, $expire_time);
  public function get($key);
  public function clear($key);
  public function clear_all();
}

