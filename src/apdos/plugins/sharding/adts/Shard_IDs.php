<?php
namespace apdos\plugins\sharding\adts;

use apdos\kernel\core\Object;

/**
 * @class Shard_IDs
 *
 * @birief 샤드아이디 콜렉션을 표현
 *
 * @authoer Lee, Hyeon-gi
 */
class Shard_IDs extends Object {
  public function __construct($args = array()) {
    if (count($args))
      parent::__construct($args, array('construct1'));
    else
      parent::__construct();
    $this->ids = array();
  }

  public function construt1($ids) {
    $this->ids = $ids;
  }

  /**
   * 샤드아이디를 추가한다.
   *
   * @param shard_id Shard_ID
   */
  public function add($shard_id) {
    array_push($this->ids, $shard_id);
  }

  public function gets() {
    return $this->ids;
  }

  private $ids;
}

