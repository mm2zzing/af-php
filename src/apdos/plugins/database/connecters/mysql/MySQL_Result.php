<?php
namespace apdos\plugins\database\connecters\mysql;

use apdos\plugins\database\base\rdb\RDB_Result;

/**
 * @class MySQL_Result
 *
 * @brief 쿼리에 대한 결과를 표현하는 객체이다.
 *
 * @author Lee, Hyeon-gi
 */
class MySQL_Result extends RDB_Result { 
  /**
   *
   * @param result mysqli_result or boolean
   * @param time float query process time
   */
  public function __construct($result, $time = 0) {
    $this->result = $result;
    $this->time = $time;
    if (!$this->bool_result()) {
      while($row = $result->fetch_assoc()) {
        array_push($this->rows, $row);
      }
      $this->result->close();
    }
    else
      array_push($this->rows, TRUE);
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

  private function bool_result() {
    return $this->result === TRUE || $this->result === FALSE;
  }

  private $result;
  private $rows = array();
  private $time;
  private $error_message;

}
