<?php
namespace apdos\examples\hellow_world\components;

use apdos\kernel\actor\Component;

class Hellow_World extends Component {
  public function index() {
    echo 'example hello world!';
  }
}
