<?php
require_once 'apdt/kernel/actor/component.php';
require_once 'apdt/plugins/db_connecters/mongodb/write_concern.php';

/**
 * @class Mongodb_Connecter
 *
 * @breif Mongodb 커넥터 컴퍼넌트
 *        upddate 함수의 w 옵션 1은 primary server에 입력이 완료되었음을 확인한다. 
 *        중요하지 않은 데이터는 0으로 설정하면 성능향상이 있다.(fire and forget)
 *
 */
class Mongodb_Connecter extends Component {
  private $client;
  private $database;
  private $collection;
  private $limit_count = 0;
  private $skip_offset = 0;
  private $wheres = array();

  public function __construct() {
    $this->client = new MongoClient();
  }

  /**
   * 서버에 접속한다.
   *
   * @param server String 접속핧 서버 주소 (ex) mongodb://localhost:27017)
   */
  public function connect($server) {
    $this->clinet = new MongoClient($server);
  }

  public function select_database($database_name) {
    $this->database = $this->client->{$database_name};
    return $this;
  }

  public function select_collection($collection_name) {
    $this->collection = $this->database->{$collection_name};
    return $this;
  }

  public function drop_collection($collection_name) {
    $this->collection = $this->database->{$collection_name};
    $this->collection->drop();
  }

  public function close() {
    $this->client->close();
  }

  public function insert($document, $write_concern = null) {
    $options = $this->get_options($write_concern); 
    $this->collection->insert($document, $options);
  }

  /**
   * 검색조건을 설정
   * @param wheres array 검색 조건
   * @return Mongodb_Connecter 
   */
  public function where($wheres) {
    $this->wheres = $wheres;
    return $this;
  }

  /**
   * 도큐먼트 하나를 찾아온다.
   *
   * @param where array 검색 조건
   * @return array 도큐먼트
   */
  public function find_one() {
    $result = $this->collection->findOne($this->wheres);
    $this->clear();
    return $result;
  }

  public function find() {
    $returns = array();
    $cursor = $this->collection->find($this->wheres)->limit($this->limit_count)->skip($this->skip_offset);
    foreach ($cursor as $doc) {
      array_push($returns, $doc);
    }
    $this->clear();
    return $returns;
  }

  private function clear() {
    $this->wheres = array();
    $this->limit_count = 0;
    $this->skip_offset = 0;
  }

  public function set_limit($count) {
    $this->limit_count = $count;
  }

  public function set_skip($offset) {
    $this->skip_offset = $offset;
  }

  /**
   * 도큐먼트의 정보를 교체
   *
   * @param document_data array 도큐먼트 데이터
   * @param write_concern Write_Concern 쓰기모드
   */
  public function update($document_data, $write_concern = null) {
    $options = $this->get_options($write_concern);
    $options['multiple'] = false;
    $this->collection->update($this->wheres, $document_data, $options);
    $this->clear();
  }

  /**
   * 하나의 도큐먼트 정보를 갱신
   *
   * @param wheres array 검색 조건
   * @param set_data array 변경할 속성 데이터
   */
  public function set($set_data, $write_concern = null) {
    $options = $this->get_options($write_concern);
    $options['multiple'] = false;
    $this->collection->update($this->wheres, array('$set' => $set_data), $options);
    $this->clear();
  } 

  /**
   * 모든 도큐먼트 정보를 갱신
   *
   * @param wheres array 검색 조건
   * @param set_data array 변경할 속성 데이터
   */

  public function set_all($set_data, $write_concern = null) {
    $options = $this->get_options($write_concern);
    $options['multiple'] = true;
    $this->collection->update($this->wheres, array('$set' => $set_data), $options);
    $this->clear();
  }

  private function get_options($write_concern) {
    if (null == $write_concern) {
      $w = new Acknowleged_Write();
      return $w->get_options();
    }
    else
      return $write_concern->get_options();
  }

  public function command($query) {
  }
}
