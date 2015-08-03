<?php
namespace af\kernel\actor;

use af\kernel\actor\Component;

class Null_Component extends Component {
  public function is_null() {
    return true;
  }
}
