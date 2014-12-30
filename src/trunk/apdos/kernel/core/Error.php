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
  public function load($is_error_reporting, $is_display_errors) {
    if ($is_error_reporting)
      error_reporting(E_ALL);
    else
      error_reporting(0);
    if ($is_display_errors) 
      ini_set('display_errors', 'On');
   else
      ini_set('display_errors', 'Off');
    $this->register_handler();
  }

  private function register_handler() {
    set_error_handler(array($this, 'on_error'));
    // set_error_hander는 FATAL ERROR는 캐치하지 못한다.
    // FATAL ERROR는 로그 출력후 종료
    register_shutdown_function(function() { 
      $this->on_stop(); 
    });
  } 

  public function on_error($err_no, $err_str, $err_file, $err_line, $err_context = null) {
    if ($err_no == E_WARNING || 
        $err_no == E_STRICT || 
        $err_no == E_DEPRECATED ||
        $err_no == E_USER_DEPRECATED) {
      $message = "$err_str (error no: $err_no, error file: $err_file, error line: $err_line)";
      Logger::get_instance()->warning('ERROR_HANDLER', $message);
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
      Logger::get_instance()->critical('Error', $message);
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
