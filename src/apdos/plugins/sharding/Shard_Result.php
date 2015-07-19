<?php
namespace apdos\plugins\sharding;

/**
 * @class Shard_Result
 *
 * @brief 쿼리에 대한 결과를 표현하는 객체이다.
 *
 * @author Lee, Hyeon-gi
 */
class Shard_Result { 
  /**
   *
   * @param result mysqli_result or boolean
   * @param time float query process time
   */
  public function __construct($rdb_results) {
    foreach ($rdb_results as $result) {
      $this->time += $result->get_time();
      array_merge($this->rows, $result->get_rows());
    }
  }

  public function get_rows_count() {
    return count($this->rows);
  }

  public function get_rows() {
    return $this->rows;
  }

  public function get_row($index, $row_key = '') {
    if ($index >= count($this->rows))
      return array();
    else {
      return $row_key == '' ? $this->rows[$index] : $this->rows[$index][$row_key];
    }
  }
  
  public function get_time() {
    return $this->time;
  }

  private $rows = array();
  private $time;

}
