<?php
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\component;
use apdos\plugins\database\connecters\mysql\errors\Mysql_Error;

class Mysql_Connecter extends Component {
  private $mysqli;

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
    return $this->mysqli->select_db($name);
  }

  public function create_database($name) {
    return $this->query('CREATE DATABASE ' . $name);
  }

  public function drop_database($name) {
    return $this->query('DROP DATABASE ' . $name);
  }

  public function query($sql) {
    return $this->mysqli->query($sql);
  }
}
