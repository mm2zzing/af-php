<?php
namespace apdos\plugins\mvc;

use apdos\kernel\actor\Component;
use apdos\kernel\core\errors\Core_Error;
use apdos\kernel\log\Logger;
use apdos\plugins\config\Config;

class View extends Component {
  public function load_view($view_path, $data = array()) {
    foreach ($data as $key=>$value) {
      $$key = $value;
    }
    $application_path = Config::get_instance()->get_application_path();
    include_once($application_path . '/' . $view_path . '.php');
  }
}
