<?php
require_once 'apdos/kernel/actor/component.php';
require_once 'apdos/plugins/prereg/dto/prereg_user_dto.php';
require_once 'apdos/plugins/prereg/accessors/prereg_user.php';
require_once 'apdos/plugins/prereg/errors/prereg_error.php';

class Prereg extends Component {
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
   * 사전 등록 유저 정보 추가.
   */
  public function register_by_email($email, $values) {
    $user = new $this->user_dto_class_name;
    $user->email = $email;
    $this->read_values($user, $values);
    $this->register_user($user);
    return $this->get_prereg_user(array('email'=>$email));
  }

  /**
   * 사전 등록 유저 정보 추가.
   */
  public function register_by_phonenumber($phonenumber, $values) {
    $user = new $this->user_dto_class_name;
    $user->phonenumber = $phonenumber;
    $this->read_values($user, $values);
    $this->register_user($user);
    return $this->get_prereg_user(array('phonenumber'=>$phonenumber));
  }

  private function read_values($user, $values) {
    foreach ($values as $key=>$value) {
      if (!isset($user->{$key}))
        throw Prereg_User_Property_Not_Exist($key . ' is not exist');
      $user->{$key} = $value;
    }
  }

  private function register_user($user) {
    try {
      $array = Object_Converter::to_array($user);
      $this->storage->register($array);
    }
    catch (Prereg_Storage_Error $e) {
      throw new Prereg_Error($e->getMessage());
    }
  }

  /**
   * 유저 정보 조회
   *
   * @param user_wheres array 찾으려는 유저 데이터 
   * @return User 유저 객체 
   */
  public function get_prereg_user($user_wheres) {
    try {
      $users = $this->storage->get_prereg_users($user_wheres);
      if (0 == count($users))
        return new Null_Prereg_User();
      $user_dto = new $this->user_dto_class_name;
      $user_dto->deserialize($users[0]);
      return new Prereg_User($user_dto);
    }
    catch (Prereg_Storage_Error $e) {
      throw new Prereg_Error($e->getMessage());
    }
    return new Null_Prereg_User();
  }

}
