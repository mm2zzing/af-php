<?php 
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\component;
use apdos\plugins\database\connecters\mysql\errors\Mysql_Error;

class Mysql_Connecter extends Component {
  private $mysqli;
  private $database;

  public function connect($host, $user, $password) {
    try {
      $this->mysqli = new \mysqli($host, $user, $password);
    }
    catch (\Exception $e) {
      throw new Mysql_Error($e->getMessage(), Mysql_Error::CONNECT_FAIL);
    }
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
    return $result->num_rows == 1 ? true : false;
  }

  public function has_table($name) {
    $query = "select table_name from information_schema.tables where table_schema='$this->database' AND table_name = '$name'";
    $result = $this->query($query);
    return $result->num_rows == 1 ? true : false;
  }

  public function query($sql) {
    $result = $this->mysqli->query($sql);
    if (!$result)
      throw new Mysql_Error($this->get_last_error(), Mysql_Error::QUERY_FAILED);
    return $result;
  }

  public function get_last_error() {
    return $this->mysqli->error;
  }
}
