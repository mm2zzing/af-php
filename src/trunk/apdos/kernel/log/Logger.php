<?php
namespace apdos\kernel\log;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Time;
use Psr\Log\LoggerInterface;
use apdos\kernel\log\handlers\Logd_Handler;

class Logger extends Component {
  private $logger_interface;

  public function debug($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    $message = '[' . $ctime . '][' . $tag . '][DEBUG] ' . $message . PHP_EOL;
    $this->logger_interface->debug($message);
  }

  public function warning($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][WARN] ' . $message . PHP_EOL;
    $this->logger_interface->warning($message);
  }

  public function notice($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][TRACE] ' . $message . PHP_EOL;
    $this->logger_interface->notice($message);
  }

  public function info($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][INFO] ' . $message . PHP_EOL;
    $this->logger_interface->info($message);
  }

  public function error($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][ERROR] ' . $message . PHP_EOL;
    $this->logger_interface->error($message);
  }

  public function critical($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][FATAL] ' . $message . PHP_EOL;
    $this->logger_interface->critical($message);
  }

  public function set_psr_log_interface($logger_interface) {
    $this->logger_interface = $logger_interface;
  }

  public function add_log_handler($logger_handler) {
    $this->logger_interface->add_log_handler($logger_handler);
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/logger');
      $instance = $actor->add_component('apdos\kernel\log\Logger');
      $instance->set_psr_log_interface(new Apdos_Logger_Interface());
      $instance->add_log_handler(new Logd_Handler());
    }
    return $instance;
  }
}
