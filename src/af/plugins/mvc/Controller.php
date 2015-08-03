<?php
namespace af\plugins\mvc;

use af\kernel\actor\Component;
use af\plugins\input\Input;

class Controller extends Component {
  private $accepter;

  public function __construct() {
  }

  public function index() {
  }

  /**
   * POST로 전송된 이벤트 객체를 처리한다.
   */
  public function dispatch() {
    $this->accepter = $this->get_parent()->add_component('af\kernel\actor\net\Actor_Accepter');
    $this->accepter->recv(Input::get_instance()->get('event'));
  }
}
