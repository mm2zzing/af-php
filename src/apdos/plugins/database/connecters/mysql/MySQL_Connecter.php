<?php 
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\Component;
use apdos\plugins\database\connecters\mysql\errors\MySQL_Error;

class MySQL_Connecter extends Component {
  private $mysqli;
  private $database;

  public function connect($host, $user, $password, $port = "3306", $is_persistent = false) {
    if ($is_persistent)
      $host = 'p:' . $host;
    $this->mysqli = new \mysqli($host, $user, $password, '', $port);
    if (mysqli_connect_errno())
      throw new MySQL_Error(mysqli_connect_error(), MySQL_Error::CONNECT_FAIL);
  }

  public function close() {
    $this->mysqli->close();
  }

  public function select_database($name) {
    if (!$this->mysqli->select_db($name))
      throw new MySQL_Error("Select database failed($name)", MySQL_Error::SELECT_DATABASE_FAIELD);
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
   * @return MySQL_Result is_success 함수로 성공 여부를 알 수 있다.
   */
  public function query($sql) {
    $result = $this->mysqli->query($sql);
    if (!$result)
      throw new MySQL_Error($this->get_last_error(), MySQL_Error::QUERY_FAILED);
    return new MySQL_Result($result);
  }


  public function begin_trans() {
  }

  public function end_trans() {
  }

  public function get_last_error() {
    return $this->mysqli->error;
  }
}
