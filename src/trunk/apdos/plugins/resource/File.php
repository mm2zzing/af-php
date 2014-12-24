<?php
namespace apdos\plugins\resource;

use apdos\kernel\actor\Component;

class File extends Component {
  private $contents = '';

  public function __construct() {
  }

  public function load($file_path) {
    $this->contents = file_get_contents($file_path);
    //$this->contents = '{"register_gets":[{"uri":"/welcome/hellow", "controller_class":"app\\\\welcome\\\\controllers\\\\Hellow"}]}';
  }

  public function get_contents() {
    return $this->contents;
  }
}
