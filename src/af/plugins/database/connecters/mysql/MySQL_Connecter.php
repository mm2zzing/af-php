<?php 
namespace af\plugins\database\connecters\mysql;

use af\kernel\log\Logger;
use af\kernel\core\Time;
use af\kernel\actor\Component;
use af\plugins\database\base\rdb\errors\RDB_Error;
use af\plugins\database\base\rdb\RDB_Connecter;

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
   * @return MySQL_Result 수행 결과 정보 
   */
  public function query($sql) {
    Logger::get_instance()->debug('RDB-MYSQL', "Query: $sql");
    $before = Time::get_instance()->get_micro_timestamp();
    $result = $this->mysqli->query($sql);
    $time = Time::get_instance()->get_micro_timestamp() - $before;
    Logger::get_instance()->debug('RDB-MYSQL', "Connecter: host: $this->host_info, db: $this->database, time: $time");
    // TRUE or FALSE or mysqli_result object
    if (!$result) {
      Logger::get_instance()->error('RDB-MYSQL', "Query: $sql Error: " . $this->get_last_error());
      throw new RDB_Error($this->get_last_error(), RDB_Error::QUERY_FAILED);
    }
    return new MySQL_Result($result, $time);
  }

  /**
   * 데이터를 추가한다.
   *
   * @param table_name string 테이블명
   * @param data array(key=>value) 데이터
   */
  public function insert($table_name, $data) {
    $query = $this->create_insert_key_clause($table_name, $data); 
    $query .= ' VALUES';
    $query .= $this->create_insert_value_clause($data);
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
      throw new RDB_Error('insert data count is 0', RDB_Error::QUERY_FAILED);

    $last_index = count($data) - 1;
    $query = '';
    for ($i = 0; $i < count($data); $i++) {
      if ($i == 0) {
        $query .= $this->create_insert_key_clause($table_name, $data[$i]); 
        $query .= ' VALUES';
      }
      $query .= $this->create_insert_value_clause($data[$i]);
      if ($i != $last_index)
        $query .= ', ';
    }
    return $this->query($query);
  }

  private function create_insert_key_clause($table_name, $data) {
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

  private function create_insert_value_clause($data) {
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

  public function where($name, $value) {
    if (strlen($this->where_query))
      $this->where_query .= ("AND $name=" . $this->convert_value_format($value));
    else
      $this->where_query .= ("$name=" .$this->convert_value_format($value));
    return $this;
  }

  public function where_lt($name, $value) {
    if (strlen($this->where_query))
      $this->where_query .= ("AND $name<" . $this->convert_value_format($value));
    else
      $this->where_query .= ("$name<" .$this->convert_value_format($value));
    return $this;
  }

  public function where_lte($name, $value) {
    if (strlen($this->where_query))
      $this->where_query .= ("AND $name<=" . $this->convert_value_format($value));
    else
      $this->where_query .= ("$name<=" .$this->convert_value_format($value));
    return $this;
  }

  public function where_gt($name, $value) {
    if (strlen($this->where_query))
      $this->where_query .= ("AND $name>" . $this->convert_value_format($value));
    else
      $this->where_query .= ("$name>" .$this->convert_value_format($value));
    return $this;
  }

  public function where_gte($name, $value) {
    if (strlen($this->where_query))
      $this->where_query .= ("AND $name>=" . $this->convert_value_format($value));
    else
      $this->where_query .= ("$name>=" .$this->convert_value_format($value));
    return $this;
  }

  public function where_not($name, $value) {
    if (strlen($this->where_query))
      $this->where_query .= ("AND $name!=" . $this->convert_value_format($value));
    else
      $this->where_query .= ("$name!=" .$this->convert_value_format($value));
    return $this;
  }

  public function or_where($name, $value) {
    $this->where_query .= ("OR $name=" .$this->convert_value_format($value));
    return $this;
  }

  public function or_where_lt($name, $value) {
    $this->where_query .= ("OR $name<" .$this->convert_value_format($value));
    return $this;
  }

  public function or_where_lte($name, $value) {
    $this->where_query .= ("OR $name<=" .$this->convert_value_format($value));
    return $this;
  }

  public function or_where_gt($name, $value) {
    $this->where_query .= ("OR $name>" .$this->convert_value_format($value));
    return $this;
  }

  public function or_where_gte($name, $value) {
    $this->where_query .= ("OR $name>=" .$this->convert_value_format($value));
    return $this;
  }

  public function or_where_not($name, $value) {
    $this->where_query .= ("OR $name!=" .$this->convert_value_format($value));
    return $this;
  }

  /**
   * 와일드카드(%)를 사용하지 않는 LIKE 쿼리를 생성한다.
   *
   * @param name string 필드명
   * @param value string 필드 비교값
   */
  public function like($name, $value) {
    $this->_like($name, $value, 'AND', 'none');
    return $this;
  }

  public function or_like($name, $value) {
    $this->_like($name, $value, 'OR', 'none');
    return $this;
  } 

  public function not_like($name, $value) {
    $this->_not_like($name, $value, 'AND', 'none');
    return $this;
  }

  public function or_not_like($name, $value) {
    $this->_not_like($name, $value, 'OR', 'none');
    return $this;
  }

  /**
   * 와일드카드(%)를 헤더에만 사용하는 LIKE 쿼리를 생성한다.
   *
   * @param name string 필드명
   * @param value string 필드 비교값
   */
  public function w_like($name, $value) {
    $this->_like($name, $value, 'AND', 'before');
    return $this;
  }

  public function or_w_like($name, $value) {
    $this->_like($name, $value, 'OR', 'before');
    return $this;
  }

  public function not_w_like($name, $value) {
    $this->_not_like($name, $value, 'AND', 'before');
    return $this;
  }

  public function or_not_w_like($name, $value) {
    $this->_not_like($name, $value, 'OR', 'before');
    return $this;
  }

  /**
   * 와일드카드(%)를 풋터에만 사용하는 LIKE 쿼리를 생성한다.
   *
   * @param name string 필드명
   * @param value string 필드 비교값
   */
  public function like_w($name, $value) {
    $this->_like($name, $value, 'AND', 'after');
    return $this;
  }

  public function or_like_w($name, $value) {
    $this->_like($name, $value, 'OR', 'after');
    return $this;
  }

  public function not_like_w($name, $value) {
    $this->_not_like($name, $value, 'AND', 'after');
    return $this;
  }

  public function or_not_like_w($name, $value) {
    $this->_not_like($name, $value, 'OR', 'after');
    return $this;
  }

  /**
   * 와일드카드(%)를 사용하는 LIKE 쿼리를 생성한다.
   *
   * @param name string 필드명
   * @param value string 필드 비교값
   */

  public function w_like_w($name, $value) {
    $this->_like($name, $value, 'AND', 'both');
    return $this;
  }

  public function or_w_like_w($name, $value) {
    $this->_like($name, $value, 'OR', 'both');
    return $this;
  }

  public function not_w_like_w($name, $value) {
    $this->_not_like($name, $value, 'AND', 'both');
    return $this;
  }

  public function or_not_w_like_w($name, $value) {
    $this->_not_like($name, $value, 'OR', 'both');
    return $this;
  }

  private function _like($name, $value, $operator, $wildcard) {
    $value = $this->convert_wild_card_value($value, $wildcard);
    if (strlen($this->where_query) == 0)
      $this->where_query .= ("$name LIKE '$value'");
    else
      $this->where_query .= ("$operator $name LIKE '$value'");
  }

  private function _not_like($name, $value, $operator, $wildcard) {
    $this->convert_wild_card_value($value, $wildcard);
    if (strlen($this->where_query) == 0)
      $this->where_query .= ("$name NOT LIKE '$value'");
    else
      $this->where_query .= ("$operator $name NOT LIKE '$value'");
  }

  private function convert_wild_card_value($value, $wildcard) {
    $value = $this->escape_str($value, true);
    if ($wildcard == 'before')
      $value = "%$value";
    if ($wildcard == 'after')
      $value = "$value%";
    if ($wildcard == 'both')
      $value = "%$value%";
    return $value;
  }

  public function get($table_name, $limit = -1, $offset = -1) {
    $this->limit($limit, $offset);
    if ($this->select_function == '')
      $select_fields = $this->create_select_field_clause();
    else
      $select_fields = $this->create_select_function_field_clause();
    $query = "SELECT $select_fields FROM $table_name";
    if (strlen($this->join_query))
      $query .= " $this->join_query";
    if (strlen($this->where_query))
      $query .= " WHERE $this->where_query";
    if ($this->limit != -1 && $this->offset != -1)
      $query .= " LIMIT $this->offset, $this->limit";
    if (count($this->order_clauses))
      $query .= $this->create_order_clause();
    $this->reset_query();
    return $this->query($query);
  }

  public function get_where($table_name, $wheres, $limit = -1, $offset = -1) {
    $this->limit($limit, $offset);
    if ($this->select_function == '')
      $select_fields = $this->create_select_field_clause();
    else
      $select_fields = $this->create_select_function_field_clause();
    $query = "SELECT $select_fields FROM $table_name";
    if (strlen($this->join_query))
      $query .= " $this->join_query";
    $query .= $this->create_where_clause($wheres);
    if ($this->limit != -1 && $this->offset != -1)
      $query .= " LIMIT $this->offset, $this->limit";
    if (count($this->order_clauses))
      $query .= $this->create_order_clause();
    $this->reset_query();
    return $this->query($query);
  }

  private function create_order_clause() {
    $clause = " ORDER BY ";
    end($this->order_clauses);
    $last_key = key($this->order_clauses);
    foreach ($this->order_clauses as $key=>$value) {
      if ($key != $last_key)
        $clause .= ($value . ', ');
      else
        $clause .= $value;
    }
    return $clause;
  }

  private function create_select_field_clause() {
    end($this->select_fields);
    $last_key = key($this->select_fields);
    $query = '';
    foreach ($this->select_fields as $key=>$value) {
      if ($key != $last_key) {
        $query .= ($value . ',');
      }
      else {
        $query .= ($value);
      } 
    }
    return $query == '' ? '*' : $query;
  }

  private function create_select_function_field_clause() {
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
    $query .= $this->create_where_clause($wheres);
    return $this->query($query);
  }

  public function delete_all($table_name) {
    $query = "DELETE FROM $table_name";
    return $this->query($query);
  }

  private function create_where_clause($wheres) {
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

  /**
   * JOIN 쿼리를 생성한다.
   *
   * @param table_name string 조인 대상이되는 테이블 네임
   * @param condition string 조인 조건
   * @param type string 조인 종류(inner(default), outer, left(=left_outer), right(=right_outer), left_outer, right_outer)
   */
  public function join($table_name, $condition, $type = '') {
    $this->join_query .= $this->select_join($type);
    $this->join_query .= " $table_name";
    if (strlen($condition))
      $this->join_query .= " ON $condition";
    return $this;
  }

  public function order_by_asc($field_name) {
    array_push($this->order_clauses, "$field_name ASC");
    return $this;
  }

  public function order_by_desc($field_name) {
    array_push($this->order_clauses, "$field_name DESC");
    return $this;
  }

  public function update($table_name, $values) {
    $query = "UPDATE $table_name";
    $query .= $this->create_set_clause($values);
    if (strlen($this->where_query))
      $query .= " WHERE $this->where_query";
    $this->reset_query();
    return $this->query($query);
  }

  public function update_where($table_name, $values, $wheres) {
    $query = "UPDATE $table_name";
    $query .= $this->create_set_clause($values);
    $query .= $this->create_where_clause($wheres);
    $this->reset_query();
    return $this->query($query);
  }

  private function create_set_clause($set_values) {
    $query = ' SET ';
    end($set_values);
    $last_key = key($set_values);
    foreach ($set_values as $key=>$value) {
      $query .= ($key . '=' . $this->convert_value_format($value));
      if ($key != $last_key)
        $query .= ', ';
    }
    return $query;
  }

  private function select_join($type) {
    if ($type == 'inner')
      return 'INNER JOIN';
    else if($type == 'outer')
      return 'OUTER JOIN';
    else if($type == 'left')
      return 'LEFT JOIN';
    else if($type == 'right')
      return 'RIGHT JOIN';
    else if($type == 'left_outer')
      return 'LEFT OUTER JOIN';
    else if($type == 'right_outer')
      return 'RIGHT OUTER JOIN';
    else
      return 'JOIN';
  } 

  private function convert_value_format($value) {
    return is_string($value) ? '\''. $this->escape_str($value) . '\'' : $this->escape_str($value);
  }

  /**
   * 특수 문자열을 이스케이프하여 실행되지 않도록 막아준다.(\x00,\n,\r,\,',",\x1a)
   */
  private function escape_str($str, $like = false) {
    if ($this->escape_query) {
      $str = mysqli_real_escape_string($this->mysqli, $str);
      if ($like)
        $str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
      return $str;
    }
    else
      return $str;
  }

  public function begin_trans() {
  }

  public function end_trans() {
  }

  public function get_last_error() {
    return $this->mysqli->error;
  }

  private function reset_query() {
    $this->limit = -1;
    $this->offset = -1;
    $this->select_fields = array();
    $this->select_function = '';
    $this->select_function_field = '';
    $this->select_function_as_field_name = '';
    $this->where_query = '';
    $this->order_clauses = array();
  }


  public function toggle_escape_query($enable) {
    $this->escape_query = $enable;
  }

  private $limit = -1;
  private $offset = -1;
  private $join_query = '';
  private $select_fields = array();
  private $select_function = '';
  private $select_function_field = '';
  private $select_function_as_field_name = '';
  private $where_query = '';
  private $order_clauses = array();
  private $escape_query = true;

}
