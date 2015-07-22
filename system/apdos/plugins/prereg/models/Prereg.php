<?php
namespace apdos\plugins\prereg\models;

use apdos\kernel\actor\component;
use apdos\plugins\prereg\dto\Prereg_User_DTO;
use apdos\plugins\prereg\accessors\Prereg_User;
use apdos\plugins\prereg\errors\Prereg_Error;
use apdos\kernel\core\Object_Converter;

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
    $this->update_prereg_user_dto($user);
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
    $this->update_prereg_user_dto($user);
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

  private function update_prereg_user_dto($user) {
    if (isset($_SERVER['REMOTE_ADDR']))
      $user->prereg_ip = $_SERVER['REMOTE_ADDR'];
    else
      $user->prereg_ip = '<unknown system>';
    $user->prereg_date = date('Y-m-d H:i:s');
  }

}
