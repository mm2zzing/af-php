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
   * POST로 전송된 이벤트를 처리한다.
   * 
   * @param string URL에 전달되어온 base64인코딩된 이벤트 객체
   */
  public function dispatch_post() {
    $this->accepter = $this->get_parent()->add_component('apdos\kernel\actor\Actor_Accepter');
    $this->accepter->recv(Input::get_instance()->get('event'));
  }

  /**
   * GET으로 전송된 이벤트를 처리한다.
   *
   */
  public function dispatch_get($event) {
    $this->accepter = $this->get_parent()->add_component('apdos\kernel\actor\Actor_Accepter');
    $this->accepter->recv($event);
  }
}
