<?php
namespace apdos\kernel\log\handlers;

class Logd_Handler implements Logger_Handler {
  public function write($log) {
    $time = $log->get_time();
    $tag = $log->get_tag();
    $level = $log->get_level();
    $message = $log->get_message();
    // @TODO Logd접속후 로그 전송. 로테이션 설정은 Logd스스로 처리
    echo "[$time][$tag][$level]$message" . '<br/>';
  }
}
