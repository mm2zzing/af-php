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
    Logger::get_instance()->debug('RDB-MYSQL', "Query: $sql");
    $before = Time::get_instance()->get_timestamp();
    $result = $this->mysqli->query($sql);
    $time = Time::get_instance()->get_timestamp() - $before;
    Logger::get_instance()->debug('RDB-MYSQL', "Connecter: host: $this->host_info, db: $this->database, time: $time");
    if (!$result)
      throw new RDB_Error($this->get_last_error(), RDB_Error::QUERY_FAILED);
    $mysql_result = new MySQL_Result($result, $time);
    Logger::get_instance()->debug('RDB-MYSQL', 'Result: '. var_export($mysql_result->get_rows(), true));
    return $mysql_result;
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
        $query .= ($this->convert_value_format($value) . ',');
      }
      else {
        $query .= ($this->convert_value_format($value) . ')');
      } 
    }
    return $query;
  }

  public function get($table_name, $limit = -1, $offset = -1) {
    $this->limit($limit, $offset);
    if ($this->select_function == '')
      $select_fields = $this->create_select_field_query();
    else
      $select_fields = $this->create_select_function_field_query();
    if ($this->limit != -1 && $this->offset != -1)
      $query = "SELECT $select_fields FROM $table_name LIMIT $this->offset, $this->limit";
    else
      $query = "SELECT $select_fields FROM $table_name";
    $this->reset_limit();
    $this->reset_select();
    return $this->query($query);
  }

  public function get_where($table_name, $wheres, $limit = -1, $offset = -1) {
    $this->limit($limit, $offset);
    if ($this->select_function == '')
      $select_fields = $this->create_select_field_query();
    else
      $select_fields = $this->create_select_function_field_query();
    $query = "SELECT $select_fields FROM $table_name";
    $query .= $this->create_where_query($wheres);
    if ($this->limit != -1 && $this->offset != -1)
      $query .= " LIMIT $this->offset, $this->limit";
    $this->reset_limit();
    $this->reset_select();
    return $this->query($query);
  }

  private function create_select_field_query() {
    end($this->select_fields);
    $last_key = key($this->select_fields);
    $query = '';
    foreach ($this->select_fields as $key=>$value) {
      if ($key != $last_key) {
        $query .= ($this->convert_value_format($value) . ',');
      }
      else {
        $query .= ($this->convert_value_format($value));
      } 
    }
    return $query == '' ? '*' : $query;
  }

  private function create_select_function_field_query() {
    if ($this->select_function_as_field_name == '')
      return "$this->select_function($this->select_function_field) as $this->select_function_field";
    else
      return "$this->select_function($this->select_function_field) as $this->select_function_as_field_name";
  }


  public function limit($limit, $offset) {
    if ($limit != -1 && $offset != -1) {
      $this->limit = $limit;
      $this->offset = $offset;
    }
    return $this;
  } 

  public function select($select_fields) {
    $this->select_fields = $select_fields;
    return $this;
  }

  public function select_max($max_field, $as_field_name = '') {
    $this->select_function = 'MAX';
    $this->select_function_field = $max_field;
    $this->select_function_as_field_name = $as_field_name;
    return $this;
  }

  public function select_min($min_field, $as_field_name = '') {
    $this->select_function = 'MIN';
    $this->select_function_field = $min_field;
    $this->select_function_as_field_name = $as_field_name;
    return $this;
  }

  public function select_avg($min_field, $as_field_name = '') {
    $this->select_function = 'AVG';
    $this->select_function_field = $min_field;
    $this->select_function_as_field_name = $as_field_name;
    return $this;
  }

  public function select_sum($min_field, $as_field_name = '') {
    $this->select_function = 'SUM';
    $this->select_function_field = $min_field;
    $this->select_function_as_field_name = $as_field_name;
    return $this;
  }

  /**
   * 특정 테이블의 데이타 갯수를 조회한다.
   *
   * @param string table_name 테이블 명
   *
   * @return int 데이터의 갯수
   *
   * @throw RDB_Error 잘못된 쿼리 요청시에 예외 발생
   */
  public function count($table_name) {
    $select = "COUNT(*)";
    $query = "SELECT $select FROM $table_name";
    $result = $this->query($query);
    return $result->get_row(0, $select);
  }

  public function delete($table_name, $wheres) {
    $query = "DELETE FROM $table_name";
    $query .= $this->create_where_query($wheres);
    return $this->query($query);
  }

  private function create_where_query($wheres) {
    $query = ' WHERE ';
    end($wheres);
    $last_key = key($wheres);
    foreach ($wheres as $key=>$value) {
      $query .= ($key . '=' . $this->convert_value_format($value));
      if ($key != $last_key)
        $query .= ' AND ';
    }
    return $query;
  }

  private function convert_value_format($value) {
    return is_string($value) ? "'$value'" : $value;
  }

  public function begin_trans() {
  }

  public function end_trans() {
  }

  public function get_last_error() {
    return $this->mysqli->error;
  }

  private function reset_limit() {
    $this->limit = -1;
    $this->offset = -1;
  }

  private function reset_select() {
    $this->select_fields = array();
    $this->select_function = '';
    $this->select_function_field = '';
    $this->select_function_as_field_name = '';
  }

  private $limit = -1;
  private $offset = -1;
  private $select_fields = array();
  private $select_function = '';
  private $select_function_field = '';
  private $select_function_as_field_name = '';

}