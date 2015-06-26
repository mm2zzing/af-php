<?php
namespace apdos\plugins\sharding;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;

class Shard_Router extends Component {
  private $router;

  public function __construct() {
  }

  /**
   * 샤딩 설정을 로드한다.
   *
   * @param shard_tables array(object) 샤딩 테이블 설정
   * @param lookup_shards array(object) 룩업 샤드 설정
   * @param data_shards array(object) 데이터 샤드 설정
   */
  public function load($shard_tables, $lookup_shard, $data_shards) {
    //$this->router = new Shard_Config_DTO($shard_config);
  }

  public function insert($shard_table_id, $data) {
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/srouter');
      $instance = $actor->add_component('apdos\plugins\sharding\Shard_Router');
    }
    return $instance;
  }
}
