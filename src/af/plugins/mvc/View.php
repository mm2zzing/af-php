<?php
namespace af\plugins\mvc;

use af\kernel\actor\Component;
use af\kernel\core\errors\Core_Error;
use af\kernel\log\Logger;
use af\plugins\config\Config;

class View extends Component {
  public function load_view($view_path, $data = array()) {
    foreach ($data as $key=>$value) {
      $$key = $value;
    }
    $application_path = Config::get_instance()->get_application_path();
    include_once($application_path . '/' . $view_path . '.php');
  }
}
