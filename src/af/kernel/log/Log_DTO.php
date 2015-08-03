<?php
namespace af\kernel\log;

class Log_DTO {
  private $datetime;
  private $tag;
  private $level;
  private $message;

  public function __construct($datetime, $tag, $level, $message) {
    $this->datetime = $datetime;
    $this->tag = $tag;
    $this->level = $level;
    $this->message = $message;
  }

  public function get_time() {
    return $this->datetime;
  }

  public function get_tag() {
    return $this->tag;
  }

  public function get_level() {
    return $this->level;
  }

  public function get_message() {
    return $this->message;
  }
}
