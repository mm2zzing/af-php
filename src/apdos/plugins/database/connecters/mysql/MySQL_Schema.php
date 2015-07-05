<?php
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\component;
use apdos\plugins\database\connecters\mysql\errors\MySQL_Error;
use apdos\plugins\database\connecters\mysql\MySQL_Connecter;

/**
 * @class MySQL_Schema
 *
 * @brieif MySQL 데이타베이스와 테이블을 생성하는 방법들을 제공

           http://codeigniter-kr.org/user_guide_2.1.0/database/index.html와 같이
           
           액티브레코드를 사용하여 쿼리를 자동으로 생성한다.
 *
 * @authro Lee, Hyeon-gi
 */
class MySQL_Schema extends Component {

  /**
   * 데이타베이스를 생성한다.
   *
   * @param string name 데이터베이스명
   *
   * @return bool 성공 여부
   */
  public function create_database($name, $if_not_exists = true) {
    $query = 'CREATE DATABASE ';
    if ($if_not_exists)
      $query .= 'IF NOT EXISTS ';
    $query .= $name;
    return $this->get_connecter()->query($query)->is_success();
  }

  public function drop_database($name, $if_exists = true) {
    $query = 'DROP DATABASE ';
    if ($if_exists)
      $query .= 'IF EXISTS ';
    $query .= $name;
    return $this->get_connecter()->query($query)->is_success();
  }

  public function has_database($name) {
    $query = "SELECT schema_name FROM information_schema.schemata WHERE schema_name = '$name'";
    $result = $this->get_connecter()->query($query);
    $count = $result->get_rows_count();
    $result->close();
    return $count == 1 ? true : false;
  }

  /**
   * 테이블을 생성한다.
   *
   * @name 테이블 이름
   * @fields 테이블의 필드명
   *
   * @return bool 성공 여부
   */
  public function create_table($name, $fields) {
    $query = "CREATE TABLE $name(\n";

    $last_index = count($fields) - 1;
    $index = 0;

    end($fields);
    $last_key = key($fields);
    foreach ($fields as $key=>$value) {
      $query .= $this->get_field_query($key, $value);
      if($key != $last_key)
        $query .= ",\n";
    }
    $query .= "\n);";
    return $this->get_connecter()->query($query)->is_success();
  }

  private function get_field_query($name, $values) {
    $result = $name . ' ';
    $result .= $values['type'] . ' ';
    if (isset($values['unsigned'])) {
      if ($values['unsigned'])
        $result.= 'UNSIGNED ';
    }
    if (isset($values['null'])) {
      if (!$values['null'])
        $result .= 'NOT NULL ';
      else
        $result .= 'NULL ';
    }
    if (isset($values['auto_increment'])) {
      if ($values['auto_increment'])
        $result .= 'AUTO_INCREMENT ';
    }
    if (isset($values['primary_key'])) {
      if ($values['primary_key'])
        $result .= 'PRIMARY KEY ';
    }
    if (isset($values['default'])) {
      $default_value = $values['default'];
      if (is_string($default_value))
        $result .= "DEFAULT '$default_value'";
      else
        $result .= "DEFAULT $default_value";
    }
    return $result;
  }

  /**
   * 테이블을 삭제한다.
   *
   * @name string 테이블 이름
   *
   * @return bool 성공 여부
   */
  public function drop_table($name, $if_exists = true) {
    $query = "drop table ";
    if ($if_exists)
      $query .= 'if exists ';
    $query .= "$name;";
    return $this->get_connecter()->query($query)->is_success();
  }

  private function get_connecter() {
    $result = $this->get_component(MySQL_Connecter::get_class_name());
    if ($result->is_null())
      throw new MySQL_Error('Connecter is null', MySQL_Error::CONNECTER_IS_NULL);
    return $result;
  }
}
