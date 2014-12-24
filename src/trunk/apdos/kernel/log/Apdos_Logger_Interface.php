<?php
namespace apdos\kernel\log;

use Psr\Log\LoggerInterface;

/**
 * Psr-3 로그 인터페이스 구현
 *
 */
class Apdos_Logger_Interface implements LoggerInterface {
  private $handlers = array();

  public function emergency($message, array $context = array()) {
    $this->replace_message($message, $context);  
  }

  public function alert($message, array $context = array()) {
    $this->replace_message($message, $context);  
  }

  public function critical($message, array $context = array()) {
    $this->replace_message($message, $context);  
  }

  public function error($message, array $context = array()) {
    $this->replace_message($message, $context);  
  }

  public function warning($message, array $context = array()) {
    $this->replace_message($message, $context);  
  }

  public function notice($message, array $context = array()) {
    $this->replace_message($message, $context);  
  }

  public function info($message, array $context = array()) {
    $this->replace_message($message, $context);  
  }

  public function debug($message, array $context = array()) {
    $message = $this->replace_message($message, $context);  
    foreach ($this->handlers as $handler) {
      $handler->write($message);
    }
  }

  public function log($level, $message, array $context = array()) {
    $this->replace_message($message, $context);  
  }

  public function add_log_handler($log_handler) {
    array_push($this->handlers, $log_handler);
  }

  private function replace_message($message, $context) {
    $replace = array();
    foreach ($context as $key=>$val) {
      $replace['{' . $key . '}'] = $val;
    }
    return count($replace) == 0 ? $message : strstr($message, $replace);
  }
}
