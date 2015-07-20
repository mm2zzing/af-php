<?php
namespace apdos\kernel\core;

use apdos\kernel\core\errors\Core_Error;
use apdos\kernel\log\Logger;

/**
 * @class Error
 *
 * @bireif 
 * PHP 시스템 에러를 캐치해서 알려주는 역할을 한다.
 * PHP의 Error Handling 절차는 다음과 같다
 * 1. Fatal error인 경우(PHP 시스템 중지 상황 발생)
 * 
 * 이 에러 발생시에는 계속 처리할 수 없다. 로그 남기고 시스템 종료
 * display_errors 옵션에 따라 다음 처리로 분기된다
 * 
 *  a. display_errors가 On인 경우 에러 메시지가 담긴  HTTP 정상 응답
 *  b. display_errorsㅏ Off인 경우 HTTP 500 Internal Server Error 응답
 *             
 * 2. Fatal error가 아닌 경우
 *
 *  a. 경고성 에러는 로그만 남기고 계속 처리 진행
 *  b. 중단이 필요한 에러인 경우 Core_Error를 발생시켜 내부에서 처리
 *     Core_Error를 잡아서 처리하지 않으면 HTTP 500 에러 응답
 * 
 * @author Error 
 */ 
 class Error {
  public function __construct() {
  }

  /**
   * 시스템 시작에 필요한 정보를 로드
   */
  public function load($is_display_errors, $is_assert_active) {
    $this->setup_display_erros($is_display_errors); 
    $this->setup_assertion($is_assert_active);
    $this->register_handlers();
  }

  /**
   * 디버깅이 원활하도록 에러 발생 내용을 출력할지 여부를 설정한다.
   * Off로 설정할 경우 HTTP 500 Internal Server Error 응답이 가게 된다.
   */
  private function setup_display_erros($is_display) {
    error_reporting(E_ALL);
    if ($is_display)
      ini_set('display_errors', 'On');
    else
      ini_set('display_errors', 'Off');
  }

  private function setup_assertion($is_active) {
    if ($is_active)
      Assert::get_instance()->active(array($this,"on_assert"));
    else
      Assert::get_instance()->inactive();
  }

  public function on_assert($file, $line, $code, $desc=null) {
    $message = "ASSERT $desc (file: $file, line: $line, code: $code)";
    Logger::get_instance()->error('ERROR_HANDLER', $message);
    throw new Core_Error($message);
  } 

  private function register_handlers() {
    set_error_handler(array($this, 'on_error')); 
    register_shutdown_function(function() { 
      $this->on_stop(); 
    });
  } 

  /**
   * FATAL ERROR를 제외한 에러 처리
   * 
   * 일반 에러이면 예외를 발생시켜 내부에서 처리하고 그외 에러는 로그만을 남긴다.
   */
  public function on_error($err_no, $err_str, $err_file, $err_line, $err_context = null) {
    $message = "$err_str (error no: $err_no, error file: $err_file, error line: $err_line)";
    if ($err_no == E_WARNING || 
        $err_no == E_STRICT || 
        $err_no == E_DEPRECATED ||
        $err_no == E_USER_DEPRECATED) {
      Logger::get_instance()->warning('ERROR_HANDLER', $message);
      return;
    }
    if ($err_no == E_NOTICE || $err_no == E_USER_NOTICE) {
      Logger::get_instance()->notice('ERROR_HANDLER', $message);
      return;
    }
    // 예외 발생 시킨다. 예외를 잡아서 처리를 계속 하던지 에러 로그를 남기고 종료시킬지 결정
    throw new Core_Error($message, $err_no);
  }

  /** 
   * FATAL_ERROR 발생시킨 에러의 대한 내용을 critical 로그에 남긴다.
   */
  public function on_stop() {
    $last_error = error_get_last();
    if (isset($last_error['type']) && $last_error['type'] == E_ERROR) {
      $err_str = $last_error['message'];
      $err_file = $last_error['file'];
      $err_line = $last_error['line'];
      $message = "$err_str (error file: $err_file, error line: $err_line)";
      Logger::get_instance()->critical('ERROR_HANDLER', $message);
      Logger::get_instance()->debug('ERROR_HANDLER', 'os stop');
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
