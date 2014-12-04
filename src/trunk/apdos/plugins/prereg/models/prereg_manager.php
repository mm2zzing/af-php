<?php
require_once 'apdos/kernel/actor/component.php';
require_once 'apdos/plugins/prereg/dto/prereg_user_dto.php';
require_once 'apdos/plugins/prereg/accessors/prereg_user.php';
require_once 'apdos/plugins/prereg/errors/prereg_error.php';

class Prereg_Manager extends Component {
  private $storage;
  private $user_dto_class_name;

  /**
   * 컴포넌트를 시작
   *
   * @param storage Prereg_Storage 사용할 스토리지 인스턴스
   * @param user_dto_class_name String 사용할 Prereg_User_DTO 클래스명
  */
  public function start($storage, $user_dto_class_name) {
    $this->storage = $storage;
    $this->user_dto_class_name = $user_dto_class_name;
  } 


  /**
   * 전체 유저 정보 조회
   *
   * @return array(User) 유저 객체 리스트
   */
  public function get_prereg_users() {
    try {
      $users = $this->storage->get_prereg_users(array());

      $result = array();
      foreach ($users as $user) {
        $user_dto = new $this->user_dto_class_name;
        $user_dto->deserialize($users[0]);
        array_push($result, new Prereg_User($user_dto));
      }
      return $result;
    }
    catch (Prereg_Storage_Error $e) {
      throw new Prereg_Error($e->getMessage());
    }
    return new Null_Prereg_User();
  }

}
