<?php
class Component {
  private $parent_actor;

  public function set_parent($actor) {
    $this->parent_actor = $actor;
  }

  public function get_parent() {
    return $this->parent_actor;
  }

  public function is_null() {
    return false;
  }
}

class Null_Component extends Component {
  public function is_null() {
    return true;
  }
}
