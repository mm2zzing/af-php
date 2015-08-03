<?php
namespace af\plugins\auth\presenters;

use af\plugins\auth\presenters\events\req_register_device;
use af\plugins\auth\presenters\events\res_register_device;
use af\plugins\auth\presenters\events\req_register_guest;
use af\plugins\auth\presenters\events\res_register_guest;
use af\plugins\auth\presenters\events\req_register;
use af\plugins\auth\presenters\events\res_register;
use af\plugins\auth\presenters\events\req_unregister;
use af\plugins\auth\presenters\events\res_unregister;
use af\plugins\auth\presenters\events\req_login;
use af\plugins\auth\presenters\events\res_login;
use af\plugins\auth\presenters\events\req_get_user;
use af\plugins\auth\presenters\events\res_get_user;

/**
 * @class Auth_Controller
 *
 * @brief 인증 처리를 하는 컨트롤러
 */
class Auth_Controller extends Component {
  private $auth_model;
  private $actor;

  public function __construct() {
  }

  /**
   * 컨트롤로 시작
   */
  public function start($auth_model) {
    $this->auth_model = $auth_model;
    $this->add_register_device_listener();
    $this->add_register_guest_listener();
    $this->add_register_listener();
    $this->add_unregister_listener();
    $this->add_login_listener();
    $this->add_get_user_listener();
  }

  private function add_register_device_listener() {
    $auth_model = $this->auth_model;
    $listener = function ($event) use(&$auth_model) { 
      $user = $auth_model->register_device($event->get_device_id());
      $dto = $user->get_user_dto();

      $res_event = new Res_Register_Device();
      $res_event->init(Object_Converter::to_array($dto));
      $event->get_remote_actor()->send($res_event);
    };
    $this->get_parent()->add_event_listener(Req_Register_Device::$REQ_REGISTER_DEVICE, $listener);
  }

  private function add_register_guest_listener() { 
    $auth_model = $this->auth_model;
    $listener = function ($event) use(&$auth_model) { 
      $user = $auth_model->register_guest();
      $dto = $user->get_user_dto();

      $res_event = new Res_Register_Guest();
      $res_event->init(Object_Converter::to_array($dto));
      $event->get_remote_actor()->send($res_event);
    };
    $this->get_parent()->add_event_listener(Req_Register_Guest::$REQ_REGISTER_GUEST, $listener);
  }

  private function add_register_listener() { 
    $auth_model = $this->auth_model;
    $listener = function ($event) use(&$auth_model) { 
      $user = $auth_model->register($event->get_register_id(), 
                                          $event->get_register_password(), 
                                          $event->get_register_password());
      $dto = $user->get_user_dto();

      $res_event = new Res_Register();
      $res_event->init(Object_Converter::to_array($dto));
      $event->get_remote_actor()->send($res_event);
    };
    $this->get_parent()->add_event_listener(Req_Register::$REQ_REGISTER, $listener);
  }

  private function add_unregister_listener() { 
    $auth_model = $this->auth_model;
    $listener = function ($event) use(&$auth_model) { 
      $user = $auth_model->unregister($event->get_uuid());

      $res_event = new Res_Unregister();
      $event->get_remote_actor()->send($res_event);
    };
    $this->get_parent()->add_event_listener(Req_Unregister::$REQ_UNREGISTER, $listener);
  }

  private function add_login_listener() {
    $auth_model = $this->auth_model;
    $listener = function ($event) use(&$auth_model) { 
      $user = $auth_model->login($event->get_register_id(), $event->get_register_password());
      $dto = $user->get_user_dto();

      $res_event = new Res_Register_Guest();
      $res_event->init(Object_Converter::to_array($dto));
      $event->get_remote_actor()->send($res_event);
    };
    $this->get_parent()->add_event_listener(Req_Login::$REQ_LOGIN, $listener);
  }

  private function add_get_user_listener() {
    $auth_model = $this->auth_model;
    $listener = function ($event) use(&$auth_model) { 
      $user = $auth_model->get_user(array('device_id'=>$event->get_device_id()));
      $dto = $user->get_user_dto();

      $res_event = new Res_Get_User();
      if ($dto->is_null())
        $res_event->init(Res_Get_User::$RESULT_USER_IS_NOT_EXIST, Object_Converter::to_array($dto));
      else
        $res_event->init(Res_Get_User::$RESULT_USER_IS_EXIST, Object_Converter::to_array($dto));
      $event->get_remote_actor()->send($res_event);
    };
    $this->get_parent()->add_event_listener(Req_Get_User::$REQ_GET_USER, $listener);
  }
}
