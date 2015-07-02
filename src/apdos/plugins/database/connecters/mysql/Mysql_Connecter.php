<?php 
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\Component;
use apdos\plugins\database\connecters\mysql\errors\Mysql_Error;

class Mysql_Connecter extends Component {
  private $mysqli;
  private $database;

  public function connect($host, $user, $password, $port = "3306", $is_persistent = false) {
    if ($is_persistent)
      $host = 'p:' . $host;
    $this->mysqli = new \mysqli($host, $user, $password, '', $port);
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
 
  public function has_table($name) {
    $query = "SELECT table_name FROM information_schema.tables WHERE table_schema='$this->database' AND table_name = '$name'";
    $result = $this->query($query);
    $count = $result->get_rows_count();
    $result->close();
    return $count  == 1 ? true : false;
  } 

  /**
   * 쿼리문을 요청한 그 결과를 리턴한다.
   *
   * @param sql string sql문
   * @return Mysql_Result is_success 함수로 성공 여부를 알 수 있다.
   */
  public function query($sql) {
    $result = $this->mysqli->query($sql);
    if (!$result)
      throw new Mysql_Error($this->get_last_error(), Mysql_Error::QUERY_FAILED);
    return new Mysql_Result($result);
  }


  public function begin_trans() {
  }

  public function end_trans() {
  }

  public function get_last_error() {
    return $this->mysqli->error;
  }
}
