<?php 
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\component;
use apdos\plugins\database\connecters\mysql\errors\Mysql_Error;

class Mysql_Connecter extends Component {
  private $mysqli;
  private $database;

  public function connect($host, $user, $password) {
    $this->mysqli = new \mysqli($host, $user, $password);
    if (mysqli_connect_errno())
      throw new Mysql_Error(mysqli_connect_error(), Mysql_Error::CONNECT_FAIL);
  }

  public function close() {
    $this->mysqli->close();
  }

  public function select_database($name) {
    if (!$this->mysqli->select_db($name))
      throw new Mysql_Error("Select database failed($name)", Mysql_Error::SELECT_DATABASE_FAIELD);
    $this->database = $name;
  }

  public function has_database($name) {
    $query = "select schema_name from information_schema.schemata where schema_name = '$name'";
    $result = $this->query($query);
    $count = $result->get_rows_count();
    $result->close();
    return $count == 1 ? true : false;
  }

  public function has_table($name) {
    $query = "select table_name from information_schema.tables where table_schema='$this->database' AND table_name = '$name'";
    $result = $this->query($query);
    $count = $result->get_rows_count();
    $result->close();
    return $count  == 1 ? true : false;
  }

  public function query($sql) {
    $result = $this->mysqli->query($sql);
    if (!$result)
      throw new Mysql_Error($this->get_last_error(), Mysql_Error::QUERY_FAILED);
    return new Mysql_Result($result);
  }

  public function simple_query($sql) {
    return $this->mysqli->query($sql);
  }

  public function begin_trans() {
  }

  public function end_trans() {
  }

  public function get_last_error() {
    return $this->mysqli->error;
  }
}
