<?php
namespace apdos\kernel\log;

use apdos\kernel\core\Time;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Psr-3 로그 인터페이스 구현
 *
 */
class Psr_Logger implements LoggerInterface {
  private $logger_handlers = array();
  private $masks;
  private $level_mask = 0;
  private $tag;

  public function __construct() {
    $this->masks = array(
      LogLevel::EMERGENCY => 1,
      LogLevel::ALERT => 1 << 1,
      LogLevel::CRITICAL => 1 << 2,
      LogLevel::ERROR => 1 << 3,
      LogLevel::WARNING => 1 << 4,
      LogLevel::NOTICE => 1 << 5,
      LogLevel::INFO => 1 << 6,
      LogLevel::DEBUG => 1 << 7
    );

    foreach ($this->masks as $mask)
      $this->level_mask |= $mask;
  }

  public function select_tag($tag) {
    $this->tag = $tag;
  }

  public function emergency($message, array $context = array()) {
    $this->log(LogLevel::EMERGENCY, $message, $context);
  }

  public function alert($message, array $context = array()) {
    $this->log(LogLevel::ALERT, $message, $context);
  }

  public function critical($message, array $context = array()) {
    $this->log(LogLevel::CRITICAL, $message, $context);
  }

  public function error($message, array $context = array()) {
    $this->log(LogLevel::ERROR, $message, $context);
  }

  public function warning($message, array $context = array()) {
    $this->log(LogLevel::WARNING, $message, $context);
  }

  public function notice($message, array $context = array()) {
    $this->log(LogLevel::NOTICE, $message, $context);
  }

  public function info($message, array $context = array()) {
    $this->log(LogLevel::INFO, $message, $context);
  }

  public function debug($message, array $context = array()) {
    $this->log(LogLevel::DEBUG, $message, $context);
  }

  public function log($level, $message, array $context = array()) {
    if ($this->has_level($level)) {
      $message = $this->replace_message($message, $context);
      $log_dto = new Log_DTO(Time::get_instance()->get_ymd_his(), $this->tag, $level, $message);
      $this->write_log($log_dto);
    }
  }

  public function remove_logger_handlers() {
    $this->logger_handlers = array();
  }

  public function add_logger_handler($logger_handler) {
    array_push($this->logger_handlers, $logger_handler);
  }

  public function remove_logger_handler($class_name) {
    for ($i = 0; $i < count($this->logger_handlers); $i++) {
      if (get_class($this->logger_handlers[$i]) == $class_name) {
        array_splice($this->logger_handlers, $i, 1);
        return;
      }
    }
  }

  /**
   * 출력할 로그 레벨을 선택한다.
   *
   * @param levels array 선택할 레벨 문자열
   */
  public function set_levels($levels) {
    $mask = 0;
    foreach ($levels as $level) {
      $mask |= $this->masks[$level];
    }
    $this->level_mask = $mask;
  }

  private function has_level($level) {
    return $this->masks[$level] & $this->level_mask;
  }

  private function replace_message($message, $context) {
    $replace = array();
    foreach ($context as $key=>$val) {
      $replace['{' . $key . '}'] = $val;
    }
    return count($replace) == 0 ? $message : strtr($message, $replace);
  }

  private function write_log($log) {
    foreach ($this->logger_handlers as $handler) {
      $handler->write($log);
    }
  }
}
