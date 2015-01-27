<?php
namespace apdos\kernel\log\handlers;

interface Logger_Handler {
  /**
   * 로그를 쓴다
   * 
   * @param log Log_DTO
   */
  public function write($log); 
}
