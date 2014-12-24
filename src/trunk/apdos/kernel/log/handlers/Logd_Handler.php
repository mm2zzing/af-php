<?php
namespace apdos\kernel\log\handlers;

class Logd_Handler implements Log_Handler {
  public function write($message) {
    echo $message . '<br/>';
  }
}
