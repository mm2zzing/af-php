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
  public function load() {
    $this->register_handler();
  }

  private function register_handler() {
    error_reporting(E_ALL);
    set_error_handler(array($this, 'on_error'));
  }

  public function on_error($err_no, $err_str, $err_file, $err_line, $err_context = null) {
    if ($err_no == E_WARNING || 
        $err_no == E_STRICT || 
        $err_no == E_DEPRECATED ||
        $err_no == E_USER_DEPRECATED) {
      $message = "$err_str (error no: $err_no, error file: $err_file, error line: $err_line)";
      Logger::warning('ERROR_HANDLER', $message);
      return;
    }
    $message = "$err_str (error file: $err_file, error line: $err_line)";
    throw new Core_Error($message, $err_no);
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new Error();
    }
    return $instance;
  }

}
