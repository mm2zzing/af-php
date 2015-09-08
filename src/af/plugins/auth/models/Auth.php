<?php
namespace af\plugins\auth\models;

use af\kernel\actor\component;
use af\plugins\auth\dto\User_DTO;
use af\plugins\auth\accessors\User;
use af\plugins\auth\accessors\Null_User;
use af\plugins\auth\errors\Auth_Error;
use af\plugins\auth\errors\Auth_Id_Is_None;
use af\plugins\auth\errors\Auth_Is_Unregistered;
use af\plugins\auth\errors\Auth_Password_Is_Wrong;
use af\plugins\auth\errors\Auth_Uuid_Is_None;
use aol\core\Object_Converter;

/**
 * @class Auth
 *
 * @brief 인증 처리 플러그인. Auth_Storage 확장을 통해  여러 형태의 DB에 회원 정보를 저장한다.
 * @author Lee Hyeon-gi
 */
class Auth extends Component {
  private $storage;
  private $user_dto_class_name;

  public function __construct() {
  }

  /**
   * 컴포넌트를 시작
   *
   * @param storage Auth_Storage 사용할 스토리지 인스턴스
   * @param user_dto_class_name String 사용할 User_DTO 클래스명
  */
  public function start($storage, $user_dto_class_name) {
    $this->storage = $storage;
    $this->user_dto_class_name = $user_dto_class_name;
  }

  /**
   * 일반 회원 가입. User_DTO를 상속받은 경우 추가정보는 update_user 메서드를
   * 통해 갱신한다.
   *
   * @return User 유저 객체
   */
  public function register($register_id, $register_password, $register_email) {
    $user = new $this->user_dto_class_name;
    $user->register_id = $register_id;
    $user->register_password = $register_password;
    $user->register_email = $register_email;
    $this->update_user_dto($user);
    $this->register_user($user);
    return $this->get_user(array('register_id'=>$register_id));
  }

  /**
   * 게스트 회원 가입
   *
   * @return User 유저 객체
   */
  public function register_guest() {
    $user = new $this->user_dto_class_name;
    $this->update_user_dto($user);
    $this->register_user($user);
    return $this->get_user(array('token'=>$user->token));
  }


  /**
   * 디바이스 기반 회원 가입
   *
   * @param device_id String 변경되지 않는 유니크한 디바이스 아이디
   * @return User 유저 객체 
   */
  public function register_device($device_id) {
    $user = new $this->user_dto_class_name;
    $user->external_ids['device_id'] = $device_id;
    $this->update_user_dto($user);
    $this->register_user($user);
    return $this->get_user(array('device_id'=>$device_id));
  }

  private function update_user_dto($user) {
    $user->id = $this->gen_uuid();
    if (isset($_SERVER['REMOTE_ADDR']))
      $user->install_ip = $_SERVER['REMOTE_ADDR'];
    else
      $user->install_ip = '<unknown system>';
    $user->install_date = date('Y-m-d H:i:s');
    $user->token = $this->gen_uuid();
  }

  private function register_user($user) {
    try {
      $array = Object_Converter::to_array($user);
      $this->storage->register($array);
    }
    catch (Auth_Storage_Error $e) {
      throw new Auth_Error($e->getMessage());
    }
  }

  /**
   */
  private function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                   // 32 bits for "time_low"
                   mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

                   // 16 bits for "time_mid"
                   mt_rand( 0, 0xffff ),

                   // 16 bits for "time_hi_and_version",
                   // four most significant bits holds version number 4
                   mt_rand( 0, 0x0fff ) | 0x4000,

                   // 16 bits, 8 bits for "clk_seq_hi_res",
                   // 8 bits for "clk_seq_low",
                   // two most significant bits holds zero and one for variant DCE1.1
                   mt_rand( 0, 0x3fff ) | 0x8000,

                   // 48 bits for "node"
                   mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
  }


  /**
   * 회원 탈퇴
   * 
   * @param user_token String 유저의 UUID
   */ 
  public function unregister($user_token) {
    try {
      $user = $this->get_user(array('token'=>$user_token));
      if ($user->is_null())
        throw new Auth_Uuid_Is_None('Uuis is none');
      $this->storage->unregister($user_token);
    }
    catch (Auth_Storage_Error $e) {
      throw new Auth_Error($e->getMessage());
    }
  }

  /**
   * 유저 정보 조회
   *
   * @param user_wheres array 찾으려는 유저 데이터 
   * @return User 유저 객체 
   */
  public function get_user($user_wheres) {
    try {
      $users = $this->storage->get_users($user_wheres);
      if (0 == count($users))
        return new Null_User();
      $user_dto = new $this->user_dto_class_name;
      $user_dto->deserialize($users[0]);
      return new User($user_dto);
    }
    catch (Auth_Storage_Error $e) {
      throw new Auth_Error($e->getMessage());
    }
    return new Null_User();
  }

  /**
   * 유저 로그인.
   *
   * @param register_id String 가입 아이디
   * @param register_password String 가입 패스워드 
   * @return User 유저 객체
   */
  public function login($register_id, $register_password) {
    try {
      $users = $this->storage->get_users(array('register_id'=>$register_id));
      if (0 == count($users))
        throw new Auth_Id_Is_None('user is null');
      $user_dto = new User_DTO();
      $user_dto->deserialize($users[0]);
      if (0 != strcmp($user_dto->register_password, $register_password))
        throw new Auth_Password_Is_Wrong('password is wrong');
      if (true == $user_dto->unregistered)
        throw new Auth_Is_Unregistered('unregisterd is true');
    }
    catch (Auth_Storage_Error $e) {
      throw new Auth_Error($e->getMessage());
    }
    return new User($users[0]);
  }

  /**
   * 유저 정보를 갱신
   *
   * @parma where array 갱신할 유저 정보
   * @param contents array 갱신할 유저 데이터
   */
  public function update_user($user_wheres, $contents) {
    try {
      $this->storage->update_user($user_wheres, $contents);
    }
    catch (Auth_Storage_Error $e) {
      throw new Auth_Erro($e->getMessage());
    }
  }
}
