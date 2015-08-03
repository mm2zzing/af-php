<?php
namespace af\kernel\log\handlers;

use af\kernel\core\Object;

abstract class Logger_Handler extends Object {
  /**
   * 로그를 쓴다
   * 
   * @param log Log_DTO
   */
  abstract public function write($log); 
}
