<?php
require_once 'apdt/kernel/actor/component.php';

class Prereg_Storage extends Component {
  /**
   * 새로운 사전등록 유저를 추가
   *
   * @param user Array 유저 정보
   */
  public function register($user) {
  }

  /**
   * 사전등록 유저 정보를 조회
   *
   * @param where array 조회할 유저 정보
   * @return Array 유저 정보 리스트
   */
  public function get_prereg_users($wheres) {
  }

  /**
   * 사전등록 유저의 정보를 변경
   *
   * @parma where array 갱신할 유저 정보
   * @param contents array 갱신할 유저 데이터
   */ 
  public function update_prereg_user($wheres, $contents) {
  }
}
