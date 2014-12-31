<?php
namespace apdos\kernel\core;

use apdos\kernel\core\errors\Core_Error;
use apdos\kernel\log\Logger;

/**
 * @class Error
 *
 * @bireif PHP 시스템 에러를 캐치해서 알려주는 역할을 한다.
 * 
 * @author Error
 */
class Error {
  public function __construct() {
  }

  /**
   * 시스템 시작에 필요한 정보를 로드
   */
  public function load($is_error_reporting) {
    if ($is_error_reporting)
      error_reporting(E_ALL);
    else
      error_reporting(0);
    $this->register_handler();
  }

  private function register_handler() {
    /**
     * FATAL ERROR를 제외한 에러 발생시 예외를 발생시켜 내부에서 처리하도록 한다.
     * config.error_reporting 이 false로 지정시 에러 이벤트가 넘어오지 않는다.
     */
    set_error_handler(array($this, 'on_error'));

    /** 
     * FATAL_ERROR 발생시 critical 로그를 남긴다.
     */
    register_shutdown_function(function() { 
      $this->on_stop(); 
    });
  } 

  public function on_error($err_no, $err_str, $err_file, $err_line, $err_context = null) {
    // WARNING 에러는 로그만 남긴다.
    if ($err_no == E_WARNING || 
        $err_no == E_STRICT || 
        $err_no == E_DEPRECATED ||
        $err_no == E_USER_DEPRECATED) {
      $message = "$err_str (error no: $err_no, error file: $err_file, error line: $err_line)";
      Logger::get_instance()->warning('ERROR', $message);
      return;
    }
    $message = "$err_str (error file: $err_file, error line: $err_line)";
    throw new Core_Error($message, $err_no);
  }

  public function on_stop() {
    $last_error = error_get_last();
    if (isset($last_error['type']) && $last_error['type'] == E_ERROR) {
      $err_str = $last_error['message'];
      $err_file = $last_error['file'];
      $err_line = $last_error['line'];
      $message = "$err_str (error file: $err_file, error line: $err_line)";
      Logger::get_instance()->critical('ERROR', $message);
      Logger::get_instance()->debug('ERROR', 'os stop');
    }
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new Error();
    }
    return $instance;
  }

}
