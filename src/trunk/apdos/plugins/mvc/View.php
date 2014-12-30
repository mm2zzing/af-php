<?php
namespace apdos\plugins\mvc;

use apdos\kernel\actor\Component;

class View extends Component {
  public function load_view($view_path, $data = array()) {
    foreach ($data as $key=>$value) {
      $$key = $value;
    }
    require_once($view_path . '.php');
  }
}
