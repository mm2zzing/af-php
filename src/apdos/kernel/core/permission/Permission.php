<?php
namespace apdos\kernel\core\permission;

use apdos\kernel\core\Object;
use apdos\kernel\core\permission\enums\Access_Type;
use apdos\kernel\core\permission\enums\Access_User_Type;

class Permission extends Object {  
  public function __construct($args) {
    $this->accesses[Access_User_Type::OWNER] = Access_Type::FLAG_NONE;
    $this->accesses[Access_User_Type::GROUP] = Access_Type::FLAG_NONE;
    $this->accesses[Access_User_Type::OTHER] = Access_Type::FLAG_NONE;

    parent::__construct($args, array('', '', 'construct3'));
  }

  public function construct3($owner_flag, $group_flag, $other_flag) {
    $this->set_owner_permission($owner_flag);
    $this->set_group_permission($group_flag);
    $this->set_other_permission($other_flag);
  }

  public function set_owner_permission($flag) {
    $this->accesses[Access_User_Type::OWNER] = $flag;
  }

  public function set_group_permission($flag) {
    $this->accesses[Access_User_Type::GROUP] = $flag;
  }

  public function set_other_permission($flag) {
    $this->accesses[Access_User_Type::OTHER] = $flag;
  }

  public function possible_read($access_user_type) {
    return $this->accesses[$access_user_type] & Access_Type::FLAG_READ;
  }

  public function possible_write($access_user_type) {
    return $this->accesses[$access_user_type] & Access_Type::FLAG_WRITE;
  } 

  public function possible_execute($access_user_type) {
    return $this->accesses[$access_user_type] & Access_Type::FLAG_EXECUTE;
  }

  public function to_string() {
    $result = '';
    $user_types = array(Access_User_Type::OWNER, 
                        Access_User_Type::GROUP,
                        Access_User_Type::OTHER);
    foreach ($user_types as $user_type) {
      if ($this->possible_read($user_type))
        $result .= 'r';
      else
        $result .= '-';
      if ($this->possible_write($user_type))
        $result .= 'w';
      else
        $result .= '-';
      if ($this->possible_execute($user_type))
        $result .= 'x';
      else
        $result .= '-';

    }
    return $result;
  }

  private $accesses;
}

