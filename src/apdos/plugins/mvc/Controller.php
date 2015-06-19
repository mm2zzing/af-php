<?php
namespace apdos\plugins\mvc;

use apdos\kernel\actor\Component;
use apdos\plugins\input\Input;

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
    $this->accepter = $this->get_parent()->add_component('apdos\kernel\actor\Actor_Accepter');
    $this->accepter->recv(Input::get_instance()->get('event'));
  }
}
