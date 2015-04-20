<?php
namespace apdos\plugins\database\connecters\mysql;

class Mysql_Result {
  private $result;
  private $rows = array();

  /**
   *
   * @param result mysqli_result
   */
  public function __construct($result) {
    $this->result = $result;
    while($row = $result->fetch_assoc()) {
      array_push($this->rows, $row);
    }
  }

  public function get_rows_count() {
    return $this->result->num_rows;
  }

  public function get_result() {
    return $this->rows;
  }

  public function close() {
    $this->result->close();
  }
}
