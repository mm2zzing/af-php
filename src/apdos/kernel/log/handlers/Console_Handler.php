<?php
namespace apdos\kernel\log\handlers;

class Console_Handler extends Logger_Handler {
  public function write($log) {
    $time = $log->get_time();
    $tag = $log->get_tag();
    $level = $log->get_level();
    $message = $log->get_message();
    print("[$time] [$level] [$tag] $message" . PHP_EOL);
  }
}
