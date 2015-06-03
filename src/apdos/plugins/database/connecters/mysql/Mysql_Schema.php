<?php
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\component;

/**
 * @class Mysql_Schema
 *
 * @brieif Mysql 데이타베이스와 테이블을 생성하는 방법들을 제공

           http://codeigniter-kr.org/user_guide_2.1.0/database/index.html와 같이
           
           액티브레코드를 사용하여 쿼리를 자동으로 생성한다.
 *
 * @authro Lee, Hyeon-gi
 */
class Mysql_Schema extends Component {
  /**
   * 데이타베이스를 생성한다.
   *
   * @param string name 데이터베이스명
   */
  public function create_database($name, $if_not_exists = true) {
    $query = 'create database ';
    if ($if_not_exists)
      $query .= 'if not exists ';
    $query .= $name;
    $this->get_connecter()->simple_query($query);
  }

  public function drop_database($name, $if_exists = true) {
    $query = 'drop database ';
    if ($if_exists)
      $query .= 'if exists ';
    $query .= $name;
    $this->get_connecter()->simple_query($query);
  }

  /**
   * 테이블을 생성한다.
   *
   * @name 테이블 이름
   * @fields 테이블의 필드명
   */
  public function create_table($name, $fields) {
    $query = "create table $name(\n";
    $query .= $this->get_field_query('id', array(
        'type'=>'int(11)',
        'unsigned'=>true,
        'auto_increment'=>true,
        'null'=>false,
        'primary_key'=>true
    ));
    foreach ($fields as $key=>$value) {
      $query .= ",\n";
      $query .= $this->get_field_query($key, $value);
    }
    $query .= "\n);";
    $this->get_connecter()->simple_query($query);
  }

  private function get_field_query($name, $values) {
    $result = $name . ' ';
    $result .= $values['type'] . ' ';
    if (isset($values['unsigned'])) {
      if ($values['unsigned'])
        $result.= 'unsigned ';
    }
    if (isset($values['null'])) {
      if (!$values['null'])
        $result .= 'not null ';
      else
        $result .= 'null ';
    }
    if (isset($values['auto_increment'])) {
      if ($values['auto_increment'])
        $result .= 'auto_increment ';
    }
    if (isset($values['primary_key'])) {
      if ($values['primary_key'])
        $result .= 'primary key ';
    }
    if (isset($values['default'])) {
      $default_value = $values['default'];
      if (is_string($default_value))
        $result .= "default '$default_value'";
      else
        $result .= "default $default_value";
    }
    return $result;
  }

  /**
   * 테이블을 삭제한다.
   *
   * @name string 테이블 이름
   */
  public function drop_table($name, $if_exists = true) {
    $query = "drop table ";
    if ($if_exists)
      $query .= 'if exists ';
    $query .= "$name;";
    $this->get_connecter()->simple_query($query);
  }

  private function get_connecter() {
    $result = $this->get_parent()->get_component('apdos\plugins\database\connecters\mysql\Mysql_Connecter');
    if ($result->is_null())
      throw new Mysql_Error('Connecter is null', Mysql_Error::CONNECTER_IS_NULL);
    return $result;
  }
}
