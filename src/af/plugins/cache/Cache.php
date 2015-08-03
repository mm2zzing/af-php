<?php
namespace af\plugins\cache;
use af\kernel\core\Kernel;
use af\kernel\actor\Component;
use af\plugins\cache\handlers\Null_Cache_Handler;

/**
 * @class Cache
 *
 * @brief 데이터 캐시를 위한 컴포넌트. 캐시는 핸들러에 따라 File, Memcached, Redis등에 저장이 가능하다.
 *        기본은 파일에 캐시한다.
 * @author Lee, Hyeon-gi
 */
class Cache extends Component {
  const CACHE_FOREVER_TIME = 999999999;

  private $handler;

  public function __construct() {
    $this->handler = new Null_Cache_Handler();
  }

  public function set_handler($handler) {
    $this->handler = $handler;
  }

  public function remove_handler() {
    $this->handler = new Null_Cache_Handler();
  }

  public function set($key, $data, $cache_time = self::CACHE_FOREVER_TIME) {
    if (0 == $cache_time)
      return;
    $this->handler->set($key, $data, $cache_time);
  }

  public function get($key) {
    return $this->handler->get($key);
  }

  public function has($key) {
    return $this->handler->has($key);
  }

  public function clear($key) {
    $this->handler->clear($key);
  }

  public function clear_all() {
    $this->handler->clear_all();
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('af\kernel\actor\Actor', '/sys/cache');
      $instance = $actor->add_component('af\plugins\cache\Cache');
    }
    return $instance;
  }
}
