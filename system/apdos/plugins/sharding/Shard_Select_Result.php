<?php
namespace apdos\plugins\sharding;

/**
 * @class Shard_Select_Result
 *
 * @author Lee, Hyeon-gi
 */
class Shard_Select_Result { 
  /**
   *
   * @param result mysqli_result or boolean
   * @param time float query process time
   */
  public function __construct($select_function, $select_field, $rdb_results) {
    $value = NULL;
    foreach ($rdb_results as $result) {
      $this->time += $result->get_time();
      if (0 == $result->get_rows_count())
        continue;
      $select_value = $result->get_row(0, $select_field); 
      if ($select_value != NULL) {
        if (NULL == $value)
          $value = $select_value;
        if ($select_function == 'select_max') {
          if ($select_value > $value)
            $value = $select_value; 
        }
        if ($select_function == 'select_min') {
          if ($select_value < $value)
            $value = $select_value;
        }
      }
    }
    array_push($this->rows, array($select_field=>$value));
  }

  public function get_rows_count() {
    return count($this->rows);
  }

  public function get_rows() {
    return $this->rows;
  }

  public function get_row($index, $row_key = '') {
    if ($index >= count($this->rows))
      return $row_key == '' ? array() : '';
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
