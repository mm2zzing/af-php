<?php 
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\log\Logger;
use apdos\kernel\core\Time;
use apdos\kernel\actor\Component;
use apdos\plugins\database\base\rdb\errors\RDB_Error;
use apdos\plugins\database\base\rdb\RDB_Connecter;

class MySQL_Connecter extends RDB_Connecter {
  private $mysqli;
  private $database;
  private $host_info;

  public function connect($host, $user, $password, $port = "3306", $is_persistent = false, $db_name = '') {
    if ($is_persistent)
      $host = 'p:' . $host;
    $this->mysqli = new \mysqli($host, $user, $password, $db_name, $port);
    if (mysqli_connect_errno())
      throw new RDB_Error(mysqli_connect_error(), RDB_Error::CONNECT_FAIL);
    $this->host_info = "$host:$port, user: $user";
  }

  public function close() {
    $this->mysqli->close();
  }

  public function select_database($name) {
    if (!$this->mysqli->select_db($name))
      throw new RDB_Error("Select database failed($name)", RDB_Error::SELECT_DATABASE_FAIELD);
    $this->database = $name;
  }
 
  public function has_table($name) {
    $query = "SELECT table_name FROM information_schema.tables WHERE table_schema='$this->database' AND table_name = '$name'";
    $result = $this->query($query);
    $count = $result->get_rows_count();
    return $count  == 1 ? true : false;
  } 

  /**
   * 쿼리문을 요청한 그 결과를 리턴한다.
   *
   * @param sql string sql문
   * @return MySQL_Result is_success 함수로 성공 여부를 알 수 있다.
   *
   * @throw RDB_Error 잘못된 쿼리 요청시에 예외 발생
   */
  public function query($sql) {
    $before = Time::get_instance()->get_timestamp();
    $result = $this->mysqli->query($sql);
    $time = Time::get_instance()->get_timestamp() - $before;
    Logger::get_instance()->info('QUERY', "$sql");
    Logger::get_instance()->info('QUERY', "(host: $this->host_info, db: $this->database, time: $time)");
    if (!$result)
      throw new RDB_Error($this->get_last_error(), RDB_Error::QUERY_FAILED);
    return new MySQL_Result($result, $time);
  }

  /**
   * 데이터를 추가한다.
   *
   * @param table_name string 테이블명
   * @param data array(key=>value) 데이터
   */
  public function insert($table_name, $data) {
    $query = $this->create_insert_key_query($table_name, $data); 
    $query .= ' VALUES';
    $query .= $this->create_insert_value_query($data);
    return $this->query($query);
  }

  /**
   * 데이터 여러개를 추가한다.
   *
   * @param table_name string 테이블명
   * @param data array(array(key=>value)) 데이터 콜렉션
   */
  public function insert_batch($table_name, $data) { 
    if (count($data) == 0)
      return new MySQL_Result(FALSE);

    $last_index = count($data) - 1;
    $query = '';
    for ($i = 0; $i < count($data); $i++) {
      if ($i == 0) {
        $query .= $this->create_insert_key_query($table_name, $data[$i]); 
        $query .= ' VALUES';
      }
      $query .= $this->create_insert_value_query($data[$i]);
      if ($i != $last_index)
        $query .= ', ';
    }
    return $this->query($query);
  }

  private function create_insert_key_query($table_name, $data) {
    end($data);
    $last_key = key($data);
    $query = "INSERT INTO $table_name (";
    foreach ($data as $key=>$value) {
      if ($key != $last_key)
        $query .= "$key,";
      else
        $query .= "$key)";
    }
    return $query;
  }

  private function create_insert_value_query($data) {
    end($data);
    $last_key = key($data);
    $query = '(';
    foreach ($data as $key=>$value) {
      if ($key != $last_key) {
        $query .= ($this->convert_insert_value($value) . ',');
      }
      else {
        $query .= ($this->convert_insert_value($value) . ')');
      } 
    }
    return $query;
  }

  private function convert_insert_value($value) {
    return is_string($value) ? "'$value'" : $value;
  }

  public function get($table_name, $limit = -1, $offset = -1) {
    $this->limit($limit, $offset);

    if ($this->limit != -1 && $this->offset != -1)
      $query = "SELECT * FROM $table_name LIMIT $this->offset, $this->limit";
    else
      $query = "SELECT * FROM $table_name";
    return $this->query($query);
  }

  public function limit($limit, $offset) {
    if ($limit != -1 && $offset != -1) {
      $this->limit = $limit;
      $this->offset = $offset;
    }
    return $this;
  }

  public function begin_trans() {
  }

  public function end_trans() {
  }

  public function get_last_error() {
    return $this->mysqli->error;
  }

  private $limit = -1;
  private $offset = -1;
}
