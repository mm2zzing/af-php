<?php
namespace apdos\plugins\mvc;

use apdos\kernel\actor\Component;
use apdos\kernel\core\errors\Core_Error;
use apdos\kernel\log\Logger;

class View extends Component {
  public function load_view($view_path, $data = array()) {
    foreach ($data as $key=>$value) {
      $$key = $value;
    }
    include_once($view_path . '.php');
  }
}
