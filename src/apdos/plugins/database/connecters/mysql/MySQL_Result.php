<?php
namespace apdos\plugins\database\connecters\mysql;

use apdos\plugins\database\base\rdb\RDB_Result;

/**
 * @class MySQL_Result
 *
 * @brief 쿼리에 대한 결과를 표현하는 객체이다. 데이타베이스 API 사용시
 *        갱신 관련 쿼리는 성공 여부만 리턴하고 
 *        조회 쿼리는 조회한 데이터 내용을 리턴하는데 이 두가지 경우를 추상화
 *
 * @author Lee, Hyeon-gi
 */
class MySQL_Result extends RDB_Result {
  private $result;
  private $rows = array();
  private $time;

  /**
   *
   * @param result mysqli_result or boolean
   * @param time float query process time
   */
  public function __construct($result, $time = 0) {
    $this->result = $result;
    $this->time = $time;
    if (!$this->result_data_query()) {
      while($row = $result->fetch_assoc()) {
        array_push($this->rows, $row);
      }
      $this->result->close();
    }
  }

  public function is_success() {
    if ($this->result_data_query())
      return $this->result === TRUE ? true : false;
    else
      return true;
  }

  public function get_rows_count() {
    if ($this->result_data_query())
      return 0;
    return count($this->rows);
  }

  public function get_result() {
    return $this->rows;
  }
  
  public function get_time() {
    return $this->time;
  }

  private function result_data_query() {
    return $this->result === TRUE || $this->result === FALSE;
  }
}
