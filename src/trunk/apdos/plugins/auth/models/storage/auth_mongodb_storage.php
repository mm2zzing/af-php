<?php
namespace apdos\plugins\auth\models\storage;

Loader::get_instance()->include_module('apdos/plugins/auth/models/storage/auth_storage');
Loader::get_instance()->include_module('apdos/plugins/auth/dto/user_dto');
Loader::get_instance()->include_module('apdos/plugins/db_connecters/mongodb/mongodb_connecter');
Loader::get_instance()->include_module('apdos/plugins/db_connecters/mongodb/write_concern');
Loader::get_instance()->include_module('apdos/kernel/core/object_converter');

class Auth_Mongodb_Storage extends Auth_Storage {
  private $connecter;

  public function __construct() {
  }

  public function start($host) {
    $this->connecter = $this->get_parent()->get_component('Mongodb_Connecter');
    if ($this->connecter->is_null())
      $this->connecter = $this->get_parent()->add_component('Mongodb_Connecter');
    $this->connecter->connect($host);
    $this->connecter->select_database('ft');
    $this->connecter->select_collection('users');
  }

  public function register($user) {
    $this->connecter->insert($user, new Acknowleged_Write());
  }

  /**
   * 분산처리를 위해 DB에서 생성한 ObjectID의 string 값을 user id로 사용한다.
   *
   * @param wheres array 검색 조건
   * @return array 검색된 유저들
   */
  public function get_users($wheres) {
    $users = $this->connecter->where($wheres)->find();
    foreach ($users as &$user) {
      $user['id'] =  $user['_id']->{'$id'};
      unset($user['_id']);
    }
    return $users;
  }

  public function update_user($wheres, $contents) {
    foreach ($contents as $key=>$value) {
      $this->connecter->where($wheres)->set(array($key=>$value));
    }
  }

  public function unregister($user_uuid) {
    $this->connecter->where(array('uuid'=>$user_uuid))->set(array('unregistered'=>true));
  }
}
