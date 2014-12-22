<?php
namespace apdos\plugins\mvc;

use apdos\kernel\actor\Component;

class Controller extends Component {
  public function index() {
  }

  /**
   * view 파일을 로딩한다.
   *
   * @param view_path string 뷰 템플릿 파일이 담긴 위치
   */
  protected function load_view($view_path, $data = array()) {
    $view =  $this->get_parent()->add_component('apdos\\plugins\\mvc\\View');
    $view->load_view($view_path, $data);
  }
}
