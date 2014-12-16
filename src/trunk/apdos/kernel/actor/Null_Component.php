<?php
namespace apdos\kernel\actor;

use apdos\kernel\actor\Component;

class Null_Component extends Component {
  public function is_null() {
    return true;
  }
}
