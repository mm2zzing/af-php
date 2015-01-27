<?php
namespace apdos\kernel\log;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use apdos\kernel\log\handlers\Logd_Handler;

/**
 * @class Logger
 * 
 * @birief 로깅 컴포넌트 RFC 5424 레벨에 따른 9가지 로깅 레벨을 제공한다. 
 *         그리고 PSR-4 로깅 인터페이스 규악을 준수한다.
 *
 * @author Lee, Hyeon-gi
 */
class Logger extends Component { 
  private $psr_logger; 

  /**
   * 시스템 정지할 정도의 최고 레벨 로그
   */
  public function emergency($tag, $message, $context = array()) {
    $this->psr_logger->select_tag($tag);
    $this->psr_logger->emergency($message, $context);
  }

  /**
   * 알림이 필요한 바로 수정해야할 상황 레벨의 로그 
   */
  public function alert($tag, $message, $context = array()) {
    $this->psr_logger->select_tag($tag);
    $this->psr_logger->alert($message, $context);
  }

  /**
   * 어플리케이션이 비정상적으로 종료될 정도의 레벨 로그
   */
  public function critical($tag, $message, $context = array()) {
    $this->psr_logger->select_tag($tag);
    $this->psr_logger->critical($message, $context);
  }

  /**
   * 어플리케이션이 그럭저럭 돌아갈정도의 에러 레벨 로그
   */
  public function error($tag, $message, $context = array()) {
    $this->psr_logger->select_tag($tag);
    $this->psr_logger->error($message, $context);
  }

  /**
   * 어플리케이션이 이상없이 실행될 수 있는 경고 레벨 로그
   */
  public function warning($tag, $message, $context = array()) {
    $this->psr_logger->select_tag($tag);
    $this->psr_logger->warning($message, $context);
  }

  /**
   * 알림이 필요한 이벤트 레벨 로그 
   */
  public function notice($tag, $message, $context = array()) {
    $this->psr_logger->select_tag($tag);
    $this->psr_logger->notice($message, $context);
  }

  /**
   * 기타 정보 출력을 위한 레벨 로그
   */
  public function info($tag, $message, $context = array()) {
    $this->psr_logger->select_tag($tag);
    $this->psr_logger->info($message, $context);
  }

  /**
   * 상세정보 파악을 위한 정보 레벨 로그
   */
  public function debug($tag, $message, $context = array()) {
    $this->psr_logger->select_tag($tag);
    $this->psr_logger->debug($message, $context);
  }

  public function log($tag, $level, $message, $context = array()) {
    $this->psr_logger->select_tag($tag);
    $this->psr_logger->log($level, $message, $context);
  }

  /**
   * 출력할 로그 레벨을 선택한다.
   *
   * @param levels array 선택할 로그 레벨 문자열
   */
  public function set_levels($levels) {
    $this->psr_logger->set_levels($levels);
  }

  public function set_psr_logger($psr_logger) {
    $this->psr_logger = $psr_logger;
  }

  public function remove_logger_handlers() {
    $this->psr_logger->remove_logger_handlers();
  }

  public function add_logger_handler($logger_handler) {
    $this->psr_logger->add_logger_handler($logger_handler);
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/logger');
      $instance = $actor->add_component('apdos\kernel\log\Logger');
      $instance->set_psr_logger(new Psr_Logger());
    }
    return $instance;
  }
}
